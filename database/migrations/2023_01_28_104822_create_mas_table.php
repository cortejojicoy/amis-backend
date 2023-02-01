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
        Schema::create('mas', function (Blueprint $table) {
            $table->string('id', 15)->primary();
            $table->string('uuid', 36);
            $table->integer('faculty_id');
            $table->string('actions', 6);
            $table->string('status', 20);
            $table->string('mentor_name', 50);
            $table->integer('mentor_role');
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
        Schema::dropIfExists('mas');
    }
};
