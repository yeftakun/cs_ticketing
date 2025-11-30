<?php
$pageTitle = 'Master Kategori Keluhan';
$subtitle = 'Kelola kategori dan deskripsi singkat';
$breadcrumbs = ['Home' => '#', 'Admin' => '#', 'Kategori Keluhan' => null];
$activeMenu = 'kategori';

$kategori = [
    ['nama' => 'Jaringan', 'deskripsi' => 'Gangguan sinyal, telepon, SMS, data', 'jumlah' => 142],
    ['nama' => 'Tagihan', 'deskripsi' => 'Kesalahan tagihan, paket tidak sesuai', 'jumlah' => 88],
    ['nama' => 'Layanan Data', 'deskripsi' => 'Kecepatan internet, kuota, FUP', 'jumlah' => 64],
    ['nama' => 'Promo', 'deskripsi' => 'Promo tidak masuk, syarat ketentuan', 'jumlah' => 34],
];

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h6 class="text-muted mb-0">Daftar kategori aktif</h6>
    </div>
    <button class="btn btn-danger"><i class="bi bi-plus-lg me-1"></i> Tambah Kategori</button>
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
                            <td class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                            <td><span class="badge bg-light text-dark"><?= htmlspecialchars($row['jumlah']) ?></span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-danger">Edit</button>
                                    <button class="btn btn-outline-secondary">Hapus</button>
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
