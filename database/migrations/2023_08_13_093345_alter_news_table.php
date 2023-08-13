<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::table('news', function (Blueprint $table) {
            $table->dropFullText('news_title_fulltext');
            $table->dropFullText('news_content_fulltext');
        });

        DB::connection('mysql')->statement('
            CREATE fulltext INDEX news_title_content_fulltext
                ON news ( title, content)
                WITH parser ngram
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
