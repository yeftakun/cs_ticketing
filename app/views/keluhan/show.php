<?php
$pageTitle = 'Detail Keluhan';
$subtitle = 'Pantau progres dan riwayat penanganan';
$breadcrumbs = ['Home' => '#', 'Keluhan' => '#', 'Detail' => null];
$activeMenu = 'keluhan';

$keluhan = [
    'kode' => 'KEL-202501-010',
    'tanggal_lapor' => '2025-01-12 09:15',
    'tanggal_update' => '2025-01-12 11:30',
    'tanggal_selesai' => null,
    'pelanggan' => 'Rizky Ramadhan',
    'no_hp' => '0812-9988-1122',
    'kota' => 'Jakarta Selatan',
    'kategori' => 'Jaringan',
    'channel' => 'Call Center',
    'status' => 'On Progress',
    'prioritas' => 'Critical',
    'deskripsi' => 'Sinyal hilang total pada area Sudirman sejak pukul 08.00, pelanggan tidak bisa melakukan panggilan maupun data.'
];

$timeline = [
    ['tanggal' => '2025-01-12 09:20', 'status' => 'Open', 'user' => 'Ayu Rahma', 'catatan' => 'Keluhan dicatat oleh agent call center, kode tiket dibuat.'],
    ['tanggal' => '2025-01-12 10:05', 'status' => 'On Progress', 'user' => 'Budi Pratama', 'catatan' => 'Diteruskan ke tim teknis area, pengecekan BTS berlangsung.'],
    ['tanggal' => '2025-01-12 11:30', 'status' => 'On Progress', 'user' => 'Budi Pratama', 'catatan' => 'Ada gangguan listrik di site, koordinasi dengan PLN. Estimasi selesai 14.00.']
];

$statusOptions = ['Open', 'On Progress', 'Pending', 'Solved', 'Closed'];

ob_start();
?>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="card-title">Informasi Pelanggan</h6>
                <p class="mb-1 fw-semibold"><?= htmlspecialchars($keluhan['pelanggan']) ?></p>
                <div class="text-muted mb-2"><?= htmlspecialchars($keluhan['no_hp']) ?></div>
                <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($keluhan['kota']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <h6 class="card-title mb-0">Detail Keluhan</h6>
                    <div class="d-flex gap-2">
                        <span class="badge status-badge <?= str_replace(' ', '', $keluhan['status']) ?> px-3 py-2"><?= htmlspecialchars($keluhan['status']) ?></span>
                        <span class="badge priority-badge <?= $keluhan['prioritas'] ?> px-3 py-2"><?= htmlspecialchars($keluhan['prioritas']) ?></span>
                    </div>
                </div>
                <div class="row mt-3 gy-2">
                    <div class="col-md-6">
                        <div class="text-muted small">Kode Keluhan</div>
                        <div class="fw-semibold"><?= htmlspecialchars($keluhan['kode']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Kategori</div>
                        <div class="fw-semibold"><?= htmlspecialchars($keluhan['kategori']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Channel</div>
                        <div class="fw-semibold"><?= htmlspecialchars($keluhan['channel']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Tanggal Lapor</div>
                        <div class="fw-semibold"><?= htmlspecialchars($keluhan['tanggal_lapor']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Update Terakhir</div>
                        <div class="fw-semibold"><?= htmlspecialchars($keluhan['tanggal_update']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Tanggal Selesai</div>
                        <div class="fw-semibold"><?= $keluhan['tanggal_selesai'] ?: '-' ?></div>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-muted small">Deskripsi Keluhan</div>
                    <p class="mb-0"><?= htmlspecialchars($keluhan['deskripsi']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="card-title mb-0">Timeline Penanganan Keluhan</h6>
            <span class="badge bg-light text-dark"><?= count($timeline) ?> log</span>
        </div>
        <div class="timeline">
            <?php foreach ($timeline as $log): ?>
                <div class="timeline-item">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="fw-semibold"><?= htmlspecialchars($log['tanggal']) ?></div>
                        <span class="badge status-badge <?= str_replace(' ', '', $log['status']) ?>"><?= htmlspecialchars($log['status']) ?></span>
                        <span class="text-muted small">oleh <?= htmlspecialchars($log['user']) ?></span>
                    </div>
                    <div class="text-muted"><?= htmlspecialchars($log['catatan']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <hr>
        <div class="mt-3">
            <h6 class="fw-semibold mb-2">Tambah Log / Update Status</h6>
            <form class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status Baru</label>
                    <select class="form-select">
                        <?php foreach ($statusOptions as $option): ?>
                            <option <?= $option === $keluhan['status'] ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-9">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" rows="2" placeholder="Catatan singkat progres..."></textarea>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-danger" type="button">Simpan</button>
                    <button class="btn btn-outline-secondary" type="button">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
