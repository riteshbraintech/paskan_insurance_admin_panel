<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryFormFieldsOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_form_fields_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained('categoryformfields')->cascadeOnDelete();
            $table->string('value');
            // $table->foreignId('parent_option_id')->nullable()->constrained('category_form_fields_options')->nullOnDelete();
            $table->integer('order')->default(0);
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
        Schema::dropIfExists('category_form_fields_options');
    }
}
