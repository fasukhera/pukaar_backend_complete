<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTherapistProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('therapist_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('about');
            $table->string('city');
            $table->string('service_therapist_provider');
            $table->string('therapist_focus');
            $table->string('type_of_doctor');
            $table->text('introduction');
            $table->text('education');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('therapist_profiles');
    }
}
