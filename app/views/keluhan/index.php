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
    <a href="?page=keluhan-create" class="btn btn-danger"><i class="bi bi-plus-lg me-1"></i> Tambah Keluhan</a>
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
            <div class="col-md-2 d-flex gap-2 align-items-end">
                <button class="btn btn-danger flex-fill" type="submit">Filter</button>
                <a class="btn btn-outline-secondary" href="?page=keluhan">Reset</a>
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
                ?>
                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $currentPage <= 1 ? '#' : '?page=keluhan&p=' . $prev ?>">Prev</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>"><a class="page-link" href="?page=keluhan&p=<?= $i ?>"><?= $i ?></a></li>
                <?php endfor; ?>
                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $currentPage >= $totalPages ? '#' : '?page=keluhan&p=' . $next ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
