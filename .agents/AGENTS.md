# Perangkat Pembelajaran Project Rules

## Aturan Git & Versi Aplikasi
Setiap kali Anda (AI Agent) selesai melakukan perubahan pada kode (*codebase*), Anda **WAJIB**:
1. Memperbarui tanggal pembaruan di file `app/Config/AppVersion.php` ke waktu saat ini.
2. Melakukan *commit* Git secara otomatis (melalui command line `git add .` dan `git commit -m "..."`).
3. Mengingatkan USER di akhir respon untuk melakukan *push* ke GitHub dengan perintah `git push` agar server/hosting mereka ikut ter-update.
