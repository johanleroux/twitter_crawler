<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');

            $table->bigInteger('twitter_id')->unique()->nullable();
            $table->string('name')->nullable();
            $table->string('screen_name');
            $table->text('description')->nullable();
            $table->text('location')->nullable();

            $table->boolean('should_crawl')->default(0);
            $table->dateTime('crawled_at')->nullable();

            $table->timestamps();
        });

        // \App\User::create([
        //     'twitter_id'  => 69008563,
        //     'name'        => 'Formula 1',
        //     'screen_name' => 'F1',
        //     'description' => 'The Official Formula 1® Account. The F1 Esports Series is bigger and better in 2018. Sign up: https://t.co/fkk4u7ZDtW',
        //     'location'    => 'Great Britian',
        // ]);
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
