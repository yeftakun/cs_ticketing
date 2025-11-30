<?php
$pageTitle = 'Data Pelanggan';
$subtitle = 'Monitoring pelanggan dan riwayat keluhan';
$breadcrumbs = ['Home' => '?page=dashboard', 'Pelanggan' => null];
$activeMenu = 'pelanggan';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h6 class="text-muted mb-0">Kelola data pelanggan</h6>
    </div>
    <a href="?page=pelanggan-form" class="btn btn-danger"><i class="bi bi-plus-lg me-1"></i> Tambah Pelanggan</a>
</div>

<div class="card border-0 mb-3">
    <div class="card-body">
        <form class="row gy-2 gx-3 align-items-end" method="get" id="pelanggan-filter-form">
            <input type="hidden" name="page" value="pelanggan">
            <div class="col-md-6">
                <label class="form-label">Cari Pelanggan</label>
                <input type="text" name="q" class="form-control" placeholder="Cari nama / no HP / kota" value="<?= htmlspecialchars($filters['q'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Kota</label>
                <select class="form-select" name="kota">
                    <option value="">Semua</option>
                    <?php
                    $kotaList = array_unique(array_filter(array_map(fn($p) => $p['kota'] ?? '', $pelanggan)));
                    sort($kotaList);
                    foreach ($kotaList as $kotaOpt): ?>
                        <option value="<?= htmlspecialchars($kotaOpt) ?>" <?= ($filters['kota'] ?? '') === $kotaOpt ? 'selected' : '' ?>><?= htmlspecialchars($kotaOpt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-center gap-2">
                <span class="text-muted small">Filter otomatis</span>
                <a class="btn btn-outline-secondary" href="?page=pelanggan">Reset</a>
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
                            <td class="fw-semibold"><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                            <td><?= htmlspecialchars($row['no_hp']) ?></td>
                            <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['kota'] ?? '-') ?></td>
                            <td><span class="fw-semibold"><?= (int)($row['jumlah_keluhan'] ?? 0) ?></span> keluhan</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-outline-secondary" href="?page=keluhan&pelanggan=<?= (int)$row['id'] ?>">Keluhan</a>
                                    <a class="btn btn-outline-danger" href="?page=pelanggan-form&id=<?= (int)$row['id'] ?>">Edit</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($pelanggan)): ?>
                        <tr><td colspan="6" class="text-center text-muted">Belum ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
(function() {
    const form = document.getElementById('pelanggan-filter-form');
    if (!form) return;
    const debounce = (fn, delay = 400) => {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), delay);
        };
    };
    const submit = debounce(() => form.submit());
    form.querySelectorAll('input, select').forEach((el) => {
        el.addEventListener('change', submit);
        if (el.tagName === 'INPUT' && el.type === 'text') {
            el.addEventListener('input', submit);
        }
    });
})();
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
