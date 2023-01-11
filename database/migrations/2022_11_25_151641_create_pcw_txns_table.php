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
        Schema::create('pcw_txns', function (Blueprint $table) {
            $table->bigIncrements('pcw_txn_id');
            $table->string('pcwtxnable_id');
            $table->string('pcwtxnable_type');
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
        Schema::dropIfExists('pcw_txns');
    }
};
