<?php
$pageTitle = 'User Management';
$subtitle = 'Atur akses agent, supervisor, dan admin';
$breadcrumbs = ['Home' => '?page=dashboard', 'Admin' => '#', 'User Management' => null];
$activeMenu = 'users';
ob_start();
?>
<div class="card border-0 mb-3">
    <div class="card-body">
        <h6 class="text-muted mb-3">Tambah User</h6>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form class="row g-3" method="post" action="?page=admin-users">
            <input type="hidden" name="action" value="create">
            <div class="col-md-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Role</label>
                <select class="form-select" name="role">
                    <option value="agent">agent</option>
                    <option value="supervisor">supervisor</option>
                    <option value="admin">admin</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="aktif" id="aktif" checked>
                    <label class="form-check-label" for="aktif">Aktif</label>
                </div>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-danger" type="submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($user['nama']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><span class="badge bg-light text-dark text-uppercase"><?= htmlspecialchars($user['role']) ?></span></td>
                            <td>
                                <?php if ((int)$user['is_active'] === 1): ?>
                                    <span class="badge bg-success-subtle text-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form class="d-inline" method="post" action="?page=admin-users">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                                    <button class="btn btn-outline-secondary btn-sm" type="submit"><?= (int)$user['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="5" class="text-center text-muted">Belum ada pengguna.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
