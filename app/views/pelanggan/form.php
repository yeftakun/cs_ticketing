<?php
$pageTitle = 'Form Pelanggan';
$subtitle = 'Tambah atau perbarui data pelanggan';
$breadcrumbs = ['Home' => '?page=dashboard', 'Pelanggan' => '?page=pelanggan', 'Form' => null];
$activeMenu = 'pelanggan';
ob_start();
?>
<div class="card">
    <div class="card-body">
        <h6 class="card-title mb-3">Data Pelanggan</h6>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form class="row g-3" method="post" action="?page=pelanggan-form<?= $isEdit ? '&id=' . (int)$pelanggan['id'] : '' ?>">
            <div class="col-md-6">
                <label class="form-label">Nama Pelanggan</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($_POST['nama'] ?? $pelanggan['nama'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">No HP</label>
                <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($_POST['no_hp'] ?? $pelanggan['no_hp'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? $pelanggan['email'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Kota</label>
                <input type="text" name="kota" class="form-control" value="<?= htmlspecialchars($_POST['kota'] ?? $pelanggan['kota'] ?? '') ?>">
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-danger" type="submit">Simpan</button>
                <a class="btn btn-secondary" href="?page=pelanggan">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
