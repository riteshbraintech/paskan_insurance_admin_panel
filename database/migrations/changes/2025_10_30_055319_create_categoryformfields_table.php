<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryformfieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categoryformfields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('optiontype')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_filtered')->default(0);
            $table->foreignId('parent_field_id')->nullable()->constrained('categoryformfields')->nullOnDelete();
            $table->integer('sort_order')->default(0)->comment('Used to sort fields in order');
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
        Schema::dropIfExists('categoryformfields');
    }
}
