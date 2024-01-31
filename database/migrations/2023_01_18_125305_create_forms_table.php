<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Form;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('client_first_name')->nullable();
            $table->string('client_last_name')->nullable();
            $table->date('client_dob')->nullable();
            $table->string('client_ssn')->nullable();
            $table->string('client_sex')->nullable();
            $table->string('client_phone')->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_address_line_1')->nullable();
            $table->string('client_address_line_2')->nullable();
            $table->string('client_city')->nullable();
            $table->string('client_state')->nullable();
            $table->string('client_zip')->nullable();
            $table->boolean('product_change')->nullable();
            $table->boolean('pregnant')->nullable();
            $table->boolean('married')->nullable();
            $table->boolean('smoker')->nullable();
            $table->boolean('dependents')->nullable();
            $table->boolean('american_indian_native')->nullable();
            $table->string('spousal_first_name')->nullable();
            $table->string('spousal_last_name')->nullable();
            $table->string('spousal_dob')->nullable();
            $table->string('spousal_ssn')->nullable();
            $table->string('spousal_employer_name')->nullable();
            $table->string('spousal_employer_phone')->nullable();
            $table->string('spousal_income')->nullable();
            $table->boolean('spouse')->nullable();
            $table->boolean('domestic_partner')->nullable();
            $table->string('main_id_type')->nullable();
            $table->string('main_id_file')->nullable();
            $table->string('other_file')->nullable();
            $table->string('image_with_form')->nullable();
            $table->string('additional_file_one')->nullable();
            $table->string('employer_name')->nullable();
            $table->string('employer_phone')->nullable();
            $table->bigInteger('income')->nullable();
            $table->string('income_frequency')->nullable();
            $table->bigInteger('tax_credit_amount')->nullable();
            $table->string('plan_premium')->nullable();
            $table->bigInteger('you_pay')->nullable();
            $table->string('plan_name')->nullable();
            $table->string('insurance')->nullable();
            $table->string('plan_id')->nullable();
            $table->string('plan_selected')->nullable();
            $table->date('date_processed')->nullable();
            $table->string('cc_number')->nullable();
            $table->date('cc_expiration_date')->nullable();
            $table->string('cc_cvc')->nullable();
            $table->string('additional_notes')->nullable();
            $table->string('signature')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->tinyInteger('current_step')->nullable();
            $table->tinyInteger('status')->default(Form::DRAFT);
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
        Schema::dropIfExists('forms');
    }
};
