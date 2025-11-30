<?php
$pageTitle = 'Data Pelanggan';
$subtitle = 'Monitoring pelanggan dan riwayat keluhan';
$breadcrumbs = ['Home' => '#', 'Pelanggan' => null];
$activeMenu = 'pelanggan';

$pelanggan = [
    ['nama' => 'Anisa Putri', 'no_hp' => '0812-9001-2233', 'email' => 'anisa@mail.com', 'kota' => 'Jakarta', 'jumlah' => 3],
    ['nama' => 'Budi Santoso', 'no_hp' => '0813-7765-9900', 'email' => 'budi@mail.com', 'kota' => 'Bandung', 'jumlah' => 1],
    ['nama' => 'Citra Dewi', 'no_hp' => '0812-4455-8877', 'email' => 'citra@mail.com', 'kota' => 'Surabaya', 'jumlah' => 4],
    ['nama' => 'Dimas Aditya', 'no_hp' => '0811-7788-9900', 'email' => 'dimas@mail.com', 'kota' => 'Jakarta', 'jumlah' => 2],
];

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h6 class="text-muted mb-0">Kelola data pelanggan</h6>
    </div>
    <a href="#" class="btn btn-danger"><i class="bi bi-plus-lg me-1"></i> Tambah Pelanggan</a>
</div>

<div class="card border-0 mb-3">
    <div class="card-body">
        <form class="row gy-2 gx-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Cari Pelanggan</label>
                <input type="text" class="form-control" placeholder="Cari nama / no HP / kota">
            </div>
            <div class="col-md-3">
                <label class="form-label">Kota</label>
                <select class="form-select">
                    <option>Semua</option>
                    <option>Jakarta</option>
                    <option>Bandung</option>
                    <option>Surabaya</option>
                </select>
            </div>
            <div class="col-md-3 d-grid">
                <button class="btn btn-danger" type="button">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama Pelanggan</th>
                        <th>No HP</th>
                        <th>Email</th>
                        <th>Kota</th>
                        <th>Jumlah Keluhan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pelanggan as $row): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['no_hp']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['kota']) ?></td>
                            <td><a href="#" class="text-decoration-none fw-semibold"><?= htmlspecialchars($row['jumlah']) ?> keluhan</a></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary">Detail</button>
                                    <button class="btn btn-outline-danger">Edit</button>
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
