<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


use App\User;

use Image;
use Str;

class ResizeProfilePhoto implements ShouldQueue
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
            $displayPhotoUrl = $user->display_photo;

            try{
                if($user->thumbnail) unlink(public_path() . $user->thumbnail);
            }catch(\Exception $e){}

            $savedDir = pathinfo($displayPhotoUrl)['dirname'];
            $extension = pathinfo($displayPhotoUrl)["extension"];

            $thumbnailUrl = $savedDir . '/thumbnails/' . Str::random(30) . ".$extension";

            if (!file_exists(public_path() . pathinfo($thumbnailUrl)['dirname'])) {
                mkdir(public_path() . pathinfo($thumbnailUrl)['dirname'], 0777, true);
            }

            $image = Image::make(public_path()  . $displayPhotoUrl);

            $image->orientate();

            $image->resize(500, null, function($constraint){
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save();

            $image->resize(200, null, function($constraint){
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $image->save(public_path() . $thumbnailUrl);

            $user->thumbnail = $thumbnailUrl;
            $user->save();
        }catch(\Exception $e){
            throw $e;
        }finally{
            ini_set('memory_limit',$memory);
        }
    }//end method handle
}
