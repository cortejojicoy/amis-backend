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
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('tokenable_type', 255);
            $table->bigInteger('tokenable_id');
            $table->string('name', 255);
            $table->string('token', 64)->unique();
            $table->text('abilities');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->index(["tokenable_type", "tokenable_id"], 'personal_access_tokens_tokenable_type_tokenable_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
