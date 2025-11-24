<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsuranceClaimTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurance_claim_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('insurance_claim_id'); // FIXED
            $table->string('lang_code');

            $table->string('title');
            $table->longText('description')->nullable();
            $table->timestamps();

            $table->foreign('insurance_claim_id')->references('id')->on('insurance_claims')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('insurance_claim_translations');
    }
}
