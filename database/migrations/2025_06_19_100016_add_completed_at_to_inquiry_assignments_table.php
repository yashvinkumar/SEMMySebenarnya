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
        Schema::table('inquiry_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('inquiry_assignments', 'completed_At')) {
                $table->timestamp('completed_At')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiry_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('inquiry_assignments', 'completed_At')) {
                $table->dropColumn('completed_At');
            }
        });
    }
};
