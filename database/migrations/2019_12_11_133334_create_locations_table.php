<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuidMorphs("trackable");
            $table->string("unformatted_address");
            $table->string("formatted_address")->nullable();
            $table->double("lng", 30, 15)->nullable();
            $table->double("lat", 30, 15)->nullable();
            $table->timestamps();

            //Keys
            $table->primary("id");
            $table->index("trackable_id");
            $table->index("trackable_type");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
