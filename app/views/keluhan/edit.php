<?php
$pageTitle = 'Edit Keluhan';
$subtitle = 'Perbarui informasi keluhan pelanggan';
$breadcrumbs = ['Home' => '#', 'Keluhan' => '#', 'Edit' => null];
$activeMenu = 'keluhan';

$keluhan = [
    'kode' => 'KEL-202501-010',
    'tanggal_lapor' => '2025-01-12 09:15',
    'kategori' => 'Jaringan',
    'channel' => 'Call Center',
    'prioritas' => 'High',
    'deskripsi' => 'Pelanggan melaporkan sinyal hilang sejak pagi di area Sudirman, Jakarta.'
];

$kategoriOptions = ['Jaringan', 'Tagihan', 'Layanan Data', 'Promo', 'Perangkat'];
$channelOptions = ['Call Center', 'Grapari', 'WhatsApp', 'Aplikasi', 'Media Sosial', 'Email'];
$prioritasOptions = ['Low', 'Medium', 'High', 'Critical'];

ob_start();
?>
<div class="card">
    <div class="card-body">
        <h6 class="card-title mb-3">Data Keluhan</h6>
        <form class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Kode Keluhan</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($keluhan['kode']) ?>" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Lapor</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($keluhan['tanggal_lapor']) ?>" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Kategori Keluhan</label>
                <select class="form-select">
                    <?php foreach ($kategoriOptions as $option): ?>
                        <option <?= $option === $keluhan['kategori'] ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Channel</label>
                <select class="form-select">
                    <?php foreach ($channelOptions as $option): ?>
                        <option <?= $option === $keluhan['channel'] ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Prioritas</label>
                <select class="form-select">
                    <?php foreach ($prioritasOptions as $option): ?>
                        <option <?= $option === $keluhan['prioritas'] ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi Keluhan</label>
                <textarea class="form-control" rows="4"><?= htmlspecialchars($keluhan['deskripsi']) ?></textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-danger" type="button">Simpan Perubahan</button>
                <a class="btn btn-secondary" href="#">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
