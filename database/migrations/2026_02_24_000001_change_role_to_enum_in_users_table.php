<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'hod', 'employee') DEFAULT 'employee'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(255) DEFAULT 'employee'");
    }
};
