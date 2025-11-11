<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('admin_type_id')->default(3);   // for Superadmin, admin, staff
            $table->integer('role_id')->default(0);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('gender', ['male', 'female', 'transgender'])->default('male');
            $table->date('date_of_birth')->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('dial_code',5)->nullable();
            $table->string('image')->nullable();
            $table->longText('firebase_token')->nullable();
            $table->longText('address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->longText('remarks')->nullable();
            $table->enum('status', ['active', 'inactive', 'block'])->default('active');
            $table->integer('created_by')->default(0);
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
        Schema::dropIfExists('admins');
    }
}
