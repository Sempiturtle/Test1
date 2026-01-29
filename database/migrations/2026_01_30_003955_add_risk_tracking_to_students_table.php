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
        Schema::table('students', function (Blueprint $table) {
            $table->enum('risk_level', ['low', 'moderate', 'high'])->default('low');
            $table->timestamp('last_follow_up_at')->nullable();
            $table->boolean('is_at_risk')->default(false);
            $table->text('risk_factors')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['risk_level', 'last_follow_up_at', 'is_at_risk', 'risk_factors']);
        });
    }
};
