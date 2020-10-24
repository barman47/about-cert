<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DocumentRepository;
use App\Exceptions\DocumentException;

use App\Jobs\DeleteTemporaryCV;


use App\Document;

use App\DocumentType;
use App\User;
use Str;
use Storage;
use File;
use URL;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html as HTMLParser;

use App\SignatureGateway\HelloSignGateway;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        return response()->json([
                "documents" => $user->rootDocuments()->get()
            ]);
    }//end method index

    public function folder(Document $document){
        if($document->type != DocumentType::FOLDER)
            return response()->json("Not a folder", 403);

        return response()->json([
            "documents" => $document->documents,
        ]);
    }//end method folder

    public function getBreadCrumb(Document $document){

        $breadCrumb[] = [
            "id" => $document->id,
            "text" => $document->name
        ];

        $nextDocument = $document;

        do{
            if($nextDocument->documentable_type != DocumentType::ROOT){
                $temp = $nextDocument->documentable;

                if($temp == null){
                    break;
                }

                if($temp->type == DocumentType::FOLDER){
                    $nextDocument = $temp;
                }else{
                    break;
                }
                $breadCrumb[] = [
                    "text" => $nextDocument->name,
                    "id" => $nextDocument->id
                ];
            }else{
                break;
            }
        }while(true);

        return response()->json(array_reverse($breadCrumb));
    }//end method getBreadCrumb

    public function shareDocument(Request $request){
        $request->validate([
            "document_id" => "required|string",
            "receiver_id" => "required|string",
            "folder_id" => "string"
        ]);


        $document = Document::find($request->document_id);
        $user = auth()->user();
        $receiver = User::find($request->receiver_id);

        $documentRepository =  new DocumentRepository();

        $documentRepository->user($user);

        $intoFolder = $request->has("folder_id") ? Document::find($request->folder_id) : null;

        try{
            if(!$documentRepository->share($receiver, $document, $intoFolder))
                return response(["message" => "An error occured while sending document"]);
        }catch(DocumentException $e){
            return response(["message" => "An error occured while sending document"]);
        }

        return response()->json(["message" => "Document shared to user"], 201);
    }//end method shareDocument

    public function getReceivedSubFolders(){
        $request = request();

        $request->validate([
            "folder_id" => "string",
            "user_id" => "required|string"
        ]);

        $user = User::findOrFail($request->user_id);
        $document = Document::find($request->folder_id);

        $documentRepository = new DocumentRepository();

        $subFolders = $documentRepository->user($user)->getReceivedSubFolders($document);

        return response()->json([
            "sub_folders" => $subFolders
        ]);
    }//end method getSubFolders

    public function getSubFolders(){
        $request = request();

        $request->validate([
            "folder_id" => "string"
        ]);

        $user = auth()->user();
        $document = !$request->folder_id || $request->folder_id == DocumentType::ROOT ? DocumentType::ROOT : Document::find($request->folder_id);

        $documentRepository = new DocumentRepository();
        $subFolders = $documentRepository->user($user)->getSubFolders($document);

        return response()->json([
            "sub_folders" => $subFolders
        ]);
    }//end method getSubFolders

    public function copyDocumentToFolder(Request $request){
        $request->validate([
            "document_id" => "required|string",
            "folder_id"     => "required|string"
        ]);

        $document = Document::find($request->document_id);
        $user = auth()->user();
        $folder = $request->folder_id == DocumentType::ROOT ? DocumentType::ROOT : Document::find($request->folder_id);

        $documentRepository = new DocumentRepository();
        $documentRepository->user($user)->document($document);

        $newDocument = $documentRepository->copyDocumentToFolder($folder);

        return response()->json(["document" => $newDocument], 200);
    }//end method copyDocumentToFolder

    public function moveDocumentToFolder(Request $request){
        $request->validate([
            "document_id" => "required|string",
            "folder_id"     => "required|string"
        ]);

        $document = Document::find($request->document_id);
        $user = auth()->user();
        $folder = $request->folder_id == DocumentType::ROOT ? DocumentType::ROOT : Document::find($request->folder_id);

        $documentRepository = new DocumentRepository();
        $documentRepository->user($user)->document($document);

        $newDocument = $documentRepository->moveDocumentToFolder($folder);

        return response()->json(["document" => $newDocument], 200);
    }//end method copyDocumentToFolder

    public function search(){
        $request = request();
        $request->validate([
            "query" => "required|string",
            "per_page" => "integer",
            "0" => "integer"
        ]);

        $user = auth()->user();
        $documentRepository = new DocumentRepository();

        $documentsPaginationData = $documentRepository
                ->user($user)
                ->search($request->input("query"), $request->per_page ?? 10, $request->input("0") ?? 1);

        return response()->json($documentsPaginationData, 200);
    }//end method search

    public function getSignUrl(){
        return (new DocumentRepository())->getSignUrl(new HelloSignGateway());
    }//end method sign

    public function getSignedDocuments(){
        return (new DocumentRepository())->getSignedDocuments(new HelloSignGateway());
    }//end emtjod getSignedDocuments

    public function deleteDocument(Request $request){
        $request->validate([
            "document_id" => "required|string"
        ]);

        $user = auth()->user();
        $document = Document::find($request->document_id);

        $documentRepository = new DocumentRepository();

        try{
            $documentRepository->user($user)->document($document)->softDelete();
        }catch(DocumentException $e){
            return response()->json(["message" => "Error finding user"], 401);
        }

        return response()->json(["message" => "OK"], 200);
    }//end method deleteDocument

    public function permanentlyDeleteDocument(Request $request){
        $request->validate([
            "document_id" => "required|string"
        ]);

        $user = auth()->user();
        $document = Document::find($request->document_id);

        $documentRepository = new DocumentRepository();

        if(!$documentRepository->user($user)->document($document)->deleteDocument())
            return response()->json(["message" => "An error occured while deleting the document"], 401);

        return response()->json(["message" => "OK"], 200);
    }//end method permanentlyDeleteDocument

    public function emptyTrash(Request $request, DocumentRepository $documentRepository){
        $user = auth()->user();

        return response()->json(["message" => $documentRepository->user($user)->emptyTrash()], 200);
    }//end method emptyTrash

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     //
    // }//end method create

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"      => "required|string",
            "type"      => "required|string",
            "file"      => "file|max:" . 1024 * 50,
            "folder_id" => "string"
        ]);

        $user = auth()->user();

        if(!in_array($request->type, ["file", "folder"])){
            return response()->json([
                        "message" => "Invalid type value",
                        "errors"    => [
                            "type"  => [
                                "type field can only be folder or file"
                            ]
                        ]
                    ], 422);
        }

        if($request->type == "file" && !$request->has("file")){
            return response()->json([
                        "message" => "File field is required",
                        "errors"    => [
                            "file"  => [
                                "The file field is required"
                            ]
                        ]
                    ], 422);
        }

        $originalExtension = null;
        $file = null;

        if($request->type == "file"){
            $file = $request->file("file");
            $originalFileName =  $file->getClientOriginalName();
            $originalExtension = pathinfo($originalFileName)["extension"] ?? '';
        }

        $documentRepository = new DocumentRepository();

        $document = $documentRepository->user($user)
                            ->create($request->name, $originalExtension, $request->type, $file, $request->folder_id ?? null);

        $document->refresh();

        return response()->json($document, 201);
    }//end method store

    public function getSignatureFolder(){
        try{
            $document = (new DocumentRepository)->user(auth()->user())->getSignatureFolder();
        }catch(DocumentException $e){
            return response()->json(["document" => (object) [] ]);
        }

        return response()->json(["document" => $document]);
    }//end method getSignatureFolder

    public function downloadGeneratedDocument(Request $request, Document $document){
        // TODO: remove comment
        // if (! $request->hasValidSignature()) {
        //     abort(401);
        // }
        
        return response()->download(storage_path() . "/app/" . $document->src, $document->name . ($document->extension ? "." . $document->extension : ''));
        // return response()->file(storage_path() . "/app/" . $document->src);
    }//end method

    public function viewDocument(){
        $request = request();

        $request->validate([
            "document_id" => "required|string"
        ]);

        $document = Document::findOrFail($request->document_id);

        $file_url = \URL::temporarySignedRoute(
            'tempGetDocument', now()->addMinutes(15), ['document' => $document->id]
        );

        // // TODO: Use the $file_url as is. comment next line 
        // $file_url =  str_replace("http://192.168.137.1/aboutcert/public", "https://2729ce0a.ngrok.io/aboutcert/public", $file_url);
        // $file_url =  str_replace("http://192.168.43.235/aboutcert/public", "https://2729ce0a.ngrok.io/aboutcert/public", $file_url);
        // $file_url =  str_replace("http://localhost", "https://2729ce0a.ngrok.io/aboutcert/public", $file_url);

        return response()->json([
                "document" => $document,
                "file_url" => $file_url
            ]);
    }//end method viewDocument

    public function getDownloadLink(Document $document){
        $user = auth()->user();
        
        if($document->user_id != $user->id)
            return response()->json(["messages" => "Forbidden"], 403);
        
        $rv = [];
        $rv["download_url"] = URL::temporarySignedRoute(
            'tempDocumentDownload', now()->addMinutes(30), ['document' => $document->id]
        );

        return response()->json($rv);
    }

    public function getFile(Request $request, Document $document){
        // TODO: remove comment
        // if (! $request->hasValidSignature()) {
        //     abort(401);
        // }

        return response()->file(storage_path("app/{$document->src}"));
    }//end method getViedFile

    public function restoreSingleDocument(Request $request, DocumentRepository $documentRepository){
        $request->validate([
            "document_id" => "required|string"
        ]);

        $user = auth()->user();
        try{
            $document = Document::findOrFail($request->document_id);
        }catch(\Exception $e){
            return response()->json(["message" => "Invalid Document Id"], 400);
        }
        
        try{
            $documentRepository->user($user)->restoreSingleDocument($document);
        }catch(DocumentException $e){
            return response()->json(["message" => $e->getMessage()], 400);
        }
        

        return response()->json(["message" => "Document retored."], 201);
    }//end method restoreSingleDocument

    public function restoreAll(Request $request, DocumentRepository $documentRepository){
        $user = auth()->user();
        return response()->json(["message" => $documentRepository->user($user)->restoreAll()], 201);
    }//end method restoreAll

    public function forgeDocument(Request $request){
        $request->validate([
            "html" => "required|string",
            "type" => "required|string",
            "name" => "required|string",
            "download" => "string"
        ]);

        if(!in_array($request->type, ['pdf', 'doc', 'html']))
            return response()->json("The type {$request->type} is not supported", 400);

        $user = auth()->user();

        $html = <<<EOD
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8"/>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
                    <title>Document</title>
                </head>
                <body>
                    {$request->html}
                </body>
                </html>
EOD;



        $pw = new PhpWord();

        $section = $pw->addSection();
        HTMLParser::addHtml($section, $html, true);

        Document::setUpStyles($pw);

        $path = "documents/main/";
        $savePath = storage_path() . "/app/" . $path;
        $name = null;

        if($request->type == "pdf"){
            do{
                $name = Str::random(30) . ".pdf";

                if(Document::where("src", $path . $name)->first() == null)
                    break;
            }while(true);

            $pw->save($name, "PDF");
        }elseif($request->type == "doc"){
            do{
                $name = Str::random(30) . ".docx";

                if(Document::where("src", $path . $name)->first() == null)
                    break;
            }while(true);

            $pw->save( $name, "Word2007");
        }elseif($request->type == "html"){
            do{
                $name = Str::random(30) . ".html";

                if(Document::where("src", $path . $name)->first() == null)
                    break;
            }while(true);

            $pw->save($name, "HTML");
        }

        File::move(base_path() . "/public/$name", $savePath . $name);

        $document = $user->documents()->create([
            "src" => $path . $name,
            "name" => $this->getFileName($user, null, $request->name ?? "Document"),
            "documentable_type" => DocumentType::ROOT,
            "documentable_id" => DocumentType::ROOT
        ]);

        $document = $document->refresh();

        $rv = [
            "document"  => $document
        ];

        if($request->has("download") && $request->download == "true"){
            $rv["download_url"] = URL::temporarySignedRoute(
                                        'tempDocumentDownload', now()->addMinutes(30), ['document' => $document->id]
                                    );
            // $rv["mime_type"] = Storage::mimeType($document->src);
        }

        /* [SAVE FILE ON THE SERVER] */
        // $pw->save("html-to-doc.pdf", "PDF");
        // $pw->save("html-to-doc.html", "HTML");

        // $pw->save("html-to-doc.docx", "Word2007");
        // // /* [OR FORCE DOWNLOAD] */
        // header('Content-Type: application/octet-stream');
        // header('Content-Disposition: attachment;filename="convert.docx"');
        // $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($pw, 'Word2007');
        // $objWriter->save('php://output');

        return response()->json($rv);
    }//end method forgeDocument

    public function getFileName($user, $folder, $name){
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

    

    /**
     * Display the specified resource.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        //
    }
}
