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
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();            
            $table->string("market_name");
            $table->string("email");
            $table->string("phone")->nullable();
            $table->text("address_1");
            $table->text("address_2")->nullable();
            $table->string("city");
            $table->string("state");
            $table->string("zip");
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
        Schema::dropIfExists('markets');
    }
};
