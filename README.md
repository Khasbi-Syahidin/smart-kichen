# 🍽️ Absensi Makan - Sistem Manajemen Kehadiran Makan

Sistem ini digunakan untuk mencatat dan mengelola absensi makan bagi peserta, termasuk pemilihan sesi makan (sarapan, makan siang, makan malam), menu makanan, dan pengawas. Dibangun menggunakan **Laravel** dan **Filament Admin Panel**.

---

## 🚀 Fitur Utama

- 🎯 CRUD Absensi Makan (menu, sesi, pengawas, waktu)
- 🔄 Validasi unik berdasarkan tanggal + sesi makan
- 📅 Pemilihan waktu menggunakan `DateTimePicker`
- 📢 Notifikasi kesalahan via Filament Notification
- 👨‍🍳 Relasi ke menu makanan dan pengawas (user)
- ✅ Tampilan data dengan badge & formatting tanggal

---

## 🧱 Teknologi

- [Laravel 10+](https://laravel.com/)
- [Filament v3](https://filamentphp.com/)
- [Spatie Laravel Permission (opsional)](https://spatie.be/docs/laravel-permission/)
- [Carbon](https://carbon.nesbot.com/) (format tanggal)
- TailwindCSS (via Filament)

---

## 📦 Instalasi

```bash
git clone https://github.com/username/absensi-makan.git
cd absensi-makan
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed # Jika menggunakan seeder


php artisan tinker
User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
])
