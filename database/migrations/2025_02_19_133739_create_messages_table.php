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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('sender');
            $table->longText('text')->nullable();
            $table->string('channel');
            $table->longText('fileUrl')->nullable();
            $table->longText('message_id')->nullable();
            $table->string('status');
            $table->dateTime('send_time')->nullable();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('send_type');
            $table->boolean('message_read')->default(true);
            $table->longText('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
