<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diaries', function (Blueprint $table) {
            $table->id();
            $table->string('mood');
            $table->string('anxiety');
            $table->string('energy');
            $table->string('self_confidence');
            $table->string('feeling');
            $table->unsignedBigInteger('client_profile_id');
            $table->foreign('client_profile_id')->references('id')->on('client_profiles');
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
        Schema::dropIfExists('diaries');
    }
}
