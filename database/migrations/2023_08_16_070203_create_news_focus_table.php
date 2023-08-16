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
        Schema::create('news_focus', function (Blueprint $table) {
            $table->string('id', 30);
            $table->integer('user_id');            
            $table->timestamp('created_at', $precision = 0);
            $table->timestamp('updated_at', $precision = 0);
            $table->primary(['id', 'user_id']);
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->index(['id', 'user_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_focus');
    }
};
