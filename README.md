# Laravel HR Management System

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.2.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)

**A Modern, Feature-Rich Human Resources Information System (HRIS)**

[Features](#-key-features) • [Installation](#-installation) • [Usage](#-usage) • [Documentation](#-documentation) • [Contributing](#-contributing)

</div>

---

## About

Laravel HR Management System adalah aplikasi HRIS (Human Resources Information System) yang komprehensif dan modern, dibangun dengan Laravel 12. Sistem ini dirancang untuk mengelola seluruh aspek manajemen sumber daya manusia, mulai dari data karyawan, absensi, shift, hingga sistem approval dokumen dengan tanda tangan digital.

### Why This Project?

- ✅ **Complete HR Solution** - Semua fitur HR dalam satu platform
- ✅ **Modern Tech Stack** - Laravel 12, PHP 8.2+, Bootstrap 5, Tailwind CSS
- ✅ **AI-Powered** - Analisis absensi otomatis dengan AI
- ✅ **Flexible Workflow** - Multi-step approval yang dapat dikonfigurasi
- ✅ **Digital Signature** - Sistem tanda tangan digital dengan PIN protection
- ✅ **Role-Based Access** - Keamanan berlapis dengan role & permission
- ✅ **Export Ready** - Export data ke Excel & PDF

---

## Key Features

### 👥 Employee Management
- **Comprehensive Employee Profiles** - Data lengkap karyawan (NIK, NPWP, BPJS, dll)
- **Onboarding System** - Proses onboarding otomatis untuk karyawan baru
- **Digital Signature Upload** - Upload dan kelola tanda tangan digital
- **PIN Protection** - Keamanan approval dengan PIN
- **Photo Management** - Upload dan update foto profil

### Attendance Management
- **🤖 AI-Powered Analysis** - Parsing otomatis file Excel absensi dengan AI
- **Import/Export** - Import dari Excel, export ke Excel & PDF
- **Fingerprint Mapping** - Mapping ID fingerprint ke user
- **Overtime Calculation** - Perhitungan jam lembur otomatis
- **Advanced Filtering** - Filter berdasarkan tanggal, user, status

### 🔄 Shift Management
- **Master Shift** - Kelola shift pagi, siang, malam
- **Schedule Assignment** - Assign shift ke karyawan
- **Monthly Planning** - Perencanaan jadwal shift bulanan
- **Shift Reports** - Laporan shift per periode

### 📄 Document & Approval System
- **Multi-Step Approval Workflow** - Approval bertingkat yang fleksibel
- **Dynamic Document Types** - Konfigurasi jenis dokumen & approver
- **Dual Signature Mode**:
  - **Stamp Mode** - TTD langsung di dokumen
  - **Append Mode** - Cover page terpisah dengan TTD
- **PIN Verification** - Verifikasi PIN saat approval
- **TTD Snapshot** - Menyimpan snapshot TTD saat approval
- **Auto Numbering** - Generate nomor surat otomatis
- **PDF Generation** - Generate & merge PDF otomatis
- **Approval Tracking** - Track status approval real-time
- **Resubmit Mechanism** - Resubmit setelah reject

### 🎉 Holiday Management
- **Holiday Calendar** - Kelola hari libur nasional & perusahaan
- **Integration** - Terintegrasi dengan sistem absensi

### 📊 Dashboard & Reporting
- **Role-Based Dashboard** - Dashboard sesuai role user
- **Statistics** - Total jam lembur, kehadiran, dll
- **Charts & Graphs** - Visualisasi data absensi
- **Export Reports** - Export laporan ke Excel & PDF

### 🔐 Security & Authentication
- **Role-Based Access Control** - Staff, HR, Supervisor, Super Admin
- **Policy-Based Authorization** - Laravel Policy untuk fine-grained control
- **Onboarding Middleware** - Paksa user setup TTD & PIN
- **Private Storage** - Dokumen sensitif di private storage
- **Password Reset** - Forgot & reset password via email

---

## 🛠️ Tech Stack

### Backend
| Technology | Version | Purpose |
|------------|---------|---------|
| **Laravel** | 12.x | PHP Framework |
| **PHP** | 8.2+ | Programming Language |
| **MySQL** | 8.0+ | Database |
| **Spatie Permission** | 6.25 | Role & Permission Management |
| **DomPDF** | 3.1 | PDF Generation |
| **PHPSpreadsheet** | 5.7 | Excel Import/Export |
| **Intervention Image** | 3.11 | Image Processing |
| **FPDF & FPDI** | Latest | PDF Manipulation & Stamping |

### Frontend
| Technology | Version | Purpose |
|------------|---------|---------|
| **Bootstrap** | 5.2.3 | CSS Framework |
| **Tailwind CSS** | 4.0 | Utility-First CSS |
| **Vite** | 7.0 | Build Tool |
| **Axios** | 1.11 | HTTP Client |
| **Toastr** | Latest | Notifications |

---

## 📦 Installation

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Git

### Step-by-Step Installation

1. **Clone Repository**
```bash
git clone https://github.com/nathavielathalitaaa/HR-DTP-Project-Final.git
cd Laravel-12-HR-System-Management
```

2. **Install Dependencies**
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

3. **Environment Setup**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

4. **Database Configuration**

Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Run Migrations & Seeders**
```bash
# Run migrations
php artisan migrate

# Run seeders (optional)
php artisan db:seed
```

6. **Storage Link**
```bash
php artisan storage:link
```

7. **Build Assets**
```bash
# Development
npm run dev

# Production
npm run build
```

8. **Run Application**
```bash
# Development server
php artisan serve

# Or use composer script (with queue & logs)
composer dev
```

Visit: `http://localhost:8000`

### Quick Setup (Alternative)
```bash
composer setup
```

---

## 🚀 Usage

### Default Credentials

After seeding, you can login with:

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@example.com | password |
| HR | hr@example.com | password |
| Supervisor | supervisor@example.com | password |
| Staff | staff@example.com | password |

> ⚠️ **Important**: Change default passwords after first login!

### First Time Setup

1. **Login** dengan credentials di atas
2. **Complete Onboarding**:
   - Upload tanda tangan digital
   - Set PIN untuk approval
3. **Setup Profile**:
   - Lengkapi data profil
   - Upload foto profil
4. **Ready to Use!**

### Common Tasks

#### Import Attendance Data
1. Navigate to **HR → Absensi → Import**
2. Upload Excel file
3. Map fingerprint IDs to users
4. Preview & save

#### Create Document with Approval
1. Navigate to **Surat → Create**
2. Fill document details
3. Submit for approval
4. Track approval status

#### Approve Document
1. Check dashboard for pending approvals
2. Open document
3. Enter PIN
4. Approve or reject with notes

---

## 📁 Project Structure

```
Laravel-12-HR-System-Management/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AbsensiController.php
│   │   │   ├── AbsensiImportController.php
│   │   │   ├── AbsensiAiController.php      # AI-powered attendance
│   │   │   ├── AccountController.php
│   │   │   ├── HRController.php
│   │   │   ├── ShiftController.php
│   │   │   ├── SuratController.php          # Document management
│   │   │   ├── SuratTypeController.php
│   │   │   └── ...
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── EmployeeProfile.php
│   │   ├── Absensi.php
│   │   ├── Surat.php
│   │   ├── DocumentApproval.php
│   │   └── ...
│   ├── Services/
│   │   ├── ApprovalService.php              # Approval workflow logic
│   │   ├── PdfStampService.php              # PDF stamping
│   │   ├── ApprovalCoverService.php         # Cover page generation
│   │   ├── PinVerificationService.php       # PIN verification
│   │   └── ...
│   ├── Policies/
│   │   └── SuratPolicy.php
│   └── Helper/
│       └── helpers.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── hr/
│   │   ├── surat/
│   │   └── ...
│   └── js/
├── routes/
│   └── web.php
├── public/
└── storage/
    └── app/
        └── private/                          # Private storage for TTD & docs
```

---

## 🔌 API Endpoints

### Authentication
```
GET  /login                    - Login page
POST /login                    - Authenticate user
GET  /logout                   - Logout user
POST /register                 - Register new user
```

### Employee Management
```
GET  /hr/employee/list         - List all employees
POST /hr/employee/save         - Create employee
POST /hr/employee/update       - Update employee
POST /hr/employee/delete       - Delete employee
GET  /hr/employee/show/{id}    - Show employee detail
```

### Attendance
```
GET  /hr/absensi/page          - Attendance list
GET  /hr/absensi/import        - Import page
POST /hr/absensi/import        - Import attendance
GET  /hr/absensi/ai            - AI upload page
POST /hr/absensi/ai/analyze    - AI analyze file
GET  /hr/absensi/export/excel  - Export to Excel
GET  /hr/absensi/export/pdf    - Export to PDF
```

### Document Management
```
GET  /surat                    - List documents
GET  /surat/create             - Create document form
POST /surat                    - Store document
GET  /surat/{id}               - Show document
POST /surat/{id}/approve       - Approve document
POST /surat/{id}/reject        - Reject document
GET  /surat/{id}/download      - Download PDF
```

### Shift Management
```
GET  /hr/shift/page            - Shift list
POST /hr/shift/store           - Create shift
GET  /hr/shift/jadwal          - Schedule page
POST /hr/shift/jadwal/store    - Save schedule
```

---

## 🎨 Screenshots

> 📸 Add your screenshots here

```
[Dashboard]  [Employee List]  [Attendance]
[Document Approval]  [Shift Schedule]
```

---

## 🧪 Testing

```bash
# Run tests
composer test

# Run specific test
php artisan test --filter=TestName
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/AmazingFeature`)
3. **Commit** your changes (`git commit -m 'Add some AmazingFeature'`)
4. **Push** to the branch (`git push origin feature/AmazingFeature`)
5. **Open** a Pull Request

### Coding Standards
- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add tests for new features
- Update documentation

---

## 📝 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**Nathaviel Athalita**

- GitHub: [@nathavielathalitaaa](https://github.com/nathavielathalitaaa)
- Repository: [HR-DTP-Project-Final](https://github.com/nathavielathalitaaa/HR-DTP-Project-Final)

---

## 🙏 Acknowledgments

- Laravel Framework
- Spatie for Laravel Permission package
- All contributors and supporters

---

## 📞 Support

If you have any questions or need help, please:
- Open an [Issue](https://github.com/nathavielathalitaaa/HR-DTP-Project-Final/issues)
- Contact via GitHub

---

<div align="center">

**⭐ Star this repository if you find it helpful!**

Made with ❤️ using Laravel

</div>
