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
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_ID');
            $table->foreignId('staff_ID')->constrained('mcmc_staff', 'staff_ID')->onDelete('cascade');
            $table->string('report_Title');
            $table->timestamp('report_Generated_At')->nullable();
            $table->string('report_Status');
            $table->string('report_Type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
