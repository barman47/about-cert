<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id');

            $table->string("username");
            $table->text("address")->nullable();
            $table->string("display_photo")->nullable();
            $table->string("cover_image")->nullable();
            $table->string("thumbnail")->nullable();
            $table->string("password")->nullable();
            $table->string("name")->nullable();
            $table->string("email")->nullable();
            $table->string("job_title")->nullable();
            $table->json("allowed_devices");

            $table->uuidMorphs("entity");

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            //Keys
            $table->primary("id");
            $table->unique("username");
            $table->unique("email");
            $table->index("entity_id");
            $table->index("entity_type");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
