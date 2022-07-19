<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('orientation');
            $table->string('religion');
            $table->string('religion_identifier');
            $table->string('medicines');
            $table->string('sleeping_habit');
            $table->string('problem');
            $table->string('check_assigned')->nullable(); // null = not assigned , 1 = assigned by admin , 2 = accepted by therapist
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('therapist_profile_id')->nullable();
            $table->foreign('therapist_profile_id')->references('id')->on('therapist_profiles');
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
        Schema::dropIfExists('client_profiles');
    }
}
