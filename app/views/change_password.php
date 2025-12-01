<?php
$pageTitle = 'Ganti Password';
$subtitle = 'Anda harus mengganti password sementara';
$breadcrumbs = ['Home' => '?page=dashboard', 'Ganti Password' => null];
$activeMenu = '';
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title mb-3">Ganti Password</h6>
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
                <form method="post" action="?page=change-password">
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirm" class="form-control" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-danger" type="submit">Simpan</button>
                        <a class="btn btn-secondary" href="?page=logout">Logout</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layouts/main.php';
