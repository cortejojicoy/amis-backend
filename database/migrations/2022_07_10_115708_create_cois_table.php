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
        Schema::create('cois', function (Blueprint $table) {
            $table->string('coi_id', 15)->primary();
            $table->integer('class_id');
            $table->string('status', 20);
            $table->string('student_id',10);
            $table->text('comment');
            $table->dateTime('submitted_to_sais');
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
        Schema::dropIfExists('cois');
    }
};
