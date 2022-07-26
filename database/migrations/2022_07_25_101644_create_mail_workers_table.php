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
        Schema::create('mail_workers', function (Blueprint $table) {
            $table->id();
            $table->string('subject', 255);
            $table->string('recipient', 255);
            $table->string('blade', 255);
            $table->text('data');
            $table->timestamp('queued_at');
            $table->timestamp('sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_worker');
    }
};
