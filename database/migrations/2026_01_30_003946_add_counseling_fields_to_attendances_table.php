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
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'category')) {
                $table->string('category')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'severity')) {
                $table->enum('severity', ['low', 'medium', 'high'])->default('low');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['category', 'severity']);
            // Not dropping 'notes' since it seemingly existed before
        });
    }
};
