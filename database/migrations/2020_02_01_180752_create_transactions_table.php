<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid("user_id");
            $table->double("amount", 12, 2);
            $table->string("reference_id");
            $table->string("status");
            $table->json("meta")->nullable();
            $table->timestamps();

            // Keys
            $table->primary("id");
            $table->foreign("user_id")
                    ->references("id")
                    ->on('users')
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
        Schema::dropIfExists('transactions');
    }//end method down
}
