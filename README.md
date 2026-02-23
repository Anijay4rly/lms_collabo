# 🏥 Laravel Leave Management System (LMS)

A professional, full-featured Leave Management System built with Laravel 12, featuring digital signatures, PDF generation, role-based access control, and a complete approval workflow.

![Status](https://img.shields.io/badge/Status-✅%20Operational-success)
![Laravel](https://img.shields.io/badge/Laravel-12.50.0-red)
![PHP](https://img.shields.io/badge/PHP-8.1+-informational)
![License](https://img.shields.io/badge/License-MIT-blue)

---

## 🎯 Features

### 👥 User Management
- Create, read, update, delete users
- Role-based access (Student, Employee, HOD, Admin)
- Department assignment
- Full name with middle name support
- Secure password hashing

### 🏢 Department Management
- Complete CRUD operations
- Auto staff count tracking
- Protection against deletion with assigned users
- Organized staff directory

### 📋 Leave Request System
- Multi-tier approval workflow (Employee → HOD → Admin)
- Real-time status tracking
- Support document uploads (PDF, JPG, PNG)
- Advanced filtering & search
- Overlapping leave detection
- No backdated leave submissions

### ✍️ Digital Signatures
- Canvas-based signature capture for HOD & Admin
- Base64 image storage
- Timestamps for each approval
- Optional remarks/comments
- Signature verification in views

### 📄 PDF Generation
- Official leave certificates with Ministry of Health branding
- Embedded signatures from both HOD and Admin
- Professional formatting with approval details
- Easy download for employees

### 🔍 Search & Filtering
- Real-time DataTable search on all list pages
- Advanced leave filters (status, type, date range)
- Active filter badges
- Responsive pagination

### 🔐 Role-Based Access Control
- **Student**: View own data only
- **Employee**: Submit leaves, view approvals
- **HOD**: Approve leaves from own department
- **Admin**: Full system access
- Department-based isolation for HODs

---

## 🚀 Quick Start

### Prerequisites
```bash
- PHP 8.1 or higher
- MySQL/MariaDB
- Composer
- Node.js (optional)
```

### Installation

```bash
# 1. Clone the repository
cd /path/to/lms

# 2. Install dependencies
composer install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure database in .env
DB_DATABASE=lms
DB_USERNAME=root
DB_PASSWORD=

# 5. Run migrations
php artisan migrate

# 6. Clear caches
php artisan cache:clear
php artisan route:clear

# 7. Start development server
php artisan serve
```

**Access**: http://localhost:8000

---

## 📊 Project Structure

```
lms/
├── app/
│   ├── Http/Controllers/
│   │   ├── UserController.php
│   │   ├── DepartmentController.php
│   │   ├── LeaveRequestController.php
│   │   └── Auth/AuthController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Department.php
│   │   └── LeaveRequest.php
│   └── Providers/
├── database/migrations/
├── resources/views/
│   ├── admin/layouts/
│   ├── leaves/
│   ├── users/
│   └── departments/
├── routes/web.php
└── storage/app/public/ (uploaded files)
```

---

## 🎬 Workflow Example

### 1. Employee Submits Leave
```
Dashboard → My Leaves → Apply for Leave
├── Select Leave Type (Sick, Annual, etc.)
├── Choose Start Date & End Date
├── Optional: Upload supporting document
└── Submit → Status: Submitted
```

### 2. HOD Reviews & Approves
```
Dashboard → Leave Requests
├── Edit pending leave
├── Draw digital signature
├── Add remarks (optional)
└── Click "Approve (Sign)" → Status: On Progress
```

### 3. Admin Reviews & Approves
```
Dashboard → Leave Requests (see as "Pending")
├── Edit leave with on_progress status
├── Draw digital signature
├── Add remarks (optional)
└── Click "Approve (Sign)" → Status: Approved
```

### 4. Employee Downloads Certificate
```
My Leaves → Click approved leave
├── View all signatures and approvals
└── Click "Download PDF" → Save official certificate
```

---

## 🔒 Security Features

✅ **Password Hashing**: bcrypt encryption  
✅ **CSRF Protection**: Cross-site request forgery prevention  
✅ **SQL Injection Prevention**: Eloquent ORM parameterized queries  
✅ **XSS Protection**: Blade template escaping  
✅ **Role-Based Access**: Controller & middleware checks  
✅ **Department Isolation**: HODs can only manage their department  
✅ **Sessions**: Secure session handling  
✅ **Input Validation**: Server-side validation on all forms  

---

## 🛠 API Routes

### Users (RESTful)
```
GET     /users              → List users
POST    /users              → Create user
GET     /users/{id}         → View user
PUT     /users/{id}         → Update user
DELETE  /users/{id}         → Delete user
```

### Departments
```
GET     /departments        → List departments
POST    /departments        → Create department
GET     /departments/{id}   → View department
PUT     /departments/{id}   → Update department
DELETE  /departments/{id}   → Delete department
```

### Leave Requests
```
GET     /leaves             → All leaves (role filtered)
POST    /leaves             → Submit new leave
GET     /leaves/{id}        → View leave details
PUT     /leaves/{id}        → Update/approve leave
DELETE  /leaves/{id}        → Delete leave
GET     /myleaves           → User's own leaves
GET     /leaves/{id}/download-pdf → Download certificate
```

---

## 📦 Technologies Used

| Technology | Version | Purpose |
|-----------|---------|---------|
| Laravel | 12.50.0 | Framework |
| PHP | 8.1+ | Backend language |
| MySQL | 5.7+ | Database |
| Bootstrap | 5.3 | CSS Framework |
| AdminLTE | 3.2 | Dashboard UI |
| DataTables | 1.13.7 | Table enhancement |
| SweetAlert2 | 11 | Modal notifications |
| TCPDF | 6.10.1 | PDF generation |
| Font Awesome | 5.15.4 | Icons |
| Blade | 12.x | Templating |

---

## 📋 Database Schema

### Users Table
```
- id (PK)
- fname, mname, lname
- email (unique)
- password (hashed)
- gender
- dob (date of birth)
- role (student/employee/hod/admin)
- department_id (FK)
- timestamps
```

### Departments Table
```
- id (PK)
- name (unique)
- description
- timestamps
```

### Leave Requests Table
```
- id (PK)
- user_id (FK)
- request_type
- start_date
- end_date
- status (submitted/pending/on_progress/approved/rejected)
- report_path
- hod_signature (base64)
- hod_signed_at
- hod_remarks
- admin_signature (base64)
- admin_signed_at
- admin_remarks
- timestamps
```

---

## 🧪 Testing

### Health Check
```bash
php artisan tinker

# Check database
DB::connection()->getPdo();

# Check users
App\Models\User::count();

# Check migrations
php artisan migrate:status
```

### Run Built-in Check
```bash
bash health_check.sh
```

---

## ⚙️ Configuration

Key environment variables in `.env`:

```env
APP_NAME="Leave Management System"
APP_URL=http://localhost:8000

DB_DATABASE=lms
DB_USERNAME=root
DB_PASSWORD=

MAIL_DRIVER=log
MAIL_HOST=localhost
```

---

## 📚 Documentation

- **[SETUP_GUIDE.md](SETUP_GUIDE.md)** - Complete setup and feature documentation
- **[FEATURE_CHECKLIST.md](FEATURE_CHECKLIST.md)** - Detailed feature implementation checklist
- **[health_check.sh](health_check.sh)** - Automated health verification script

---

## 🐛 Troubleshooting

### Database Connection Error
```bash
# Verify .env settings
php artisan config:cache

# Test connection
php artisan tinker
DB::connection()->getPdo();
```

### File Upload Issues
```bash
# Ensure storage is writable
chmod -R 755 storage/

# Create symlink
php artisan storage:link
```

### Routes Not Working
```bash
# Clear route cache
php artisan route:clear
php artisan route:cache

# Verify routes
php artisan route:list
```

### Blade Templating Errors
```bash
# Clear view cache
php artisan view:clear

# Check for balanced directives
# Use @forelse instead of @foreach when using @empty
```

---

## 💡 Key Features Explained

### Digital Signature Workflow
1. Canvas captures user's drawing
2. Converted to base64 PNG image
3. Stored in database
4. Embedded in PDF on download
5. Locked after approval (can't edit)

### Leave Overlap Detection
Prevents conflicts by checking:
- Does new leave start within existing range?
- Does new leave end within existing range?
- Does new leave completely contain existing range?
- Does existing leave completely contain new range?

### Role-Based Filtering
- **Employees**: See only their own leaves
- **HODs**: See department leaves (matching department_id)
- **Admins**: See all leaves
- **Students**: Read-only access

---

## 🚀 Production Deployment

1. Set `.env` to `production`
2. Disable debug mode: `APP_DEBUG=false`
3. Update database credentials
4. Run migrations: `php artisan migrate`
5. Clear caches: `php artisan config:cache`
6. Set proper file permissions
7. Configure HTTPS/SSL
8. Setup backup strategy

---

## 📝 License

This project is free to use for educational and commercial purposes.

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit pull requests.

---

## 📞 Support

For issues or questions:
1. Check [SETUP_GUIDE.md](SETUP_GUIDE.md)
2. Review logs: `storage/logs/laravel.log`
3. Run health check: `bash health_check.sh`
4. Verify migrations: `php artisan migrate:status`

---

## 🎓 Learning Resources

- [Laravel Docs](https://laravel.com/docs)
- [Blade Templating](https://laravel.com/docs/blade)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [AdminLTE](https://adminlte.io/)
- [Bootstrap](https://getbootstrap.com/)

---

## ✅ Status

**System Status**: Fully Operational ✓  
**Last Updated**: February 12, 2026  
**All Tests**: Passing ✓  
**Security**: Implemented ✓  
**Documentation**: Complete ✓  

---

<div align="center">

**Made with ❤️ for Leave Management**

Built on Laravel | Designed for Production | Ready to Deploy

</div>

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# lms_collabo
