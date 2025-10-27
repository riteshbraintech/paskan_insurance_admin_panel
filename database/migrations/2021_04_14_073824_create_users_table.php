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
            $table->bigIncrements('id');
            $table->string('member_id')->nullable();
            $table->string('certification_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('dial_code',5)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('password', 150)->nullable();
            $table->string('image')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->date('dob')->nullable();
            $table->integer('country_id')->nullable();
            $table->text('address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('wallet_amount')->nullable();
            $table->string('remarks')->nullable();
            $table->string('subscribed')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->boolean('is_active')->default(0);
            $table->string('device_name', 100)->nullable();
            $table->string('device_type', 100)->nullable();
            $table->string('device_id', 100)->nullable();
            $table->string('firebase_token', 150)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
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
