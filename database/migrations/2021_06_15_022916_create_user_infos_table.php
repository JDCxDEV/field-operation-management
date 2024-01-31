<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('avatar')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('country')->nullable();
            $table->string('language')->nullable();
            $table->string('timezone')->nullable();
            $table->string('currency')->nullable();
            $table->string('communication')->nullable();
            $table->tinyInteger('company_id')->nullable();
            $table->tinyInteger('market_id')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('user_infos');
    }
}
