<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('status')->default(true);
            $table->tinyInteger('role_type')->default(0);
            $table->timestamp('last_login')->nullable();
            $table->boolean('blacklisted')->default(false);
            $table->string('theme_mode')->default("light");
            $table->string("pod_status")->default(false);
            $table->boolean("two_auth")->default(false);
            $table->string("two_auth_type")->default("email");
            $table->string("verified")->default(false);            
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
