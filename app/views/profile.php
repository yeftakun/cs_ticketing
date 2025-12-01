<?php
$pageTitle = 'Profil Saya';
$subtitle = 'Lihat dan perbarui informasi akun';
$breadcrumbs = ['Home' => '?page=dashboard', 'Profil' => null];
$activeMenu = '';
// role guard: handled di router
ob_start();
?>
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title mb-3">Informasi Akun</h6>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <form class="row g-3" method="post" action="?page=profile">
                    <div class="col-12">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($userProfile['nama'] ?? '') ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($userProfile['username'] ?? '') ?>" disabled>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($userProfile['role'] ?? '') ?>" disabled>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Password Baru (opsional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak diubah">
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-danger" type="submit">Simpan</button>
                        <a class="btn btn-secondary" href="?page=dashboard">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layouts/main.php';
