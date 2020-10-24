<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\User;
use Image;

class ResizeCoverPhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }//end constructor

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

            $user = $this->user;
            $image = Image::make(public_path()  . $user->cover_image);

            $image->orientate();

            $image->resize(700, null, function($constraint){
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save();

            $user->save();
        }catch(\Exception $e){
            throw $e;
        }finally{
            ini_set('memory_limit',$memory);
        }
    }
}
