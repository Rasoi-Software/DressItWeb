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
        Schema::table('looks', function (Blueprint $table) {
            $table->uuid('device_id')->nullable()->index();
            $table->enum('status', ['draft', 'published'])->default('draft');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('looks', function (Blueprint $table) {
            //
        });
    }
};
