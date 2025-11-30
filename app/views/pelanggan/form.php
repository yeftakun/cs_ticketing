<?php
$pageTitle = 'Form Pelanggan';
$subtitle = 'Tambah atau perbarui data pelanggan';
$breadcrumbs = ['Home' => '#', 'Pelanggan' => '#', 'Form' => null];
$activeMenu = 'pelanggan';

$pelanggan = [
    'nama' => 'Anisa Putri',
    'no_hp' => '0812-9001-2233',
    'email' => 'anisa@mail.com',
    'kota' => 'Jakarta'
];

ob_start();
?>
<div class="card">
    <div class="card-body">
        <h6 class="card-title mb-3">Data Pelanggan</h6>
        <form class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama Pelanggan</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($pelanggan['nama']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">No HP</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($pelanggan['no_hp']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($pelanggan['email']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Kota</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($pelanggan['kota']) ?>">
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-danger" type="button">Simpan</button>
                <a class="btn btn-secondary" href="#">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
