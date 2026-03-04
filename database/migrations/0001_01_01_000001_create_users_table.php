<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- USERS TABLE ---
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();

                // Personal info
                $table->string('fname');
                $table->string('mname')->nullable();
                $table->string('lname');
                $table->enum('gender', ['male', 'female'])->nullable();
                $table->date('dob')->nullable();

                // Employment info
                $table->string('role')->default('employee'); // admin | hod | employee
                $table->foreignId('department_id')
                      ->nullable()
                      ->constrained('departments')
                      ->nullOnDelete();

                // Auth
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');

                // Other
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // --- PASSWORD RESET TOKENS TABLE ---
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // --- SESSIONS TABLE ---
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
