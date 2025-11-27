<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryFieldFormTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_field_form_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('categoryformfield_id')->nullable();
            $table->string('lang_code');
            $table->string('label')->nullable();
            $table->string('place_holder')->nullable(); 
            // $table->json('options')->nullable(); 
            $table->timestamps();
            $table->foreign('categoryformfield_id')->references('id')->on('categoryformfields')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_field_form_translations');
    }
}
