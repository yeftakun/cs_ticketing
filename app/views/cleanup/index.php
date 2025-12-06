<?php
$pageTitle = 'Pembersihan Data';
$subtitle = 'Kelola data yang dihapus (soft delete) dan eksekusi hapus permanen dengan aman';
$breadcrumbs = ['Home' => '?page=dashboard', 'Pembersihan Data' => null];
$activeMenu = 'cleanup';
ob_start();
$allEmpty = empty($deletedKeluhan) && empty($deletedPelanggan) && empty($deletedKategori) && empty($deletedUsers);
?>
<div class="alert alert-warning">
    Mode soft delete aktif. Data di bawah ini tidak muncul di halaman utama. Pulihkan jika salah hapus, atau hapus permanen (akan menghapus anak terlebih dahulu sesuai peringatan).
</div>
<?php if (!empty($cleanupError)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($cleanupError) ?></div>
<?php endif; ?>
<?php if (!empty($cleanupInfo)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($cleanupInfo) ?></div>
<?php endif; ?>

<?php if ($allEmpty): ?>
    <div class="card">
        <div class="card-body text-center text-muted">
            Tidak ada data soft delete untuk dibersihkan.
        </div>
    </div>
<?php endif; ?>

<?php
$renderWarnings = function (array $warnings): string {
    if (empty($warnings)) {
        return '<span class="text-muted small">-</span>';
    }
    $items = array_map(fn($w) => '<li>' . htmlspecialchars($w) . '</li>', $warnings);
    return '<ul class="mb-0 small text-muted">' . implode('', $items) . '</ul>';
};
?>

<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Keluhan Terhapus</h6>
                    <small class="text-muted">Soft delete pada keluhan</small>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($deletedKeluhan)): ?>
                    <div class="text-muted">Tidak ada keluhan soft delete.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Pelanggan</th>
                                    <th>Status</th>
                                    <th>Dihapus Pada</th>
                                    <th>Relasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deletedKeluhan as $row): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= htmlspecialchars($row['kode_keluhan']) ?></td>
                                        <td><?= htmlspecialchars(($row['nama_pelanggan'] ?? '-') . ' / ' . ($row['no_hp'] ?? '-')) ?></td>
                                        <td><span class="badge bg-light text-dark"><?= htmlspecialchars($row['status_keluhan']) ?></span></td>
                                        <td><?= htmlspecialchars($row['deleted_at']) ?></td>
                                        <td><?= $renderWarnings($row['warnings'] ?? []) ?></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <form method="post" action="?page=cleanup" class="d-inline">
                                                    <input type="hidden" name="action" value="restore">
                                                    <input type="hidden" name="entity" value="keluhan">
                                                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                                    <button class="btn btn-outline-secondary btn-sm" type="submit">Pulihkan</button>
                                                </form>
                                                <button class="btn btn-outline-danger btn-sm" type="button"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#confirmDeleteModal"
                                                    data-entity="keluhan"
                                                    data-id="<?= (int)$row['id'] ?>"
                                                    data-label="Keluhan <?= htmlspecialchars($row['kode_keluhan']) ?>"
                                                    data-warnings='<?= htmlspecialchars(json_encode($row['warnings'] ?? [])) ?>'>
                                                    Hapus Permanen
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Pelanggan Terhapus</h6>
                    <small class="text-muted">Soft delete pelanggan</small>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($deletedPelanggan)): ?>
                    <div class="text-muted">Tidak ada pelanggan soft delete.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Kontak</th>
                                    <th>Dihapus Pada</th>
                                    <th>Relasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deletedPelanggan as $row): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                        <td><?= htmlspecialchars($row['no_hp'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['deleted_at']) ?></td>
                                        <td><?= $renderWarnings($row['warnings'] ?? []) ?></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <form method="post" action="?page=cleanup" class="d-inline">
                                                    <input type="hidden" name="action" value="restore">
                                                    <input type="hidden" name="entity" value="pelanggan">
                                                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                                    <button class="btn btn-outline-secondary btn-sm" type="submit">Pulihkan</button>
                                                </form>
                                                <button class="btn btn-outline-danger btn-sm" type="button"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#confirmDeleteModal"
                                                    data-entity="pelanggan"
                                                    data-id="<?= (int)$row['id'] ?>"
                                                    data-label="Pelanggan <?= htmlspecialchars($row['nama_pelanggan']) ?>"
                                                    data-warnings='<?= htmlspecialchars(json_encode($row['warnings'] ?? [])) ?>'>
                                                    Hapus Permanen
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Kategori Terhapus</h6>
                    <small class="text-muted">Soft delete master kategori</small>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($deletedKategori)): ?>
                    <div class="text-muted">Tidak ada kategori soft delete.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Dihapus Pada</th>
                                    <th>Relasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deletedKategori as $row): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= htmlspecialchars($row['nama_kategori']) ?></td>
                                        <td><?= htmlspecialchars($row['deleted_at']) ?></td>
                                        <td><?= $renderWarnings($row['warnings'] ?? []) ?></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <form method="post" action="?page=cleanup" class="d-inline">
                                                    <input type="hidden" name="action" value="restore">
                                                    <input type="hidden" name="entity" value="kategori">
                                                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                                    <button class="btn btn-outline-secondary btn-sm" type="submit">Pulihkan</button>
                                                </form>
                                                <button class="btn btn-outline-danger btn-sm" type="button"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#confirmDeleteModal"
                                                    data-entity="kategori"
                                                    data-id="<?= (int)$row['id'] ?>"
                                                    data-label="Kategori <?= htmlspecialchars($row['nama_kategori']) ?>"
                                                    data-warnings='<?= htmlspecialchars(json_encode($row['warnings'] ?? [])) ?>'>
                                                    Hapus Permanen
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">User Terhapus</h6>
                    <small class="text-muted">Soft delete akun internal</small>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($deletedUsers)): ?>
                    <div class="text-muted">Tidak ada user soft delete.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Dihapus Pada</th>
                                    <th>Relasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deletedUsers as $row): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></td>
                                        <td><?= htmlspecialchars($row['username']) ?></td>
                                        <td><span class="badge bg-light text-dark text-uppercase"><?= htmlspecialchars($row['role']) ?></span></td>
                                        <td><?= htmlspecialchars($row['deleted_at']) ?></td>
                                        <td><?= $renderWarnings($row['warnings'] ?? []) ?></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <form method="post" action="?page=cleanup" class="d-inline">
                                                    <input type="hidden" name="action" value="restore">
                                                    <input type="hidden" name="entity" value="user">
                                                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                                    <button class="btn btn-outline-secondary btn-sm" type="submit">Pulihkan</button>
                                                </form>
                                                <button class="btn btn-outline-danger btn-sm" type="button"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#confirmDeleteModal"
                                                    data-entity="user"
                                                    data-id="<?= (int)$row['id'] ?>"
                                                    data-label="User <?= htmlspecialchars($row['username']) ?>"
                                                    data-warnings='<?= htmlspecialchars(json_encode($row['warnings'] ?? [])) ?>'>
                                                    Hapus Permanen
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal hapus permanen -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="post" action="?page=cleanup">
            <input type="hidden" name="action" value="delete-permanent">
            <input type="hidden" name="entity" id="confirm-entity">
            <input type="hidden" name="id" id="confirm-id">
            <div class="modal-header">
                <h5 class="modal-title" id="confirm-title">Konfirmasi Hapus Permanen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Data berikut akan dihapus permanen bersama relasi di bawah (urutan dari anak ke induk):</p>
                <ul class="small mb-0" id="confirm-warnings"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus Permanen</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('confirmDeleteModal');
    if (!modalEl) return;
    modalEl.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        if (!btn) return;
        const entity = btn.getAttribute('data-entity') || '';
        const id = btn.getAttribute('data-id') || '';
        const label = btn.getAttribute('data-label') || 'Konfirmasi';
        const warningsRaw = btn.getAttribute('data-warnings') || '[]';
        let warnings = [];
        try {
            warnings = JSON.parse(warningsRaw);
        } catch (e) {
            warnings = [];
        }

        modalEl.querySelector('#confirm-entity').value = entity;
        modalEl.querySelector('#confirm-id').value = id;
        modalEl.querySelector('#confirm-title').textContent = label;

        const list = modalEl.querySelector('#confirm-warnings');
        list.innerHTML = '';
        if (!warnings || warnings.length === 0) {
            const li = document.createElement('li');
            li.textContent = 'Tidak ada relasi lain.';
            list.appendChild(li);
        } else {
            warnings.forEach((w) => {
                const li = document.createElement('li');
                li.textContent = w;
                list.appendChild(li);
            });
        }
    });
});
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
