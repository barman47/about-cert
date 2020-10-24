<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignaturePriviledgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signature_priviledges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("name");
            $table->string("code");
            $table->double("price");
            $table->string("duration_unit");
            $table->integer("duration");
            $table->integer("max_within_duration");
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
        Schema::dropIfExists('signature_priviledges');
    }
}
