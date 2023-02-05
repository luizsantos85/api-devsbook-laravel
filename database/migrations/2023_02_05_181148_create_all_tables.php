<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllTables extends Migration
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
            $table->string('email',100);
            $table->string('password',200);
            $table->string('name', 100);
            $table->date('birthdate');
            $table->string('city', 100)->nullable();
            $table->string('work', 100)->nullable();
            $table->string('avatar', 100)->default('avatar.jpg');
            $table->string('cover', 100)->default('cover.jpg');
            $table->string('token', 200)->nullable();
        });

        Schema::create('users_relations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_from');
            $table->integer('user_to');
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('type',20);
            $table->text('body');
            $table->dateTime('created_at');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        });

        Schema::create('posts_likes', function (Blueprint $table) {
            $table->id();
            $table->dateTime('created_at');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
        });

        Schema::create('posts_comments', function (Blueprint $table) {
            $table->id();
            $table->dateTime('created_at');
            $table->text('body');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
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
        Schema::dropIfExists('users_relations');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('posts_likes');
        Schema::dropIfExists('posts_comments');
    }
}
