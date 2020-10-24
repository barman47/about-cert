<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignatureSendMarkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signature_send_markers', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid("user_id");
            $table->uuid("document_id");
            $table->string("document_name");
            $table->string("embedded_signing_url")->nullable();
            $table->tinyInteger("sent")->default(0);
            $table->tinyInteger("deleted")->default(0);
            $table->tinyInteger("executed")->default(0);
            $table->string("src")->nullable();
            $table->longText("data")->nullable();
            $table->longText("outsiders_data")->nullable();
            $table->timestamps();


            //Keys
            $table->primary("id");
            $table->foreign("user_id")
                    ->references("id")
                    ->on("users")
                    ->onDelete("cascade");

            $table->foreign("document_id")
                    ->references("id")
                    ->on("documents")
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
        Schema::dropIfExists('signature_send_markers');
    }
}
