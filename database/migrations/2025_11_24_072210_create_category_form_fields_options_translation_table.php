<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryFormFieldsOptionsTranslationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_form_fields_option_transalations', function (Blueprint $table) {
            $table->id();   
            $table->foreignId('option_id')->constrained('category_form_fields_options')->cascadeOnDelete();
            $table->string('lang_code');    // en, th
            $table->string('label');    // Honda, होंडा
            $table->string('image');    // Honda, होंडा
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
        Schema::dropIfExists('category_form_fields_option_transalations');
    }
}
