<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('look_comments_reply', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('look_comment_id'); // parent comment id
            $table->text('content');                       // reply content
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('look_comment_id')
                  ->references('id')
                  ->on('look_comments')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('look_comments_reply');
    }
};
