<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->date("dob")->nullable();
            $table->string("nationality")->nullable();
            $table->string("occupation", 20)->nullable();
            $table->string("marital_status", 10)->nullable();
            $table->string("gender", 10)->nullable();
            $table->string("phone_number", 20)->nullable();
            $table->text("about")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people');
    }
}
