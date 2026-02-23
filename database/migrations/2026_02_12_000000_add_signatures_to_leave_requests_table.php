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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->longText('hod_signature')->nullable();
            $table->timestamp('hod_signed_at')->nullable();
            $table->text('hod_remarks')->nullable();

            $table->longText('admin_signature')->nullable();
            $table->timestamp('admin_signed_at')->nullable();
            $table->text('admin_remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn([
                'hod_signature',
                'hod_signed_at',
                'hod_remarks',
                'admin_signature',
                'admin_signed_at',
                'admin_remarks'
            ]);
        });
    }
};
