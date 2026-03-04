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
        if(!Schema::hasTable('leave_requests')) {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();

            // Link to users table
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Core leave fields
            $table->string('request_type'); // e.g., sick, annual
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reasons')->nullable();

            $table->enum('status', ['submitted','pending','approved','rejected'])
                  ->default('submitted');

            $table->timestamps(); // created_at + updated_at
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
