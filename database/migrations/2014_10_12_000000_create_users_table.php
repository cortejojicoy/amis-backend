<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('uuid');
            $table->integer('sais_id')->primary()->index('users_sais_id');
            $table->string('last_name', 255);
            $table->string('first_name', 255);
            $table->string('middle_name', 255)->nullable();
            $table->string('email', 255)->unique();
            $table->string('user_roles', 29);
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
        Schema::dropIfExists('users');
    }
};
