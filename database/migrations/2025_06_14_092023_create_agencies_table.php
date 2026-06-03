<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('agencies', function (Blueprint $table) {
            $table->id('agency_ID');
            $table->string('agency_Name');
            $table->string('agency_Type');
            $table->string('agency_Email')->unique();
            $table->string('agency_Phone');
            $table->string('agency_Password');
            $table->boolean('agency_First_Time_Login');
            $table->timestamp('agency_Created_At')->nullable();
            $table->timestamp('agency_Updated_At')->nullable();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
