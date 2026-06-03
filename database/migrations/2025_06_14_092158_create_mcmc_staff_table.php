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
        Schema::create('mcmc_staff', function (Blueprint $table) {
            $table->id('staff_ID');
            $table->string('staff_Name');
            $table->string('staff_Email')->unique();
            $table->string('staff_Phone_Number');
            $table->string('staff_Password'); // ✅ added
            $table->timestamp('staff_Created_At')->nullable();
            $table->timestamp('staff_Updated_At')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mcmc_staff');
    }
};
