<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('public_users', function (Blueprint $table) {
            $table->id('user_ID');
            $table->string('user_Name');
            $table->string('user_Email')->unique();
            $table->string('user_Phone_Number');
            $table->string('user_Password');
            $table->string('user_Status');
            $table->timestamp('user_Created_At')->nullable();
            $table->timestamp('user_Updated_At')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_users');
    }
};
