<?php
$pageTitle = 'Daftar Keluhan';
$subtitle = 'Pantau seluruh keluhan pelanggan dan lakukan tindakan cepat';
$breadcrumbs = ['Home' => '#', 'Keluhan' => null];
$activeMenu = 'keluhan';

$complaints = [
    ['tanggal' => '2025-01-12', 'kode' => 'KEL-202501-010', 'pelanggan' => 'Rizky Ramadhan / 0812-9988-1122', 'kategori' => 'Jaringan', 'channel' => 'Call Center', 'status' => 'Open', 'prioritas' => 'Critical', 'petugas' => 'Ayu Rahma'],
    ['tanggal' => '2025-01-12', 'kode' => 'KEL-202501-011', 'pelanggan' => 'Sari Melati / 0813-6677-8899', 'kategori' => 'Tagihan', 'channel' => 'WhatsApp', 'status' => 'On Progress', 'prioritas' => 'High', 'petugas' => 'Budi Pratama'],
    ['tanggal' => '2025-01-11', 'kode' => 'KEL-202501-012', 'pelanggan' => 'Lukman Hakim / 0811-5544-3322', 'kategori' => 'Promo', 'channel' => 'Grapari', 'status' => 'Pending', 'prioritas' => 'Medium', 'petugas' => 'Citra Dewi'],
    ['tanggal' => '2025-01-11', 'kode' => 'KEL-202501-013', 'pelanggan' => 'Mega Putra / 0812-2233-4455', 'kategori' => 'Layanan Data', 'channel' => 'Aplikasi', 'status' => 'Solved', 'prioritas' => 'Medium', 'petugas' => 'Rian Saputra'],
    ['tanggal' => '2025-01-10', 'kode' => 'KEL-202501-014', 'pelanggan' => 'Tito Aprilia / 0812-9090-1122', 'kategori' => 'Perangkat', 'channel' => 'Media Sosial', 'status' => 'Closed', 'prioritas' => 'Low', 'petugas' => 'Ayu Rahma'],
];

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h6 class="text-muted mb-0">Kelola keluhan masuk</h6>
    </div>
    <a href="#" class="btn btn-danger"><i class="bi bi-plus-lg me-1"></i> Tambah Keluhan</a>
</div>

<div class="card border-0 mb-3">
    <div class="card-body">
        <form class="row gy-2 gx-3">
            <div class="col-md-4">
                <label class="form-label">Cari</label>
                <input type="text" class="form-control" placeholder="Cari kode keluhan / no HP / nama pelanggan">
            </div>
            <div class="col-md-2">
                <label class="form-label">Kategori</label>
                <select class="form-select">
                    <option>Semua</option>
                    <option>Jaringan</option>
                    <option>Tagihan</option>
                    <option>Layanan Data</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select">
                    <option>Semua</option>
                    <option>Open</option>
                    <option>On Progress</option>
                    <option>Pending</option>
                    <option>Solved</option>
                    <option>Closed</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Prioritas</label>
                <select class="form-select">
                    <option>Semua</option>
                    <option>Low</option>
                    <option>Medium</option>
                    <option>High</option>
                    <option>Critical</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Channel</label>
                <select class="form-select">
                    <option>Semua</option>
                    <option>Call Center</option>
                    <option>Grapari</option>
                    <option>WhatsApp</option>
                    <option>Aplikasi</option>
                    <option>Media Sosial</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Dari</label>
                <input type="date" class="form-control" value="2025-01-05">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sampai</label>
                <input type="date" class="form-control" value="2025-01-12">
            </div>
            <div class="col-md-2 d-flex gap-2 align-items-end">
                <button class="btn btn-danger flex-fill" type="button">Filter</button>
                <button class="btn btn-outline-secondary" type="button">Reset</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tanggal Lapor</th>
                        <th>Kode Keluhan</th>
                        <th>Pelanggan</th>
                        <th>Kategori</th>
                        <th>Channel</th>
                        <th>Status</th>
                        <th>Prioritas</th>
                        <th>Petugas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($complaints as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['tanggal']) ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($row['kode']) ?></td>
                            <td><?= htmlspecialchars($row['pelanggan']) ?></td>
                            <td><?= htmlspecialchars($row['kategori']) ?></td>
                            <td><?= htmlspecialchars($row['channel']) ?></td>
                            <td><span class="badge status-badge <?= str_replace(' ', '', $row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                            <td><span class="badge priority-badge <?= $row['prioritas'] ?>"><?= htmlspecialchars($row['prioritas']) ?></span></td>
                            <td><?= htmlspecialchars($row['petugas']) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary">Detail</button>
                                    <button class="btn btn-outline-danger">Update Status</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <nav class="mt-3">
            <ul class="pagination justify-content-end mb-0">
                <li class="page-item disabled"><span class="page-link">Prev</span></li>
                <li class="page-item active"><span class="page-link">1</span></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
        </nav>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
