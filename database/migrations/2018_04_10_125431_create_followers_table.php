<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->unsignedInteger('follower_id');
            $table->unsignedInteger('leader_id');
    
            $table->foreign('follower_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('leader_id')->references('id')->on('users')->onDelete('cascade');

            $table->primary(['follower_id', 'leader_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('followers');
    }
}
