<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\LiveEvent;
use Image;

class ResizeLiveEventPhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $liveEvent;

    public function __construct(LiveEvent $liveEvent)
    {
        $this->liveEvent = $liveEvent;
    }//end constructor method

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $memory = ini_get('memory_limit');
        try{
            ini_set('memory_limit','-1');
            
            $liveEvent = $this->liveEvent;
            $image = Image::make(public_path($liveEvent->cover_image));

            $image->orientate();

            $image->resize(700, null, function($constraint){
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save();

            $liveEvent->save();
        }catch(\Exception $e){
            throw $e;
        }finally{
            ini_set('memory_limit',$memory);
        }
    }//end method handle
}
