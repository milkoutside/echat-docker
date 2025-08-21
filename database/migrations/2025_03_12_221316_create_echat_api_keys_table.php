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
        Schema::create('echat_api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('sender'); // Обычная строка
            $table->string('service'); // Обычная строка
            $table->longText('api_key'); // Очень длинная строка
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('echat_api_keys');
    }
};
