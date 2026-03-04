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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Add 'assigned_as' if it doesn't exist
                if (!Schema::hasColumn('users', 'assigned_as')) {
                    $table->string('assigned_as', 100)
                          ->nullable()
                          ->after('role')
                          ->comment('e.g. HOD, Supervisor, HR, etc.');
                }

                // Add 'status' only if it doesn't exist
                if (!Schema::hasColumn('users', 'status')) {
                    $table->enum('status', ['inactive', 'active', 'retired', 'deceased'])
                          ->default('active')
                          ->after('assigned_as')
                          ->comment('User account status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'assigned_as')) {
                $table->dropColumn('assigned_as');
            }
        });
    }
};
