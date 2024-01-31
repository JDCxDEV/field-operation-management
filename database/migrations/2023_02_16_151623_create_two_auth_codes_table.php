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
        Schema::create('two_auth_codes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();            
            $table->string('token');
            $table->string('code');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('two_auth_codes');
    }
};
