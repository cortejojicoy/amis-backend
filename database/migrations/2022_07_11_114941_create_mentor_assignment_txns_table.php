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
        Schema::create('mentor_assignment_txns', function (Blueprint $table) {
            $table->bigIncrements('mas_txn_id');
            $table->string('mas_id', 15);
            $table->string('action', 20);
            $table->integer('committed_by');
            $table->text('note');
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
        Schema::dropIfExists('mentor_assignment_txns');
    }
};
