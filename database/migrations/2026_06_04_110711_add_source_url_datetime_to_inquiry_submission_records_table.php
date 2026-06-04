<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('source_news_url')->nullable()->after('inquiry_Category');
            $table->dateTime('date_time_encountered')->nullable()->after('source_news_url');
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn(['source_news_url', 'date_time_encountered']);
        });
    }
};