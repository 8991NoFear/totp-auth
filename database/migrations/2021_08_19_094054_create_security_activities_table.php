<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSecurityActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('security_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            // $table->foreign('user_id')->references('id')->on('users');
            $table->string('action', 1024);
            $table->string('device', 1024)->nullable();
            $table->string('location', 1024)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('security_activities');
    }
}
