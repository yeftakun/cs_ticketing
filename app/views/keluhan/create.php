<?php
$pageTitle = 'Input Keluhan Baru';
$subtitle = 'Catat keluhan baru untuk ditindaklanjuti';
$breadcrumbs = ['Home' => '#', 'Keluhan' => '#', 'Input Keluhan' => null];
$activeMenu = 'keluhan';

$kategoriOptions = ['Jaringan', 'Tagihan', 'Layanan Data', 'Promo', 'Perangkat'];
$channelOptions = ['Call Center', 'Grapari', 'WhatsApp', 'Aplikasi', 'Media Sosial', 'Email'];
$prioritasOptions = ['Low', 'Medium', 'High', 'Critical'];

ob_start();
?>
<div class="card">
    <div class="card-body">
        <h6 class="card-title mb-3">Detail Keluhan</h6>
        <form class="row g-3">
            <div class="col-md-6">
                <label class="form-label">No HP</label>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="08xx xxxx xxxx" required>
                    <button class="btn btn-outline-secondary" type="button">Cek Pelanggan</button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nama Pelanggan</label>
                <input type="text" class="form-control" placeholder="Nama pelanggan">
            </div>
            <div class="col-md-6">
                <label class="form-label">Kota</label>
                <input type="text" class="form-control" placeholder="Kota domisili">
            </div>
            <div class="col-md-6">
                <label class="form-label">Kategori Keluhan</label>
                <select class="form-select" required>
                    <option value="">Pilih kategori</option>
                    <?php foreach ($kategoriOptions as $option): ?>
                        <option><?= htmlspecialchars($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Channel</label>
                <select class="form-select">
                    <?php foreach ($channelOptions as $option): ?>
                        <option><?= htmlspecialchars($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Prioritas</label>
                <select class="form-select">
                    <?php foreach ($prioritasOptions as $option): ?>
                        <option <?= $option === 'Medium' ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi Keluhan</label>
                <textarea class="form-control" rows="4" placeholder="Jelaskan detail keluhan..."></textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-danger" type="button">Simpan</button>
                <a class="btn btn-secondary" href="#">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
