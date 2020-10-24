<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCVTemplateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_v_template_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("name");
            $table->string("group_code")->unique();
            $table->double("price")->nullable();
            $table->timestamps();
        });
    }//end method up

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('c_v_template_groups');
    }
}
