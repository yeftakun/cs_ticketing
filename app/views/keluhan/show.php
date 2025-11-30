<?php
$pageTitle = 'Detail Keluhan';
$subtitle = 'Pantau progres dan riwayat penanganan';
$breadcrumbs = ['Home' => '?page=dashboard', 'Keluhan' => '?page=keluhan', 'Detail' => null];
$activeMenu = 'keluhan';
ob_start();
?>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="card-title">Informasi Pelanggan</h6>
                <p class="mb-1 fw-semibold"><?= htmlspecialchars($keluhan['nama_pelanggan'] ?? '-') ?></p>
                <div class="text-muted mb-2"><?= htmlspecialchars($keluhan['no_hp'] ?? '-') ?></div>
                <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($keluhan['kota'] ?? '-') ?></div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <h6 class="card-title mb-0">Detail Keluhan</h6>
                    <div class="d-flex gap-2">
                        <span class="badge status-badge <?= str_replace(' ', '', $keluhan['status_keluhan']) ?> px-3 py-2"><?= htmlspecialchars($keluhan['status_keluhan']) ?></span>
                        <span class="badge priority-badge <?= $keluhan['prioritas'] ?> px-3 py-2"><?= htmlspecialchars($keluhan['prioritas']) ?></span>
                    </div>
                </div>
                <div class="row mt-3 gy-2">
                    <div class="col-md-6">
                        <div class="text-muted small">Kode Keluhan</div>
                        <div class="fw-semibold"><?= htmlspecialchars($keluhan['kode_keluhan']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Kategori</div>
                        <div class="fw-semibold"><?= htmlspecialchars($keluhan['nama_kategori'] ?? '-') ?></div>
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
                        <div class="fw-semibold"><?= htmlspecialchars($keluhan['tanggal_update_terakhir']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Tanggal Selesai</div>
                        <div class="fw-semibold"><?= $keluhan['tanggal_selesai'] ?: '-' ?></div>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-muted small">Deskripsi Keluhan</div>
                    <p class="mb-0"><?= htmlspecialchars($keluhan['deskripsi_keluhan']) ?></p>
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
                        <div class="fw-semibold"><?= htmlspecialchars($log['tanggal_log']) ?></div>
                        <span class="badge status-badge <?= str_replace(' ', '', $log['status_log']) ?>"><?= htmlspecialchars($log['status_log']) ?></span>
                        <span class="text-muted small">oleh <?= htmlspecialchars($log['user_nama'] ?? '-') ?></span>
                    </div>
                    <div class="text-muted"><?= htmlspecialchars($log['catatan']) ?></div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($timeline)): ?>
                <div class="text-muted">Belum ada log.</div>
            <?php endif; ?>
        </div>
        <hr>
        <div class="mt-3">
            <h6 class="fw-semibold mb-2">Tambah Log / Update Status</h6>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form class="row g-3" method="post" action="?page=keluhan-show&id=<?= (int)$keluhan['id'] ?>">
                <div class="col-md-3">
                    <label for="status_baru" class="form-label">Status Baru</label>
                    <select class="form-select" id="status_baru" name="status_baru">
                        <?php foreach ($statusList as $option): ?>
                            <option value="<?= htmlspecialchars($option) ?>" <?= $option === $keluhan['status_keluhan'] ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-9">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" name="catatan" rows="2" placeholder="Catatan singkat progres..."><?= htmlspecialchars($_POST['catatan'] ?? '') ?></textarea>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-danger" type="submit">Simpan</button>
                    <a class="btn btn-outline-secondary" href="?page=keluhan">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
