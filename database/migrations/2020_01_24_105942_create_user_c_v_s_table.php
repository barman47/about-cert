<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCVSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_c_v_s', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid("c_v_template_id");
            $table->uuid("user_id");
            $table->string("src")->nullable();
            $table->timestamps();

            // Index Keys
            $table->primary("id");
            $table->foreign("c_v_template_id")
                    ->references("id")
                    ->on("c_v_templates")
                    ->onDelete("cascade");
            
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
        Schema::dropIfExists('user_c_v_s');
    }
}
