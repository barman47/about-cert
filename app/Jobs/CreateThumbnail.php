<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

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
    
            $e = $this->user;    
            $thumbnail_url = $e->thumbnail;
    
            $temp = explode('/', $e->display_photo);
            $thumbnail_url = rtrim($e->display_photo, $temp[count($temp) - 1]) . "thumbnails/" . $temp[count($temp) - 1];
            Storage::disk('public')->delete(ltrim($thumbnail_url, '/storage'));
            if($e->thumbnail != null){
                Storage::disk('public')->delete(ltrim($e->thumbnail, '/storage'));
            }
            Storage::disk('public')->copy(ltrim($e->display_photo, '/storage'), ltrim($thumbnail_url, '/storage'));
    
            $e->thumbnail = $thumbnail_url;
    
            Image::make('public'.$e->thumbnail)->resize(100, 100, function($constraint){
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode('webp')->save()->destroy();
    
            Image::make('public'.$e->display_photo)->resize(null, 250, function($constraint){
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode('webp')->save()->destroy();

            $src = $e->thumbnail;
            $array = explode('.', $e->thumbnail);
            if(count($array) > 0)
                $src = rtrim($src, $array[count($array) - 1]);
            $src = $src . "webp";

            rename(base_path() . "/public" .$e->thumbnail, base_path() . "/public" .$src);

            $e->thumbnail = $src;

            $srcdp = $e->display_photo;
            $array = explode('.', $e->display_photo);
            if(count($array) > 0)
                $srcdp = rtrim($srcdp, $array[count($array) - 1]);
            $srcdp = $srcdp . "webp";

            rename(base_path() . "/public" .$e->display_photo, base_path() . "/public" .$srcdp);

            $e->display_photo = $srcdp;

            $e->thumbnail = $src;
    
            $e->save();
        }catch(Exception $e){

        }finally{
            ini_set('memory_limit',$memory);
        }        
    }//end method handle
}
