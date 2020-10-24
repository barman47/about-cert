<?php
namespace App\Repositories;

use App\Exceptions\DocumentException;
use App\Jobs\DeleteDocumentPermanently;

use App\Repositories\AlertRepository;

use App\User;
use App\Document;
use App\DocumentType;
use App\Handlers\Alerts\DocumentSharedHandler;

use App\SignatureGateway\SignatureGatewayAbstractClass;
use Str;
use Storage;
use DB;

class DocumentRepository{
    private $user;
    private $document;
    const RECEIVED_FOLDER_NAME = "Received";
    const SIGNED_FOLDER_NAME = "Signed Documents";

    public function __construct(User $user = null , Document $document = null){
        $this->user = $user;
        $this->document = $document;
    }//end constructor

    public function user(User $user) : DocumentRepository{
        $this->user = $user;
        return $this;
    }//end method user

    public function document(Document $document): DocumentRepository{
        $this->document = $document;
        return $this;
    }//end method document

    public function share(User $user, Document $document = null, Document $folder = null): bool{
        if($this->user == null){
            throw new DocumentException("The sharing user cannot be undefined");
        }

        if($user == null){
            throw new DocumentException("The receiving user cannot be undefined");
        }

        if($user->id == $this->user->id)
            throw new DocumentException("Cannot send file to self");

        $document = $document ?? $this->document;

        if($document == null){
            throw new DocumentException("Document must be specified");
        }

        $newDocument = null;

        if($document->type == DocumentType::FOLDER){
            $newDocument = $this->shareFolder($user, $document, $folder);
        }else{
            $newDocument = $this->shareDocument($user, $document, $folder);
        }

        $alertRepository = new AlertRepository();

        $alertRepository
            ->sender($this->user)
            ->receiver($user)
            ->data($newDocument)
            ->handler(new DocumentSharedHandler())
            ->create();

        return true;
    }//end method shareDocument

    public function softDelete(Document $document = null): bool{
        $document = $document ?? $this->document;

        if($this->user == null || $document == null)
            throw new DocumentException("The user and the document must be specified");

        if($this->user->documents()->where("documents.id", $document->id)->first() == null)
            return false;



        $document->deleted = 1;
        $document->save();

        DeleteDocumentPermanently::dispatch($this->user, $document)->delay(now()->addDays(Document::SOFT_DELETE_PERIOD_IN_DAYS));

        return true;
    }//end method softDelete

    public function deleteDocument(Document $document = null): bool{
        $document = $document ?? $this->document;

        if($this->user == null || $document == null)
            throw new DocumentException("The user and the document must be specified");

        if($this->user->documents()->where("documents.id", $document->id)->first() == null)
            return false;

        try{
            $src =  $document->src;
            $document->delete();
            try{
                if($src)
                    unlink(storage_path("app/" . $src));
            }catch(\Exception $e){}
        }catch(\Exception $e){
            return false;
        }
        return true;
    }//end emthod deleteDocument

    public function emptyTrash(){
        if(!$this->user)
            throw new DocumentException("The user must be specified");

        $documents = Document::where([
            ["user_id", $this->user->id],
            ["deleted", 1]
        ])->select("id", "src")->get();

        $count = $documents->count();

        foreach($documents as $document){
            $this->deleteDocument($document);
        }

        return "Deleted $count " . ($count == 1 ? "document/folder" : "documents/folders");
    }//end method emptyTrash

    public function create($fileName, $fileExtension, $documentType, $file = null, $folderId = null): Document {
        $user = $this->user;

        $folder = null;

        if($folderId){
            $folder = Document::where([
                ["type", DocumentType::FOLDER],
                ["id", $folderId]
            ])->with("documents")->select("id")->first();
        }

        $fileName = $this->getFileName($user, $folder, $fileName);

        $document = null;

        if($documentType == "folder"){
            $document = $user->documents()->create([
                "name" => $fileName,
                "documentable_type" => $folder != null ? Document::class : DocumentType::ROOT,
                "documentable_id"   => $folder != null ? $folder->id : DocumentType::ROOT,
                "type" => DocumentType::FOLDER
            ]);
        } // if new document type is folder
        else{
            $path = Storage::putFileAs("documents/main", $file, Str::random(30) . ( $fileExtension ? ".$fileExtension" : ''));

            $document = $user->documents()->create([
                "name" => $fileName,
                "documentable_type" => $folder != null ? Document::class : DocumentType::ROOT,
                "documentable_id"   => $folder != null ? $folder->id : DocumentType::ROOT,
                "src" => $path,
            ]);
        }

        return $document;
    }//end method create


    /**
     * Private methods
     */

