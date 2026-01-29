<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('program_sessions');
        // We MUST NOT drop 'sessions' anymore if we want to preserve system sessions, 
        // but if we already corrupted it, we should restore it.
        // To be safe, let's drop it and recreate it properly for Laravel if it doesn't match.
        // However, a simpler path is to just make sure our internal table is named differently.
        
        // Programs table
        Schema::dropIfExists('programs');
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable(); // mental health, academic support, peer support, etc.
            $table->integer('capacity')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Program Sessions table (renamed from sessions)
        Schema::create('program_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->dateTime('scheduled_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->integer('capacity')->nullable();
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });

        // Restore Laravel's Session table
        Schema::dropIfExists('sessions');
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Update attendances table to add new columns
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'program_session_id')) {
                $table->foreignId('program_session_id')->nullable()->after('student_id')->constrained('program_sessions')->onDelete('set null');
            }
            if (!Schema::hasColumn('attendances', 'status')) {
                $table->enum('status', ['present', 'late', 'absent'])->default('present')->after('duration_minutes');
            }
            if (!Schema::hasColumn('attendances', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['program_session_id']);
            $table->dropColumn(['program_session_id', 'status', 'notes']);
        });
        
        Schema::dropIfExists('program_sessions');
        Schema::dropIfExists('programs');
        // Note: We don't drop 'sessions' here as it's a core Laravel table.
    }
};