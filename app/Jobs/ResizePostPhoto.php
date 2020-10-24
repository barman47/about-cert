<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Post;
use Image;

class ResizePostPhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
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

            $post = $this->post;
            $image = Image::make(public_path()  . $post->img);

            $image->orientate();

            $image->resize(700, null, function($constraint){
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save();

            $post->save();
        }catch(\Exception $e){
            throw $e;
        }finally{
            ini_set('memory_limit',$memory);
        }
    }
}
