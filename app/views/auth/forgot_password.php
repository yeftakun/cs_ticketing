<?php
$pageTitle = 'Permintaan Reset Password';
ob_start();
?>
<div class="auth-card card shadow-lg border-0 p-4">
    <div class="text-center mb-3">
        <div class="rounded-circle bg-light mx-auto d-flex align-items-center justify-content-center mb-2" style="width:60px;height:60px;">
            <i class="bi bi-shield-lock text-danger fs-3"></i>
        </div>
        <h5 class="mb-1 fw-bold">Permintaan Reset Password</h5>
        <div class="text-muted small">Masukkan username Anda, permintaan akan dikirim ke admin</div>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-3" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success mb-3" role="alert">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        <form method="post" action="?page=forgot-password">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>
            <div class="d-grid">
                <button class="btn btn-danger btn-lg" type="submit">Kirim Permintaan</button>
            </div>
            <div class="mt-3 text-center">
                <a href="?page=login" class="small text-decoration-none">Kembali ke login</a>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/auth.php';