    private function shareFolder(User $receiver, Document $folder, Document $childFolder = null){
        // Recursively send document
    }//end method shareFolder

    private function shareDocument(User $receiver, Document $document, Document $childFolder = null)
    {
        //TODO: create the logic for the sending of the documents
        if($this->user == null){
            throw new DocumentException("The sharing user cannot be undefined");
        }

        $user = $this->user;

        if(!$this->validateReceivedFolderExistence($receiver)){
            throw new DocumentException("An error occured while creating the received folder");
        }//end method

        $receivedFolder = $receiver->documents()->where("documents.name", static::RECEIVED_FOLDER_NAME)->first();

        $pathinfo = \pathinfo($document->src);
        $extension = $pathinfo["extension"] ?? "";
        $path = $pathinfo["dirname"] . "/" . Str::random(30) . ($extension ? ".". $extension : "");

        try{
            copy(storage_path() . "/app/" . $document->src, storage_path() . "/app/" .  $path);
        }catch(\Exception $e){
            throw new DocumentException("An error occured while copying document");
        }

        $newDocument = new Document();

        $newDocument->name = $this->getfileName($receiver, $receivedFolder, $document->name);
        $newDocument->documentable_type = Document::class;

        if($childFolder && ($this->isFolderInFolder($childFolder, $receivedFolder))){
            $newDocument->documentable_id = $childFolder->id;
        }else{
            $newDocument->documentable_id = $receivedFolder->id;
        }

        $newDocument->type = $document->type;
        $newDocument->src = $path;
        $newDocument->size = $document->size;

        $receiver->documents()->save($newDocument);

        $newDocument->refresh();

        return $newDocument;
    }//end method shareDocument

    public function getSignatureFolder(): Document{
        $document = Document::where([
                        ["user_id", $this->user->id],
                        ["name", static::SIGNED_FOLDER_NAME]
                    ])->first();

        if($document == null)
            throw new DocumentException("The signed Document folder does not exist");

        return $document;
    }//end method getSignatureFolder

    public function getSubFolders($document = null, $user = null){

        $document = $document ?? $this->document;
        $user = $user ?? $this->user;

        if($user == null || $document == null)
            throw new DocumentException("The user and the document must be defined");

        if($document != DocumentType::ROOT){
            if($document->type != DocumentType::FOLDER)
                throw new DocumentException("The document passed is not a folder");

            if($document->user_id != $user->id)
                return new DocumentException("The current user is not the owner of this document");
        }

        return Document::where([
                ["documentable_id", $document == DocumentType::ROOT ? DocumentType::ROOT : $document->id],
                ["documentable_type", $document == DocumentType::ROOT ? DocumentType::ROOT : Document::class],
                ["user_id", $user->id],
                ["type", DocumentType::FOLDER]
            ])->orderBy("name")->select("id", "name")->get();
    }//end method getSubFolders

    public function getReceivedSubFolders($document = null){

        $user = $this->user;

        if($user == null)
            throw new DocumentException("The user must be defined");


        if(!$document){
            if(!$this->validateReceivedFolderExistence())
                throw new DocumentException("An error occured");
            $document = $user->documents()->where("documents.name", static::RECEIVED_FOLDER_NAME)->first();
        }else{
            $receivedFolder = $user->documents()->where("documents.name", static::RECEIVED_FOLDER_NAME)->first();

            if(!($receivedFolder->name == $document->name) && !$this->isFolderInFolder($document, $receivedFolder)){
                throw new DocumentException("Does not exist in current directory");
            }
        }

        return $this->getSubFolders( $document, $user);
    }//end method getReceivedSubFolders

    public function copyDocumentToFolder($folder) : Document{
        $user = $this->user;
        $document = $this->document;

        if($user == null || $document == null){
            throw new DocumentException("The user and the document must be defined");
        }

        if($user->id != $document->user_id)
            throw new DocumentException("This document does not belong to the user");

        if(($folder != DocumentType::ROOT) && ($folder->user_id != $user->id))
            throw new DocumentException("This folder does not belong to the user");

        $pathinfo = \pathinfo($document->src);
        $path = $pathinfo["dirname"] . "/" . Str::random(30) . "." . $pathinfo["extension"];

        try{
            copy(storage_path() . "/app/" . $document->src, storage_path() . "/app/" .  $path);
        }catch(\Exception $e){
            throw new DocumentException("An error occured while copying document");
        }

        $newDocument = new Document();

        $newDocument->name = $this->getfileName($user, $folder == DocumentType::ROOT ? null : $folder, $document->name);
        $newDocument->documentable_type = $folder == DocumentType::ROOT ? DocumentType::ROOT : Document::class;

        $newDocument->documentable_id = $folder == DocumentType::ROOT ? DocumentType::ROOT : $folder->id;

        $newDocument->type = $document->type;
        $newDocument->src = $path;
        $newDocument->size = $document->size;

        $user->documents()->save($newDocument);

        $newDocument->refresh();
        return $newDocument;
    }//end method copyDocumentToFolder

