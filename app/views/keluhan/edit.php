<?php
$pageTitle = 'Edit Keluhan';
$subtitle = 'Perbarui informasi keluhan pelanggan';
$breadcrumbs = ['Home' => '?page=dashboard', 'Keluhan' => '?page=keluhan', 'Edit' => null];
$activeMenu = 'keluhan';
ob_start();
?>
<div class="card">
    <div class="card-body">
        <h6 class="card-title mb-3">Data Keluhan</h6>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form class="row g-3" method="post" action="?page=keluhan-edit&id=<?= (int)$keluhan['id'] ?>">
            <div class="col-md-6">
                <label class="form-label">Kode Keluhan</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($keluhan['kode_keluhan']) ?>" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Lapor</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($keluhan['tanggal_lapor']) ?>" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Kategori Keluhan</label>
                <select class="form-select" name="kategori_id">
                    <?php foreach ($kategoriList as $option): ?>
                        <option value="<?= (int)$option['id'] ?>" <?= ($option['id'] == $keluhan['kategori_id']) ? 'selected' : '' ?>><?= htmlspecialchars($option['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Channel</label>
                <select class="form-select" name="channel">
                    <?php foreach ($channelList as $option): ?>
                        <option value="<?= htmlspecialchars($option) ?>" <?= ($option === $keluhan['channel']) ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Prioritas</label>
                <select class="form-select" name="prioritas">
                    <?php foreach ($prioritasList as $option): ?>
                        <option value="<?= htmlspecialchars($option) ?>" <?= ($option === $keluhan['prioritas']) ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi Keluhan</label>
                <textarea class="form-control" name="deskripsi" rows="4"><?= htmlspecialchars($keluhan['deskripsi_keluhan']) ?></textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-danger" type="submit">Simpan Perubahan</button>
                <a class="btn btn-secondary" href="?page=keluhan-show&id=<?= (int)$keluhan['id'] ?>">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
