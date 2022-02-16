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
            $table->string('username')->unique()->notNullable();
            $table->string('password')->notNullable();
            $table->string('name')->nullable();
            $table->string('email')->unique()->notNullable();
            $table->string('phone')->nullable();
            $table->boolean('verified')->default(false);
            $table->string('location')->nullable();
            $table->string('gender')->nullable();
            $table->dateTime('birth_date');
            $table->string('website')->nullable();
            $table->string('bio')->nullable();
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