    public function moveDocumentToFolder($folder) : Document{
        $user = $this->user;
        $document = $this->document;

        if($user == null || $document == null){
            throw new DocumentException("The user and the document must be defined");
        }

        if($user->id != $document->user_id)
            throw new DocumentException("This document does not belong to the user");

        if(($folder != DocumentType::ROOT) && ($folder->user_id != $user->id))
            throw new DocumentException("This folder does not belong to the user");

        $document->documentable_id = $folder == DocumentType::ROOT ? DocumentType::ROOT : $folder->id;
        $document->documentable_type = $folder == DocumentType::ROOT ? DocumentType::ROOT : Document::class;

        $document->save();

        $document->refresh();

        return $document;
    }//end method copyDocumentToFolder

    public function search(string $seed, int $perpage = 10, int $page = 1, $deleted = 0){
        if($this->user == null)
            throw new DocumentException("The user must be specified");

        $documents =  Document::search($seed)
                    ->where("user_id", $this->user->id)
                    // ->where("deleted", $deleted)
                    ->paginate($perpage, $page);

        return $documents;
    }//return method search

    public function getSignUrl(SignatureGatewayAbstractClass $signatureGateway){
        return $signatureGateway->getSignUrl();
        // return $this;
    }//end method sign

    public function getSignedDocuments(SignatureGatewayAbstractClass $signatureGateway){
        return $signatureGateway->getFiles();
    }//end method getSignedDocuments

    public function validateReceivedFolderExistence($user = null): bool{
        $user = $user ?? $this->user;

        try{
            if($user->documents()->where("documents.name", static::RECEIVED_FOLDER_NAME)->first() == null){
                $user->documents()->create([
                    "name" => static::RECEIVED_FOLDER_NAME,
                    "documentable_type" => DocumentType::ROOT,
                    "documentable_id"   => DocumentType::ROOT,
                    "type" => DocumentType::FOLDER
                ]);
            }
        }catch(\Exception $e){
            return false;
        }

        return true;
    }//end method validateReceivedFolderExistence

    public function validateSignedFolderExistence($user = null): bool{
        $user = $user ?? $this->user;

        try{
            if($user->documents()->where("documents.name", static::SIGNED_FOLDER_NAME)->first() == null){
                $user->documents()->create([
                    "name" => static::SIGNED_FOLDER_NAME,
                    "documentable_type" => DocumentType::ROOT,
                    "documentable_id"   => DocumentType::ROOT,
                    "type" => DocumentType::FOLDER
                ]);
            }
        }catch(\Exception $e){
            return false;
        }

        return true;
    }//end method validateSignedFolderExistence


    public function restoreSingleDocument(Document $document){
        if($document->user_id != $this->user->id)
            throw new DocumentException("Document does not belong to this user");

        if($document->deleted != 0){
            $document->deleted = 0;
            $document->save();
        }
    }//end method restoreSingleDocument

    public function restoreAll(){
        if(!$this->user)
            throw DocumentException("The user must be specified");

        $affected = DB::update('update documents set deleted = 0 where user_id = ? and deleted = 1', [$this->user->id]);

        return "Restored $affected " . ($affected == 1 ? "document/folder" : "documents/folders");
    }//end method restoreAll

    private function getFileName($user, $folder, $name){
        $count = 0;

        while(true){
            $found = false;

            if($folder != null){
                foreach($folder->documents as $doc){
                    if($doc->name == $name .($count == 0 ? "" : "($count)")){
                        $found = true;
                    }
                }
            }else{
                foreach($user->rootDocuments as $doc){
                    if($doc->name == $name . ($count == 0 ? "" : "($count)")){
                        $found = true;
                    }
                }
            }

            if(!$found){
                return $name . ($count == 0 ? "" : "($count)");
            }

            $count = $count + 1;
        }
    }//end method getFileName

    private function isFolderInFolder(Document $child, Document $parent): bool{
        if($child == null)
            throw new DocumentException("The child object must be specified");

        if($parent->type != DocumentType::FOLDER)
            throw new DocumentException("The parent object must have a type of folder");

        $tParent = $child;

        do{
            if($child->documentable_type == DocumentType::ROOT)
                break;
            $tParent = $tParent->documentable;

            if($tParent->id == $parent->id)
                return true;
        }while(true);


        return false;
    }//end method isFolderInReceived
}//end class DocumentRepository
