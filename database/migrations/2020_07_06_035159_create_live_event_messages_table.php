<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiveEventMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_event_messages', function (Blueprint $table) {
            $table->uuid("id");
            $table->uuid("sender_id");
            $table->uuid("live_event_id");
            $table->text("content");
            $table->timestamps();

            //Keys
            $table->primary("id");
            $table->foreign("sender_id")
                ->references("id")
                ->on("users")
                ->onDelete("cascade");
            $table->foreign("live_event_id")
                ->references("id")
                ->on("live_events")
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
        Schema::dropIfExists('live_event_messages');
    }
}
