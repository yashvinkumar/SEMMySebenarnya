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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id('inquiry_ID');
            $table->foreignId('user_ID')->constrained('public_users', 'user_ID')->onDelete('cascade');
            $table->string('inquiry_Title');
            $table->text('inquiry_Description');
            $table->string('inquiry_Category');
            $table->string('inquiry_Attachment_URL')->nullable();
            $table->string('inquiry_Status');
            $table->timestamp('inquiry_Created_At')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
