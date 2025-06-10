<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname')->nullable();
            $table->string('gender')->nullable(); // e.g., 'male', 'female', 'other'
            $table->string('interested_in')->nullable(); // e.g., 'male', 'female', 'both'
            $table->date('dob')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nickname', 'gender', 'interested_in', 'dob']);
        });
    }
};
