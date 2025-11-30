<?php
$pageTitle = 'Master Kategori Keluhan';
$subtitle = 'Kelola kategori dan deskripsi singkat';
$breadcrumbs = ['Home' => '?page=dashboard', 'Admin' => '#', 'Kategori Keluhan' => null];
$activeMenu = 'kategori';
ob_start();
?>
<div class="card border-0 mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h6 class="text-muted mb-0"><?= $editKategori ? 'Edit Kategori' : 'Tambah Kategori' ?></h6>
            </div>
        </div>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form class="row g-3" method="post" action="?page=admin-kategori<?= $editKategori ? '&id=' . (int)$editKategori['id'] : '' ?>">
            <input type="hidden" name="id" value="<?= $editKategori['id'] ?? '' ?>">
            <div class="col-md-4">
                <label class="form-label">Nama Kategori</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($_POST['nama'] ?? $editKategori['nama_kategori'] ?? '') ?>" required>
            </div>
            <div class="col-md-8">
                <label class="form-label">Deskripsi</label>
                <input type="text" name="deskripsi" class="form-control" value="<?= htmlspecialchars($_POST['deskripsi'] ?? $editKategori['deskripsi'] ?? '') ?>">
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-danger" type="submit"><?= $editKategori ? 'Simpan Perubahan' : 'Tambah' ?></button>
                <?php if ($editKategori): ?>
                    <a class="btn btn-outline-secondary" href="?page=admin-kategori">Batal Edit</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Deskripsi Singkat</th>
                        <th>Jumlah Keluhan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kategori as $row): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($row['nama_kategori']) ?></td>
                            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                            <td><span class="badge bg-light text-dark"><?= htmlspecialchars($row['jumlah']) ?></span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-outline-danger" href="?page=admin-kategori&id=<?= (int)$row['id'] ?>">Edit</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($kategori)): ?>
                        <tr><td colspan="4" class="text-center text-muted">Belum ada kategori.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
