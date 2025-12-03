CS Ticketing – User Manual
==========================

Catatan umum
------------
- Akses aplikasi: `http://localhost/cs_ticketing/`.
- Layout: sidebar kiri, top navbar (ikon notifikasi + profil), area konten.  
  [ss dashboard]
- Role: **Admin** (akses penuh), **Supervisor** (operasional tiket & pelanggan, kategori read-only), **Agent** (operasional tiket milik sendiri, pelanggan read-only).

Login & logout
--------------
1) Buka halaman login, isi `username` dan `password`, klik **Login**.  
2) Klik menu profil → **Logout** untuk keluar.  
3) Jika akun ditandai `must_change_password`, akan dipaksa ke halaman **Ganti Password** setelah login.

Lupa password & reset internal
------------------------------
- Guest klik **Lupa password** di halaman login, isi `username` + `kontak` terdaftar, submit. Pesan selalu generic (tidak bocorkan valid/tidak). Permintaan tercatat untuk admin.
- Admin membuka **User Management** → kartu **Permintaan Reset Password**:
  - Klik **Reset & Beri Password** → sistem generate password sementara, set `must_change_password=1`, tampilkan sekali di layar untuk disampaikan lewat kanal internal.
  - Reset juga bisa langsung per user lewat tombol **Reset Password** di tabel user (hasil sama).
- User login dengan password sementara → wajib ganti password.

Dashboard
---------
[ss dashboard]
- Filter periode, kategori, status (otomatis terapkan; search box di tempat lain pakai tombol **Cari**).
- Stat cards (hari ini, bulan berjalan, open/solved), tren harian (baru vs selesai), bar per kategori.
- Tabel “Keluhan Terbaru” + tombol **Lihat semua** menuju daftar keluhan.

Keluhan
-------
[ss keluhan list]
- Daftar: filter (kategori/status/prioritas/channel/date range), search (kode/no HP/nama) dengan tombol **Cari**, sort per kolom (ikon arah), pagination dinamis.
- Quick status (modal) dan bulk update status (centang beberapa, isi catatan, pilih status). Agent hanya bisa mengubah tiket miliknya.
- Ekspor: CSV atau XLSX (SpreadsheetML).  
- “Detail” membuka halaman detail + timeline log dan lampiran.
- Tombol **Update** membuka halaman edit (cek kepemilikan untuk agent).

Tambah keluhan
--------------
[ss keluhan create]
1) Isi No HP → klik **Cek Pelanggan** untuk auto-fill jika sudah ada.  
2) Isi kategori, channel, prioritas, deskripsi (wajib).  
3) Simpan → tiket dibuat, notifikasi ke admin/supervisor.

Detail & timeline keluhan
-------------------------
[ss keluhan detail]
- Menampilkan info pelanggan, detail tiket, status, prioritas, deskripsi.
- Timeline log: status, catatan, penulis, waktu, lampiran (jika ada).
- Form tambah log/status + upload lampiran multiple. File disimpan di `public/uploads/keluhan/{id}/log_{log_id}/` dan bisa diunduh dari detail.

Pelanggan
---------
[ss pelanggan list]
- List + filter/search (tombol **Cari**).  
- Tambah/Edit hanya untuk admin & supervisor (tombol disembunyikan untuk agent).  
- Agent hanya dapat membaca.

Admin: User Management
----------------------
[ss admin users]
- Tambah user: nama, kontak (No HP/Email), username, password, role, aktif/nonaktif.
- Tabel user: nonaktifkan/aktifkan, reset password per user (password sementara ditampilkan sekali).
- Kartu “Permintaan Reset Password”: daftar pending dari form lupa password; aksi reset seperti di atas.
- User yang sedang login tidak ditampilkan di tabel (tidak bisa reset diri sendiri).

Admin: Kategori Keluhan
-----------------------
[ss admin kategori]
- Admin: tambah/edit kategori.  
- Supervisor: hanya lihat (form & tombol aksi disembunyikan; POST diblokir).  
- Agent: tidak ada akses.

Notifikasi
----------
- Ikon lonceng di navbar: buka modal notifikasi (hanya yang belum dibaca) + link **Lihat semua notifikasi**.  
  [ss modal notifikasi]
- Saat modal dibuka, notifikasi yang belum dibaca otomatis ditandai sudah dibaca dan badge hilang setelah refresh.
- Halaman semua notifikasi: menampilkan 10 item pertama, tombol **Muat lebih banyak** untuk memuat 10 berikutnya.  
  [ss semua notifikasi]
- Notifikasi saat ini dibuat pada: tiket baru (to admin/supervisor), perubahan status tiket oleh user lain (ke pembuat tiket).

Profil & Ganti Password
-----------------------
[ss profil]
- Ubah nama dan password sendiri.  
- Jika `must_change_password=1`, wajib ganti sebelum mengakses halaman lain.

Hak akses ringkas
-----------------
- Admin: semua fitur + kelola user + kelola kategori + lihat/tindak reset request.
- Supervisor: dashboard, keluhan penuh (kecuali ubah tiket milik orang lain tetap boleh), pelanggan add/edit, kategori read-only, tidak bisa kelola user.
- Agent: dashboard read, keluhan (tambah; edit/status hanya tiket miliknya; lihat detail/log miliknya), pelanggan read-only, tidak ada menu admin.

Ekspor & impor data
-------------------
- Keluhan list: ekspor CSV atau XLSX. Tidak ada impor massal di versi ini.

Troubleshooting singkat
-----------------------
- “Gagal menyimpan keluhan”: pastikan semua field wajib terisi, channel/prioritas/kategori valid, dan tidak ada error DB lain.
- Lampiran tidak muncul: cek ukuran/ekstensi sesuai konfigurasi PHP; pastikan folder `public/uploads/keluhan` writeable.
- Tidak bisa hapus data: skema memakai `ON DELETE RESTRICT` pada relasi; gunakan nonaktif/soft delete atau pindahkan relasi sebelum hapus.

Penempatan file penting
-----------------------
- Front controller & routing: `public/index.php`
- View: `app/views/...`
- Asset JS/CSS: `public/assets/`
- Upload lampiran: `public/uploads/keluhan/{id}/log_{log_id}/`
