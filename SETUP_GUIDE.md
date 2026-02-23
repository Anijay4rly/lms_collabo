# Laravel Leave Management System - Setup & Features Guide

## ✅ Project Status: HEALTHY & OPERATIONAL

### Version: 1.0.0
### Last Updated: February 12, 2026

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.1+
- MySQL/MariaDB
- Composer
- Node.js & npm (optional, for frontend assets)

### Installation Steps

```bash
# 1. Clone or navigate to project
cd /path/to/lms

# 2. Install dependencies
composer install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Database setup
php artisan migrate

# 5. Clear caches
php artisan cache:clear
php artisan route:clear
php artisan config:clear

# 6. Start development server
php artisan serve
# Access at http://localhost:8000
```

---

## 📋 Features Implemented

### 1. **User Management** ✓
- Create, read, update, delete users
- Role-based access control (student, employee, HOD, admin)
- Department assignment
- Password hashing and validation
- **Features:**
  - Full name construction (fname, mname, lname)
  - Email authentication
  - Gender and date of birth
  - Department association
  - Secure password management

### 2. **Department Management** ✓
- Full CRUD operations on departments
- Staff count tracking
- Protection against deletion if users assigned
- Department details view with staff listings
- **Features:**
  - Auto-count of assigned employees
  - Description/notes field
  - Timestamps for audit trail
  - Cascade validation

### 3. **Leave Request Management** ✓
- Complete leave request workflow
- Three-tier approval system
- Multi-status tracking
- Advanced filtering and search

#### Leave Workflow:
```
Employee Submits → HOD Approves (Signs) → Admin Approves (Signs) → PDF Download
  (submitted)        (on_progress)            (approved)            Available
```

#### Status Values:
- **submitted** - Initial state when employee submits
- **pending** - HOD is reviewing
- **on_progress** - HOD has approved, awaiting admin
- **approved** - Fully approved with both signatures
- **rejected** - Denied by HOD or Admin

### 4. **Digital Signatures** ✓
- Canvas-based signature capture for HOD and Admin
- Base64 image encoding and storage
- Signature timestamps with full datetime
- Optional remarks/comments for each approver
- **Features:**
  - Clear/redraw functionality
  - Real-time signature display in PDF
  - Signature verification in views
  - Locked state after approval

### 5. **PDF Generation** ✓
- Official leave certificate generation
- Header: "MINISTRY OF HEALTH"
- Includes:
  - Employee full details
  - Leave type and duration
  - HOD signature and approval date
  - Admin signature and approval date
  - Both sets of remarks if provided
  - Document generation timestamp
- **Features:**
  - TCPDF library integration
  - High-quality output
  - Professional formatting
  - Accessible only when fully approved
  - Download available to employee and admins

### 6. **File Management** ✓
- Support document uploads (PDF, JPG, PNG)
- Inline file viewing in modals
- Image preview for image files
- PDF iframe viewer
- Download functionality
- **Features:**
  - Max 2MB file size
  - Multiple format support
  - System-based viewing
  - No external dependencies needed

### 7. **Data Validation** ✓
- No backdated leave submissions
- Overlapping leave detection
- Custom 'today_or_future' validation rule
- Date range validation (end >= start)
- **Features:**
  - Application-level validation
  - Database constraints
  - User-friendly error messages
  - Clear validation feedback

### 8. **Search & Filtering** ✓
- DataTables integration on all list pages
- Real-time search across all columns
- Custom filters for leaves:
  - Filter by status
  - Filter by leave type
  - Filter by date range (from_date, to_date)
  - Active filters display with badges
  - Empty state messaging
- **Features:**
  - Server-side ready
  - Client-side pagination
  - Sorting on sortable columns
  - Responsive tables

### 9. **Role-Based Access Control** ✓
- **Employee**: View own leaves, submit requests, see approvals
- **HOD**: View department leaves, approve with signature, add remarks
- **Admin**: View all leaves, final approval, view as "pending", manage all data
- **Student**: Read-only access to own data
- **Features:**
  - Middleware protection
  - Controller-level checks
  - View-level authorization
  - Department isolation for HODs

### 10. **User Interface** ✓
- AdminLTE 3.2 dashboard template
- Bootstrap 5 responsive design
- Font Awesome 5.15.4 icons
- Modern card-based layouts
- **Features:**
  - Sidebar navigation
  - Top navigation bar
  - Responsive mobile design
  - Color-coded status badges
  - Modal dialogs for actions

### 11. **Notifications & Feedback** ✓
- SweetAlert2 modal messages
- Auto-dismiss success messages (3 seconds)
- Error/warning/info alerts
- Persistent error displays
- **Features:**
  - CDN-based library
  - Custom colors per alert type
  - Backdrop overlay
  - Beautiful animations
  - User can dismiss manually

### 12. **Database Features** ✓
- Energy-efficient migrations
- Proper foreign key constraints
- Cascading deletes where appropriate
- Soft deletes ready (can be added)
- **Migrations:**
  - `0001_01_01_000000_create_departments_table`
  - `0001_01_01_000001_create_users_table`
  - `0001_01_01_000002_create_jobs_table` (unused)
  - `0001_01_01_000001_create_cache_table`
  - `2026_02_10_092907_create_leave_requests_table`
  - `2026_02_12_000000_add_signatures_to_leave_requests_table`

---

## 📁 Project Structure

