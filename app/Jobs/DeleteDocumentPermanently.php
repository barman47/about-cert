<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Document;
use App\User;
use App\Repositories\DocumentRepository;

use Carbon\Carbon;

class DeleteDocumentPermanently implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $document;
    private $user;
    private $now;

    public function __construct(User $user, Document $document, $now = false)
    {
        $this->document = $document;
        $this->user = $user;
        $this->now = $now;
    }//end constructor

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo "Checking for the validity of document removal\n";

        $document = $this->documents;
        $user = $this->user;

        if(!$this->now){
            if($document->deleted == 0){
                echo "Skipping document, it has been restored\n";
                return;
            }
    
            $lastUpdated = Carbon::create((string)$document->updated_at);
    
            if(!($document->deleted == 1 && now()->diffInDays($lastUpdated) < Document::SOFT_DELETE_PERIOD_IN_DAYS)){
                echo "Skipping document, it has been restored then deleted again";
                return;
            }
        }

        echo "Document eligible for removal\n";

        $documentRepository = new DocumentRepository();
        $documentRepository->user($user)->document($document);
        $documentRepository->deleteDocument();

        
        echo "Document deleted\n\n";
    }//end method handle
}
