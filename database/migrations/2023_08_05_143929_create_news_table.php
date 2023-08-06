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
        Schema::create('news', function (Blueprint $table) {            
            $table->string('id', 30);
            $table->string('category', 10);
            $table->dateTime('gmdate');
            $table->dateTime('updated_gmdate')->nullable();
            $table->string('image', 200)->nullable();
            $table->string('title', 100);
            $table->string('link', 200);
            $table->string('content', 300);
            $table->timestamp('created_at', $precision = 0);
            $table->timestamp('updated_at', $precision = 0);
            $table->primary('id');
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->index(['category', 'gmdate']);
            $table->index(['category']);
            $table->index(['gmdate']);
            $table->fullText('title');
            $table->fullText('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
