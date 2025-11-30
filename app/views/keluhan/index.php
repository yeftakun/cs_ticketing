<?php
$pageTitle = 'Daftar Keluhan';
$subtitle = 'Pantau seluruh keluhan pelanggan dan lakukan tindakan cepat';
$breadcrumbs = ['Home' => '?page=dashboard', 'Keluhan' => null];
$activeMenu = 'keluhan';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h6 class="text-muted mb-0">Kelola keluhan masuk</h6>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#exportModal">
            <i class="bi bi-download me-1"></i> Export
        </button>
        <a href="?page=keluhan-create" class="btn btn-danger"><i class="bi bi-plus-lg me-1"></i> Tambah Keluhan</a>
    </div>
</div>

<div class="card border-0 mb-3">
    <div class="card-body">
        <form class="row gy-2 gx-3" method="get">
            <input type="hidden" name="page" value="keluhan">
            <div class="col-md-4">
                <label class="form-label">Cari</label>
                <input type="text" name="q" class="form-control" placeholder="Cari kode keluhan / no HP / nama pelanggan" value="<?= htmlspecialchars($filters['q'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Kategori</label>
                <select class="form-select" name="kategori">
                    <option value="">Semua</option>
                    <?php foreach ($kategoriList as $opt): ?>
                        <option value="<?= (int)$opt['id'] ?>" <?= ($filters['kategori'] ?? '') == $opt['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($opt['nama_kategori']) ?>
                        </option>
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
            <div class="col-md-2">
                <label class="form-label">Prioritas</label>
                <select class="form-select" name="prioritas">
                    <option value="">Semua</option>
                    <?php foreach ($prioritasList as $opt): ?>
                        <option value="<?= htmlspecialchars($opt) ?>" <?= ($filters['prioritas'] ?? '') === $opt ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Channel</label>
                <select class="form-select" name="channel">
                    <option value="">Semua</option>
                    <?php foreach ($channelList as $opt): ?>
                        <option value="<?= htmlspecialchars($opt) ?>" <?= ($filters['channel'] ?? '') === $opt ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Dari</label>
                <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($filters['from'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sampai</label>
                <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($filters['to'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sort 1</label>
                <select class="form-select" name="sort1">
                    <option value="tanggal_lapor" <?= ($filters['sort1'] ?? ($filters['sort'] ?? 'tanggal_lapor')) === 'tanggal_lapor' ? 'selected' : '' ?>>Tanggal</option>
                    <option value="kode_keluhan" <?= ($filters['sort1'] ?? '') === 'kode_keluhan' ? 'selected' : '' ?>>Kode</option>
                    <option value="kategori" <?= ($filters['sort1'] ?? '') === 'kategori' ? 'selected' : '' ?>>Kategori</option>
                    <option value="status_keluhan" <?= ($filters['sort1'] ?? '') === 'status_keluhan' ? 'selected' : '' ?>>Status</option>
                    <option value="prioritas" <?= ($filters['sort1'] ?? '') === 'prioritas' ? 'selected' : '' ?>>Prioritas</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Arah 1</label>
                <select class="form-select" name="dir1">
                    <option value="desc" <?= (($filters['dir1'] ?? ($filters['dir'] ?? 'desc')) === 'desc') ? 'selected' : '' ?>>DESC</option>
                    <option value="asc" <?= (($filters['dir1'] ?? '') === 'asc') ? 'selected' : '' ?>>ASC</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Sort 2</label>
                <select class="form-select" name="sort2">
                    <option value="">(Tidak ada)</option>
                    <option value="tanggal_lapor" <?= ($filters['sort2'] ?? '') === 'tanggal_lapor' ? 'selected' : '' ?>>Tanggal</option>
                    <option value="kode_keluhan" <?= ($filters['sort2'] ?? '') === 'kode_keluhan' ? 'selected' : '' ?>>Kode</option>
                    <option value="kategori" <?= ($filters['sort2'] ?? '') === 'kategori' ? 'selected' : '' ?>>Kategori</option>
                    <option value="status_keluhan" <?= ($filters['sort2'] ?? '') === 'status_keluhan' ? 'selected' : '' ?>>Status</option>
                    <option value="prioritas" <?= ($filters['sort2'] ?? '') === 'prioritas' ? 'selected' : '' ?>>Prioritas</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Arah 2</label>
                <select class="form-select" name="dir2">
                    <option value="desc" <?= (($filters['dir2'] ?? '') === 'desc') ? 'selected' : '' ?>>DESC</option>
                    <option value="asc" <?= (($filters['dir2'] ?? '') === 'asc') ? 'selected' : '' ?>>ASC</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2 align-items-end">
                <button class="btn btn-danger flex-fill" type="submit">Filter</button>
                <a class="btn btn-outline-secondary" href="?page=keluhan">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($_GET['info'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['info']) ?></div>
        <?php endif; ?>
        <form class="mb-3" method="post" action="?page=keluhan" id="bulk-form">
            <input type="hidden" name="action" value="bulk-status">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label mb-0">Status Baru (bulk)</label>
                    <select class="form-select" name="status_baru">
                        <?php foreach ($statusList as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label mb-0">Catatan</label>
                    <input type="text" name="catatan" class="form-control" placeholder="Catatan wajib diisi saat bulk update">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-outline-danger flex-fill" type="submit">Update Status Terpilih</button>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="check-all"></th>
                        <?php
                        $buildSort = function ($key) use ($filters) {
                            $current = $filters['sort1'] ?? ($filters['sort'] ?? 'tanggal_lapor');
                            $dir1 = $filters['dir1'] ?? ($filters['dir'] ?? 'desc');
                            $nextDir = ($current === $key && $dir1 === 'asc') ? 'desc' : 'asc';
                            $params = array_merge($_GET, ['page' => 'keluhan', 'sort1' => $key, 'dir1' => $nextDir]);
                            return '?' . http_build_query($params);
                        };
                        ?>
                        <th><a class="text-decoration-none" href="<?= $buildSort('tanggal_lapor') ?>">Tanggal Lapor</a></th>
                        <th><a class="text-decoration-none" href="<?= $buildSort('kode_keluhan') ?>">Kode Keluhan</a></th>
                        <th>Pelanggan</th>
                        <th><a class="text-decoration-none" href="<?= $buildSort('kategori') ?>">Kategori</a></th>
                        <th>Channel</th>
                        <th><a class="text-decoration-none" href="<?= $buildSort('status_keluhan') ?>">Status</a></th>
                        <th><a class="text-decoration-none" href="<?= $buildSort('prioritas') ?>">Prioritas</a></th>
                        <th>Petugas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($complaints as $row): ?>
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="<?= (int)$row['id'] ?>" class="row-check"></td>
                            <td><?= htmlspecialchars($row['tanggal_lapor']) ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($row['kode_keluhan']) ?></td>
                            <td><?= htmlspecialchars(($row['nama_pelanggan'] ?? '-') . ' / ' . ($row['no_hp'] ?? '-')) ?></td>
                            <td><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['channel']) ?></td>
                            <td><span class="badge status-badge <?= str_replace(' ', '', $row['status_keluhan']) ?>"><?= htmlspecialchars($row['status_keluhan']) ?></span></td>
                            <td><span class="badge priority-badge <?= $row['prioritas'] ?>"><?= htmlspecialchars($row['prioritas']) ?></span></td>
                            <td><?= htmlspecialchars($row['petugas'] ?? '-') ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-outline-secondary" href="?page=keluhan-show&id=<?= (int)$row['id'] ?>">Detail</a>
                                    <a class="btn btn-outline-danger" href="?page=keluhan-edit&id=<?= (int)$row['id'] ?>">Update</a>
                                    <button class="btn btn-outline-warning" type="button" data-bs-toggle="modal" data-bs-target="#quickStatusModal" data-id="<?= (int)$row['id'] ?>" data-kode="<?= htmlspecialchars($row['kode_keluhan']) ?>">Status</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($complaints)): ?>
                        <tr><td colspan="9" class="text-center text-muted">Belum ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <nav class="mt-3">
            <ul class="pagination justify-content-end mb-0">
                <?php
                $currentPage = max(1, (int)($_GET['p'] ?? 1));
                $totalPages = $totalPages ?? 1;
                $prev = $currentPage - 1;
                $next = $currentPage + 1;
                $start = max(1, $currentPage - 2);
                $end = min($totalPages, $currentPage + 2);
                ?>
                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $currentPage <= 1 ? '#' : '?page=keluhan&p=' . $prev ?>">Prev</a>
                </li>
                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>"><a class="page-link" href="?page=keluhan&p=<?= $i ?>"><?= $i ?></a></li>
                <?php endfor; ?>
                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $currentPage >= $totalPages ? '#' : '?page=keluhan&p=' . $next ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>
<!-- Modal Quick Status -->
<div class="modal fade" id="quickStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="post" action="?page=keluhan">
            <input type="hidden" name="action" value="quick-status">
            <input type="hidden" name="id" id="qs-id">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Keluhan <span id="qs-kode" class="text-muted"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Status Baru</label>
                    <select class="form-select" name="status_baru">
                        <?php foreach ($statusList as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" name="catatan" rows="2" placeholder="Catatan singkat progres..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="get" action="?page=keluhan">
            <?php foreach ($filters as $k => $v): ?>
                <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
            <?php endforeach; ?>
            <input type="hidden" name="page" value="keluhan">
            <input type="hidden" name="export" value="csv">
            <div class="modal-header">
                <h5 class="modal-title">Export Keluhan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Export data keluhan sesuai filter saat ini.</p>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="export_type" value="csv" id="expCsv" checked>
                    <label class="form-check-label" for="expCsv">CSV</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="export_type" value="excel" id="expXlsx">
                    <label class="form-check-label" for="expXlsx">Excel (.xlsx)</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Export</button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('quickStatusModal');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var kode = button.getAttribute('data-kode');
        modal.querySelector('#qs-id').value = id || '';
        modal.querySelector('#qs-kode').textContent = kode ? '(' + kode + ')' : '';
    });
});

// Bulk checkbox handling
(function() {
    const checkAll = document.getElementById('check-all');
    const bulkForm = document.getElementById('bulk-form');

    const sync = () => {
        if (!checkAll) return;
        const boxes = Array.from(document.querySelectorAll('.row-check'));
        if (boxes.length === 0) return;
        const allChecked = boxes.every((c) => c.checked);
        checkAll.checked = allChecked;
    };

    if (checkAll) {
        checkAll.addEventListener('change', () => {
            const boxes = document.querySelectorAll('.row-check');
            boxes.forEach((c) => { c.checked = checkAll.checked; });
        });
    }
    document.querySelectorAll('.row-check').forEach((c) => c.addEventListener('change', sync));

    // Submit handler to ensure selected IDs stay in the bulk form
    if (bulkForm) {
        bulkForm.addEventListener('submit', () => {
            // Remove existing hidden ids
            bulkForm.querySelectorAll('input[name="ids[]"]').forEach((el) => el.remove());
            document.querySelectorAll('.row-check:checked').forEach((c) => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'ids[]';
                hidden.value = c.value;
                bulkForm.appendChild(hidden);
            });
        });
    }
})();
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
