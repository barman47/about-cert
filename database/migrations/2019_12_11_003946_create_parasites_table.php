<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParasitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parasites', function (Blueprint $table) {
            $table->uuid('id');

            $table->uuid("host");
            $table->uuid("parasite");

            $table->timestamps();

            //Keys
            $table->primary("id");
            $table->foreign("host")
                    ->references("id")
                    ->on("users")
                    ->onDelete("cascade");

            $table->foreign("parasite")
                    ->references("id")
                    ->on("users")
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
        Schema::dropIfExists('parasites');
    }
}
