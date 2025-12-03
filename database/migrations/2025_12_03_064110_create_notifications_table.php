<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');         // who receives the notification
            $table->string('type');                        // e.g., 'claim_created'
            $table->string('title');                       // short title
            $table->text('message');                       // detailed message
            $table->string('link')->nullable();            // optional link to resource
            $table->boolean('is_read')->default(false);    // read/unread
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
        Schema::dropIfExists('notifications');
    }
}
