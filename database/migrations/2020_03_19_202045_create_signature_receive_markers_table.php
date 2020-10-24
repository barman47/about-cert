<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignatureReceiveMarkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signature_receive_markers', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('signature_send_marker_id');
            $table->string('embedded_signing_url');
            $table->string("document_name");
            $table->uuid("receiver_id");
            $table->longText("data")->nullable();
            $table->tinyInteger("signed")->default(0);
            $table->tinyInteger("viewed")->default(0);
            $table->tinyInteger("deleted")->default(0);
            $table->timestamps();

            //Keys
            $table->primary("id");
            $table->foreign("receiver_id")
                    ->references("id")
                    ->on("users")
                    ->onDelete("cascade");
            $table->foreign("signature_send_marker_id")
                    ->references("id")
                    ->on("signature_send_markers")
                    ->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('signature_receive_markers');
    }
}
