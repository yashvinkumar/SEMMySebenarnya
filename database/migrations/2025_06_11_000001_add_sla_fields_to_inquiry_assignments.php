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
        if (!Schema::hasColumn('inquiry_assignments', 'due_date')) {
            Schema::table('inquiry_assignments', function (Blueprint $table) {
                $table->timestamp('due_date')->nullable()->after('assignment_Status');
                $table->string('sla_status', 20)->default('On Time')->after('due_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiry_assignments', function (Blueprint $table) {
            $table->dropColumn(['due_date', 'sla_status']);
        });
    }
};