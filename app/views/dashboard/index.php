<?php
$pageTitle = 'Dashboard Keluhan';
$subtitle = 'Ringkasan keluhan pelanggan berdasarkan data terbaru';
$breadcrumbs = ['Home' => '?page=dashboard', 'Dashboard' => null];
$activeMenu = 'dashboard';
ob_start();
?>
<script>
    window.dashboardData = {
        bar: {
            labels: <?= json_encode($barLabels ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            values: <?= json_encode($barValues ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>
        },
        trend: {
            labels: <?= json_encode($trendLabels ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            values: <?= json_encode($trendValues ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            solved: <?= json_encode($trendSolvedValues ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>
        }
    };
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('dashboard-submit');
    const form = btn?.closest('form');
    if (form && btn) {
        form.addEventListener('submit', () => {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memuat...';
        });
    }
});
</script>
<div class="card border-0 mb-4">
    <div class="card-body">
        <form class="row g-3 align-items-end" method="get">
            <input type="hidden" name="page" value="dashboard">
            <div class="col-md-3">
                <label class="form-label">Periode Dari</label>
                <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($filters['from'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai</label>
                <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($filters['to'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Kategori</label>
                <select class="form-select" name="kategori">
                    <option value="">Semua</option>
                    <?php foreach ($kategoriList as $opt): ?>
                        <option value="<?= (int)$opt['id'] ?>" <?= ($filters['kategori'] ?? '') == $opt['id'] ? 'selected' : '' ?>><?= htmlspecialchars($opt['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">Semua</option>
                    <?php foreach ($statusList as $opt): ?>
                        <option value="<?= htmlspecialchars($opt) ?>" <?= ($filters['status'] ?? '') === $opt ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <div class="d-flex gap-2">
                    <button class="btn btn-danger btn-lg flex-fill" id="dashboard-submit" type="submit">Terapkan Filter</button>
                    <a class="btn btn-outline-secondary" href="?page=dashboard">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    <?php
    $stats = [
        ['label' => 'Total Keluhan Hari Ini', 'value' => $statToday ?? 0, 'icon' => 'bi-activity', 'variant' => 'danger', 'note' => $statNotes['today'] ?? ''],
        ['label' => 'Total Keluhan Bulan Ini', 'value' => $statMonth ?? 0, 'icon' => 'bi-graph-up', 'variant' => 'primary', 'note' => $statNotes['month'] ?? ''],
        ['label' => 'Keluhan Open', 'value' => $statOpen ?? 0, 'icon' => 'bi-exclamation-circle', 'variant' => 'warning', 'note' => $statNotes['open'] ?? ''],
        ['label' => 'Keluhan Solved', 'value' => $statSolved ?? 0, 'icon' => 'bi-check2-circle', 'variant' => 'success', 'note' => $statNotes['solved'] ?? ''],
    ];
    foreach ($stats as $stat): ?>
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
                    <span class="text-muted small"><?= count($barLabels ?? []) ?> Kategori</span>
                </div>
                <div class="text-muted small empty-state d-none">Data belum tersedia.</div>
                <canvas id="kategoriChart" height="260"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="card-title mb-0">Tren Keluhan per Hari</h6>
                    <span class="text-muted small">Periode <?= htmlspecialchars($filters['from'] ?? '') ?> - <?= htmlspecialchars($filters['to'] ?? '') ?></span>
                </div>
                <div class="text-muted small empty-state d-none">Data belum tersedia.</div>
                <canvas id="trendChart" height="260"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="card-title mb-0">Keluhan Terbaru</h6>
            <?php
            $keluhanQuery = array_filter([
                'page' => 'keluhan',
                'kategori' => $filters['kategori'] ?? '',
                'status' => $filters['status'] ?? '',
                'from' => $filters['from'] ?? '',
                'to' => $filters['to'] ?? '',
            ]);
            ?>
            <a href="?<?= http_build_query($keluhanQuery) ?>" class="btn btn-outline-danger btn-sm">Lihat Semua</a>
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
                            <td><?= htmlspecialchars($item['tanggal_lapor']) ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($item['kode_keluhan']) ?></td>
                            <td><?= htmlspecialchars(($item['nama_pelanggan'] ?? '-') . ' / ' . ($item['no_hp'] ?? '-')) ?></td>
                            <td><?= htmlspecialchars($item['nama_kategori'] ?? '-') ?></td>
                            <td><span class="badge status-badge <?= str_replace(' ', '', $item['status_keluhan']) ?>"><?= htmlspecialchars($item['status_keluhan']) ?></span></td>
                            <td><span class="badge priority-badge <?= $item['prioritas'] ?>"><?= htmlspecialchars($item['prioritas']) ?></span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-outline-secondary" href="?page=keluhan-show&id=<?= (int)$item['id'] ?>">Detail</a>
                                    <a class="btn btn-outline-danger" href="?page=keluhan-edit&id=<?= (int)$item['id'] ?>">Tindak</a>
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
