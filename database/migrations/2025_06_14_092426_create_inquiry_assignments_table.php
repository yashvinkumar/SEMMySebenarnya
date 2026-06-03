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
        Schema::create('inquiry_assignments', function (Blueprint $table) {
            $table->id('assignment_ID');
            $table->foreignId('agency_ID')->constrained('agencies', 'agency_ID')->onDelete('cascade');
            $table->foreignId('approval_ID')->constrained('approvals', 'approval_ID')->onDelete('cascade');
            $table->timestamp('assignment_Date')->nullable();
            $table->string('assignment_Status')->default('pending');
            $table->text('assignment_Comments')->nullable();
            $table->text('rejection_Reason')->nullable();
            $table->foreignId('assigned_By')->nullable()->constrained('mcmc_staff', 'staff_ID');
            $table->timestamp('completed_At')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiry_assignments');
    }
};
