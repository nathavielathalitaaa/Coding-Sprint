# Langkah Serah Terima SinergiHRS

## Sebelum Deploy
1. `php artisan migrate`
2. `php artisan db:seed --class=ProductionSeeder`
3. `php artisan storage:link`
4. `php artisan config:cache`
5. `php artisan route:cache`
6. `php artisan view:cache`

## Akun Pertama
Email: **admin@sinergihotel.com**
Password: **Sinergi@2026**
→ SEGERA GANTI PASSWORD setelah login pertama!

## Langkah HR Setelah Login
1. Buka menu Pengaturan → Master Data
   → Sesuaikan jabatan, status, role sesuai kebutuhan
2. Buka menu Jenis Surat
   → Review dan sesuaikan jenis surat & approver
3. Buka menu Karyawan → Import Excel
   → Download template → isi data → upload
4. Setiap approver (HR, HOD, Direktur, dll) harus login dan selesaikan onboarding TTD + PIN
5. Test buat surat dan approve end-to-end
