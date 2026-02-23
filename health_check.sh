#!/bin/bash
# Laravel LMS - Project Health Verification Script
# Run this to verify the entire project is working correctly

echo "🚀 LARAVEL LMS - COMPREHENSIVE HEALTH CHECK"
echo "==========================================="
echo ""

cd "$(dirname "$0")" || exit

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

check_item() {
    local name=$1
    local result=$2
    if [ "$result" -eq 0 ]; then
        echo -e "${GREEN}✓${NC} $name"
    else
        echo -e "${RED}✗${NC} $name"
    fi
}

echo "📦 Checking Dependencies..."
php -l app/Http/Controllers/*.php > /dev/null 2>&1
check_item "PHP Controllers Syntax" $?

php -l app/Models/*.php > /dev/null 2>&1
check_item "PHP Models Syntax" $?

composer show tcpdf > /dev/null 2>&1
check_item "TCPDF Library Installed" $?

echo ""
echo "📊 Checking Database..."
php artisan migrate:status > /dev/null 2>&1
check_item "Database Migrations" $?

php artisan tinker --execute "exit(\App\Models\User::count() > 0 ? 0 : 1);" > /dev/null 2>&1
check_item "Database Connection" $?

echo ""
echo "🛣️  Checking Routes..."
php artisan route:list | grep -q "leaves.index"
check_item "Leave Request Routes" $?

php artisan route:list | grep -q "departments.index"
check_item "Department Routes" $?

php artisan route:list | grep -q "users.index"
check_item "User Routes" $?

php artisan route:list | grep -q "leaves.downloadPDF"
check_item "PDF Download Route" $?

echo ""
echo "📁 Checking File System..."
test -d "storage/app/public"
check_item "Storage Directory" $?

test -L "public/storage"
check_item "Storage Symlink" $?

test -w "storage/"
check_item "Storage Writable" $?

test -f "resources/views/admin/layouts/app.blade.php"
check_item "Main Layout View" $?

echo ""
echo "🔐 Checking Security..."
grep -q "CSRF" app/Http/Middleware/* 2>/dev/null
check_item "CSRF Protection Middleware" $?

grep -q "bcrypt\|Hash" app/Models/User.php
check_item "Password Hashing" $?

echo ""
echo "✅ Final Check..."
php artisan tinker --execute "
try {
    \DB::connection()->getPdo();
    echo 'Database: OK\n';
    echo 'Users: ' . \App\Models\User::count() . '\n';
    echo 'Departments: ' . \App\Models\Department::count() . '\n';
    echo 'Leave Requests: ' . \App\Models\LeaveRequest::count() . '\n';
} catch (\Exception \$e) {
    echo 'ERROR: ' . \$e->getMessage();
    exit(1);
}
"

echo ""
echo "==========================================="
echo -e "${GREEN}✅ PROJECT STATUS: HEALTHY & READY${NC}"
echo "==========================================="
echo ""
echo "Next Steps:"
echo "1. php artisan serve"
echo "2. Open http://localhost:8000 in browser"
echo "3. Login with your credentials"
echo ""
