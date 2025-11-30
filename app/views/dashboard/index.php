<?php
$pageTitle = 'Dashboard Keluhan';
$subtitle = 'Ringkasan keluhan pelanggan berdasarkan periode Januari 2025';
$breadcrumbs = ['Home' => '#', 'Dashboard' => null];
$activeMenu = 'dashboard';

$stats = [
    ['label' => 'Total Keluhan Hari Ini', 'value' => 32, 'icon' => 'bi-activity', 'variant' => 'danger', 'note' => '+12% dari kemarin'],
    ['label' => 'Total Keluhan Bulan Ini', 'value' => 845, 'icon' => 'bi-graph-up', 'variant' => 'primary', 'note' => '+4.1% mom'],
    ['label' => 'Keluhan Open', 'value' => 128, 'icon' => 'bi-exclamation-circle', 'variant' => 'warning', 'note' => 'Perlu prioritas'],
    ['label' => 'Keluhan Solved', 'value' => 622, 'icon' => 'bi-check2-circle', 'variant' => 'success', 'note' => '74% tingkat penyelesaian']
];

$recentComplaints = [
    ['tanggal' => '2025-01-12 09:15', 'kode' => 'KEL-202501-001', 'pelanggan' => 'Anisa Putri / 0812-9001-2233', 'kategori' => 'Jaringan', 'status' => 'Open', 'prioritas' => 'High'],
    ['tanggal' => '2025-01-12 08:40', 'kode' => 'KEL-202501-002', 'pelanggan' => 'Budi Santoso / 0813-7765-9900', 'kategori' => 'Tagihan', 'status' => 'On Progress', 'prioritas' => 'Medium'],
    ['tanggal' => '2025-01-11 16:20', 'kode' => 'KEL-202501-003', 'pelanggan' => 'Citra Dewi / 0812-4455-8877', 'kategori' => 'Produk', 'status' => 'Pending', 'prioritas' => 'Low'],
    ['tanggal' => '2025-01-11 15:05', 'kode' => 'KEL-202501-004', 'pelanggan' => 'Dimas Aditya / 0811-7788-9900', 'kategori' => 'Layanan Data', 'status' => 'Solved', 'prioritas' => 'High'],
    ['tanggal' => '2025-01-11 14:40', 'kode' => 'KEL-202501-005', 'pelanggan' => 'Eka Lestari / 0812-1111-6677', 'kategori' => 'Promo', 'status' => 'Closed', 'prioritas' => 'Medium'],
];

ob_start();
?>
<div class="card border-0 mb-4">
    <div class="card-body">
        <form class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Periode Dari</label>
                <input type="date" class="form-control" value="2025-01-05">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai</label>
                <input type="date" class="form-control" value="2025-01-12">
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
            <div class="col-md-2 d-grid">
                <button class="btn btn-danger btn-lg" type="button">Terapkan Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    <?php foreach ($stats as $stat): ?>
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="icon-wrapper bg-<?= $stat['variant'] ?> bg-opacity-10 text-<?= $stat['variant'] ?>">
                            <i class="bi <?= $stat['icon'] ?>"></i>
                        </div>
                        <span class="badge badge-soft"><?= $stat['note'] ?></span>
                    </div>
                    <h4 class="fw-bold mb-1"><?= $stat['value'] ?></h4>
                    <div class="text-muted"><?= htmlspecialchars($stat['label']) ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mt-1">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="card-title mb-0">Jumlah Keluhan per Kategori</h6>
                    <span class="text-muted small">7 Kategori</span>
                </div>
                <canvas id="kategoriChart" height="260"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="card-title mb-0">Tren Keluhan per Hari</h6>
                    <span class="text-muted small">1 Minggu terakhir</span>
                </div>
                <canvas id="trendChart" height="260"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="card-title mb-0">Keluhan Terbaru</h6>
            <a href="#" class="btn btn-outline-danger btn-sm">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tanggal Lapor</th>
                        <th>Kode Keluhan</th>
                        <th>Pelanggan</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Prioritas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentComplaints as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['tanggal']) ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($item['kode']) ?></td>
                            <td><?= htmlspecialchars($item['pelanggan']) ?></td>
                            <td><?= htmlspecialchars($item['kategori']) ?></td>
                            <td><span class="badge status-badge <?= str_replace(' ', '', $item['status']) ?>"><?= htmlspecialchars($item['status']) ?></span></td>
                            <td><span class="badge priority-badge <?= $item['prioritas'] ?>"><?= htmlspecialchars($item['prioritas']) ?></span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary">Detail</button>
                                    <button class="btn btn-outline-danger">Tindak</button>
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
