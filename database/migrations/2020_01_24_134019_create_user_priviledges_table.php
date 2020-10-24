<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPriviledgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_priviledges', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid("priviledge_id");
            $table->uuid("target_id")->nullable();
            $table->text("meta")->nullable();
            $table->uuid("user_id");
            $table->timestamps();

            //Index keys
            $table->primary("id");

            $table->foreign("user_id")
                    ->references("id")
                    ->on("users")
                    ->onDelete("cascade");

            $table->foreign("priviledge_id")
                    ->references("id")
                    ->on("priviledges")
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
        Schema::dropIfExists('user_priviledges');
    }//end method down
}
