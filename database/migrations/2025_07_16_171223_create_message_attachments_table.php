<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageAttachmentsTable extends Migration
{
    public function up()
    {
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->string('file_path');   // S3 path or URL
            $table->string('file_name');   // original name
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size');  // in bytes
            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('message_attachments');
    }
}
