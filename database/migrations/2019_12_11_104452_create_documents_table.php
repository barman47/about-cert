<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id');

            $table->uuid("user_id");
            $table->string("name");
            $table->nullableUuidMorphs("documentable");
            $table->text("src");
            $table->string("type");
            $table->tinyInteger("deleted")->default(0);
            $table->string("extension");
            $table->string("size");
            $table->timestamps();

            //Keys
            $table->primary("id");
            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->onDelete("cascade");
            $table->index("documentable_id");
            $table->index("documentable_type");
        });
    }//end method up

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
