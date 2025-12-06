CS Ticketing
============

Sistem tiket keluhan pelanggan berbasis PHP + Bootstrap dengan layout sidebar + top navbar. Mendukung multi-role (admin, supervisor, agent), pencatatan keluhan, timeline log, lampiran, dan notifikasi.

[Guide](docs/manual.md)

Fitur utama
-----------
- **Autentikasi & Role**: admin (full), supervisor (operasional + kategori read-only), agent (operasional tiket milik sendiri, pelanggan read-only).
- **Dashboard**: ringkasan stat, chart kategori/tren, keluhan terbaru.
- **Keluhan**: list dengan filter/sort, quick/bulk status, ekspor CSV/XLSX, detail + timeline log + lampiran, tambah/edit (cek kepemilikan untuk agent).
- **Pelanggan**: list + filter; tambah/edit oleh admin & supervisor; agent hanya baca.
- **Admin**:
  - User Management: tambah/aktif/nonaktif, reset password internal (password sementara + `must_change_password`), kartu permintaan reset.
  - Master Kategori: tambah/edit (admin), supervisor read-only.
- **Notifikasi**: modal notifikasi unread, halaman “semua notifikasi” dengan muat lebih banyak; trigger saat tiket baru (to admin/supervisor) dan perubahan status oleh user lain (ke pemilik tiket).
- **Lupa Password Internal**: form username + kontak; permintaan dicatat untuk admin; reset menampilkan password sementara sekali.

Sebuahg catatan
----------------
- `public/index.php` — front controller + routing + handler.
- `app/views/` — tampilan (dashboard, keluhan, pelanggan, admin, auth, notif, profil).
- `public/assets/` — CSS/JS kustom.
- `public/uploads/keluhan/` — penyimpanan lampiran per log.

Catatan
-------
- Skema pakai `ON DELETE RESTRICT` pada relasi; gunakan nonaktif/soft delete atau pindahkan relasi sebelum menghapus data yang dipakai.
- Placeholder screenshot bisa ditambahkan sesuai kebutuhan dokumentasi.
