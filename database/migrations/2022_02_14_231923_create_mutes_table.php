<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mutes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('muted')->nullable()->unsigned()->index();
            $table->bigInteger('muted_by')->nullable()->unsigned()->index();
            $table->foreign('muted')->references('id')->on('users');
            $table->foreign('muted_by')->references('id')->on('users');
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
        Schema::dropIfExists('mutes');
    }
}
