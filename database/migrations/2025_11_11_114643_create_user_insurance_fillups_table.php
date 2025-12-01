<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInsuranceFillupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_insurance_enqueries_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uie_id'); 
            $table->unsignedBigInteger('user_id'); 
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('form_field_id'); 
            $table->unsignedBigInteger('form_field_name'); 
            $table->string('form_field_value');

            $table->timestamps();

            // Foreign Key
            $table->foreign('uie_id')->references('id')->on('user_insurance_enqueries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_insurance_enqueries_details');
    }
}
