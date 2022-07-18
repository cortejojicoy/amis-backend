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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->index('sessions_user_id_index');
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->text('payload')->nullable(false);
            $table->integer('last_activity')->nullable(false)->index('sessions_last_activity_index');
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
        Schema::dropIfExists('sessions');
    }
};
