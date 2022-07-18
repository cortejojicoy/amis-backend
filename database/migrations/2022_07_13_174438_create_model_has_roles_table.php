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
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->bigInteger('role_id');
            $table->string('model_type', 255);
            $table->bigInteger('model_id');
            $table->timestamps();
            $table->primary(['role_id', 'model_type', 'model_id'], 'model_has_roles_pkey');
            $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_has_roles');
    }
};
