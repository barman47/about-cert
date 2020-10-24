<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->tinyInteger("for_admin")->default(0);
            $table->longText("data");
            $table->string("handler");
            $table->tinyInteger("viewed")->default(0);
            $table->uuid("receiver_id");
            $table->string("receiver_type");
            $table->uuid("sender_id");
            $table->string("sender_type");
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
        Schema::dropIfExists('alerts');
    }
}
