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
            $table->id();
            $table->string('name', 1024);
            $table->string('email', 1024)->unique();
            $table->string('email_token', 1024);
            $table->timestamp('email_token_expired_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 1024);
            $table->string('remember_token', 1024)->nullable();
            $table->string('secret_key', 1024)->nullable();
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
        Schema::dropIfExists('users');
    }
}
