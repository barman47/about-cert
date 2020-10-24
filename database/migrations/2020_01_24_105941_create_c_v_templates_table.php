<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCVTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_v_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid("c_v_template_group_id");
            $table->string("name")->unique();
            $table->string("price")->nullable();
            $table->string('preview_img');
            $table->string('template_file');
            $table->timestamps();

            $table->foreign("c_v_template_group_id")
                    ->references("id")
                    ->on("c_v_template_groups")
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
        Schema::dropIfExists('c_v_templates');
    }
}
