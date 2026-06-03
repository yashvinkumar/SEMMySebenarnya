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
        // Schema::create('inquiry_progress', function (Blueprint $table) {
        //     $table->id('progress_ID');
        //     $table->foreignId('inquiry_ID')->constrained('inquiries', 'inquiry_ID')->onDelete('cascade');
        //     $table->foreignId('agency_ID')->constrained('agencies', 'agency_ID')->onDelete('cascade');
        //     $table->foreignId('user_ID')->constrained('public_users', 'user_ID')->onDelete('cascade');
        //     $table->foreignId('staff_ID')->constrained('mcmc_staff', 'staff_ID')->onDelete('cascade');
        //     $table->string('progress_Status');
        //     $table->text('progress_Remarks')->nullable();
        //     $table->timestamp('progress_Updated_At')->nullable();
        // });
        Schema::create('inquiry_progress', function (Blueprint $table) {
            $table->id('progress_ID');

            $table->foreignId('inquiry_ID')->constrained('inquiries', 'inquiry_ID')->onDelete('cascade');
            $table->foreignId('agency_ID')->constrained('agencies', 'agency_ID')->onDelete('cascade');
            $table->foreignId('user_ID')->constrained('public_users', 'user_ID')->onDelete('cascade');
            $table->foreignId('staff_ID')->nullable()->constrained('mcmc_staff', 'staff_ID')->onDelete('cascade');

            $table->foreignId('assignment_ID')->constrained('inquiry_assignments', 'assignment_ID')->onDelete('cascade'); // ✅ New FK

            $table->string('progress_Status');
            $table->text('progress_Remarks')->nullable();
            $table->timestamp('progress_Updated_At')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiry_progress');
    }
};
