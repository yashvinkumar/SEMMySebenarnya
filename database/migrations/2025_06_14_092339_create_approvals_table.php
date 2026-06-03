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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id('approval_ID');
            $table->unsignedBigInteger('inquiry_ID');
            $table->unsignedBigInteger('staff_ID');
            $table->string('approval_Status');
            $table->text('approval_Comments')->nullable();
            $table->string('approval_Type');
            $table->timestamp('approval_Date')->nullable();

            $table->foreign('inquiry_ID')->references('inquiry_ID')->on('inquiries')->onDelete('cascade');
            $table->foreign('staff_ID')->references('staff_ID')->on('mcmc_staff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
