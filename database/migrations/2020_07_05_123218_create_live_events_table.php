<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Expression;

class CreateLiveEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_events', function (Blueprint $table) {
            $table->uuid("id");
            $table->uuid("user_id");
            $table->bigInteger("number_of_active_members")->default(0);
            $table->string("cover_image");
            $table->string("title");
            $table->json("can_join")->default(new Expression('(JSON_ARRAY())'));
            $table->string("description");
            $table->tinyInteger("is_in_session")->default(0);
            $table->timestamps();

            //Keys
            $table->primary("id");
            $table->foreign("user_id")
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
        Schema::dropIfExists('live_events');
    }
}