```
lms/
├── app/
│   ├── Http/Controllers/
│   │   ├── UserController.php
│   │   ├── DepartmentController.php
│   │   ├── LeaveRequestController.php
│   │   ├── ProfileController.php
│   │   ├── ReportController.php
│   │   ├── AuditLogController.php
│   │   └── Auth/AuthController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Department.php
│   │   └── LeaveRequest.php
│   └── Providers/
│       └── AppServiceProvider.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   │   ├── layouts/
│   │   │   │   ├── app.blade.php (main layout)
│   │   │   │   ├── sidebar.blade.php
│   │   │   │   ├── topbar.blade.php
│   │   │   │   └── messages.blade.php
│   │   ├── leaves/ (5 views)
│   │   ├── users/ (4 views)
│   │   ├── departments/ (4 views)
│   │   └── auth/ (login/register)
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php
│   └── console.php
├── storage/
│   ├── app/public/ (uploaded files)
│   ├── framework/cache/
│   └── logs/
├── public/
│   ├── storage/ (symlink to storage/app/public)
│   └── index.php
└── vendor/ (dependencies)
```

---

## 🔐 Security Features

✓ Password hashing (bcrypt)
✓ CSRF protection
✓ Role-based authorization
✓ Department isolation
✓ Input validation
✓ SQL injection prevention (Eloquent ORM)
✓ XSS protection (Blade escaping)
✓ Secure session handling

---

## 📊 API Routes (RESTful)

### Users
```
GET    /users              Users index table
GET    /users/create       Create form
POST   /users              Store new user
GET    /users/{user}       Show user details
GET    /users/{user}/edit  Edit form
PUT    /users/{user}       Update user
DELETE /users/{user}       Delete user
```

### Departments
```
GET    /departments              Departments index
GET    /departments/create       Create form
POST   /departments              Store new department
GET    /departments/{id}         Show department
GET    /departments/{id}/edit    Edit form
PUT    /departments/{id}         Update department
DELETE /departments/{id}         Delete department
```

### Leave Requests
```
GET    /leaves                    All leaves (admin/hod filtered)
GET    /leaves/create             Submit leave form
POST   /leaves                    Store new leave request
GET    /leaves/{id}               View leave details
GET    /leaves/{id}/edit          Manage/approve form
PUT    /leaves/{id}               Update/approve request
DELETE /leaves/{id}               Delete request
GET    /myleaves                  My leaves (user filtered)
GET    /leaves/{id}/download-pdf  Download signed PDF
```

---

## 🎯 Testing the Features

### Test Scenario: Complete Leave Approval Workflow

1. **Login as Employee**
   - Navigate to "My Leaves"
   - Click "Apply for Leave"
   - Fill: Type (e.g., "Sick Leave"), Start Date, End Date
   - Optional: Upload supporting document (PDF/JPG/PNG)
   - Submit → Redirects to "My Leaves" with success message

2. **Login as HOD**
   - Navigate to "Leave Requests"
   - View pending leave from employee
   - Click Edit
   - Draw digital signature in canvas
   - Add optional remarks
   - Click "Approve (Sign)" → Leave status becomes "On Progress"

3. **Login as Admin**
   - Navigate to "Leave Requests"
   - See leaves as "Pending" (status = on_progress)
   - Click Edit
   - Draw digital signature
   - Add remarks if needed
   - Click "Approve (Sign)" → Leaves become "Approved"

4. **Employee Downloads Certificate**
   - Login as employee
   - Go to "My Leaves"
   - Click on approved leave
   - Click "Download PDF" → Ministry of Health certificate with both signatures

---

## 🛠 Development Credentials

**Default Admin User** (if seeded):
- Email: admin@example.com
- Password: password

**Test Department**: Default department will be created in migrations

---

## Known Limitations & Future Enhancements

### Current Scope:
- Single organization (Ministry of Health)
- Predefined leave types (can be extended)
- Basic approval workflow (2-tier)
- Manual file management

### Potential Enhancements:
- Email notifications on leave status changes
- Leave balance tracking
- Bulk leave import/export
- Advanced reporting & analytics
- Multi-language support
- Email integration for notifications
- Leave balance auto-calculation
- Holiday calendar management
- Attendance integration
- Mobile app (React Native)

---

## 🐛 Troubleshooting

### "No users found" Error
- Run: `php artisan tinker`
- Create user: `\App\Models\User::create(['fname' => 'Test', 'lname' => 'User', 'email' => 'test@example.com', 'password' => \Hash::make('password'), 'role' => 'admin'])`

### File Upload Not Working
- Check: `storage/app/public/` is writable
- Run: `chmod -R 755 storage/`
- Run: `php artisan storage:link`

### Routes Not Working
- Clear route cache: `php artisan route:clear`
- Rebuild: `php artisan route:cache`

### Blade Syntax Errors
- Clear view cache: `php artisan view:clear`
- Check for: @forelse instead of @foreach when using @empty

### Database Connection Error
- Check .env DATABASE settings
- Test connection: `php artisan tinker`

---

## 📞 Support

For issues or questions:
1. Check application logs: `storage/logs/laravel.log`
2. Run health check: `php artisan tinker`
3. Verify routes: `php artisan route:list`
4. Check migrations: `php artisan migrate:status`

---

## 📝 Version History

### v1.0.0 (Feb 12, 2026)
- ✅ Complete user management
- ✅ Department management
- ✅ Multi-tier leave approval workflow
- ✅ Digital signatures
- ✅ PDF generation
- ✅ File management & viewing
- ✅ Search & filtering
- ✅ Role-based access control
- ✅ Modal notifications
- ✅ Data validation

---

## 🎓 Learning Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Blade Templating](https://laravel.com/docs/blade)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [AdminLTE Documentation](https://adminlte.io/)
- [Bootstrap 5](https://getbootstrap.com/docs/5.0/)
- [SweetAlert2](https://sweetalert2.github.io/)

---

## 📄 License

This project is built on Laravel framework. Respect all open-source licenses used.

---

**System Status:** ✅ Fully Operational
**Last Health Check:** February 12, 2026
**All Features:** Tested & Working
