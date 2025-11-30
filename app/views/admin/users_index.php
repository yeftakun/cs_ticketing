<?php
$pageTitle = 'User Management';
$subtitle = 'Atur akses agent, supervisor, dan admin';
$breadcrumbs = ['Home' => '#', 'Admin' => '#', 'User Management' => null];
$activeMenu = 'users';

$users = [
    ['nama' => 'Ayu Rahma', 'username' => 'ayu.rahma', 'role' => 'supervisor', 'status' => 'aktif'],
    ['nama' => 'Budi Pratama', 'username' => 'budi.pratama', 'role' => 'agent', 'status' => 'aktif'],
    ['nama' => 'Citra Dewi', 'username' => 'citra.dewi', 'role' => 'agent', 'status' => 'nonaktif'],
    ['nama' => 'Rama Putra', 'username' => 'rama.putra', 'role' => 'admin', 'status' => 'aktif'],
];

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h6 class="text-muted mb-0">Kelola akun pengguna</h6>
    </div>
    <button class="btn btn-danger"><i class="bi bi-plus-lg me-1"></i> Tambah User</button>
</div>

<div class="card border-0 mb-3">
    <div class="card-body">
        <form class="row gy-2 gx-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Cari</label>
                <input type="text" class="form-control" placeholder="Nama / username">
            </div>
            <div class="col-md-3">
                <label class="form-label">Role</label>
                <select class="form-select">
                    <option>Semua</option>
                    <option>agent</option>
                    <option>supervisor</option>
                    <option>admin</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select">
                    <option>Semua</option>
                    <option>aktif</option>
                    <option>nonaktif</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-danger" type="button">Filter</button>
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
                                <?php if ($user['status'] === 'aktif'): ?>
                                    <span class="badge bg-success-subtle text-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-danger">Edit</button>
                                    <button class="btn btn-outline-secondary"><?= $user['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?></button>
                                    <button class="btn btn-outline-warning">Reset Password</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
