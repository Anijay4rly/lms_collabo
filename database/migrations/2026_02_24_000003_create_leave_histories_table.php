<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if(!Schema::hasTable('leave_histories')) {
        Schema::create('leave_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('leave_request_id')
                  ->constrained('leave_requests')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('action'); // submitted, approved, rejected, etc.
            $table->text('remarks')->nullable();

            $table->timestamps(); // adds created_at & updated_at
        });
    }
    }
    public function down(): void
    {
        Schema::dropIfExists('leave_request_tracks');
    }
};
