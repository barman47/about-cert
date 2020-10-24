<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('user_id');
            $table->text("content");
            $table->string("title", 100);
            $table->tinyInteger("allow_comments")->default(1);
            $table->datetime("time");
            $table->string("category", 20);
            $table->string("img")->nullable();
            $table->string("video")->nullable();
            $table->timestamps();
            //Note:: If any value is added or removed, include or remove it from the columns property in the class Post
            //Keys
            $table->primary("id");
            $table->foreign("user_id")
                    ->references("id")
                    ->on("users")
                    ->onDelete("cascade");

        });
    }//end method up

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
