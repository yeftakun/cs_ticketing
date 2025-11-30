<?php
$pageTitle = 'Input Keluhan Baru';
$subtitle = 'Catat keluhan baru untuk ditindaklanjuti';
$breadcrumbs = ['Home' => '?page=dashboard', 'Keluhan' => '?page=keluhan', 'Input Keluhan' => null];
$activeMenu = 'keluhan';
ob_start();
?>
<div class="card">
    <div class="card-body">
        <h6 class="card-title mb-3">Detail Keluhan</h6>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form class="row g-3" method="post" action="?page=keluhan-create">
            <div class="col-md-6">
                <label class="form-label">No HP</label>
                <div class="input-group">
                    <input type="text" name="no_hp" id="no_hp" class="form-control" placeholder="08xx xxxx xxxx" value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>" required>
                    <button class="btn btn-outline-secondary" type="button" id="cek-pelanggan">Cek Pelanggan</button>
                </div>
                <div class="form-text text-muted" id="cek-status"></div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nama Pelanggan</label>
                <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama pelanggan" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Kota</label>
                <input type="text" name="kota" id="kota" class="form-control" placeholder="Kota domisili" value="<?= htmlspecialchars($_POST['kota'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Kategori Keluhan</label>
                <select class="form-select" name="kategori_id" required>
                    <option value="">Pilih kategori</option>
                    <?php foreach ($kategoriList as $option): ?>
                        <option value="<?= (int)$option['id'] ?>" <?= (($_POST['kategori_id'] ?? '') == $option['id']) ? 'selected' : '' ?>><?= htmlspecialchars($option['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Channel</label>
                <select class="form-select" name="channel">
                    <?php foreach ($channelList as $option): ?>
                        <option value="<?= htmlspecialchars($option) ?>" <?= (($_POST['channel'] ?? '') === $option) ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Prioritas</label>
                <select class="form-select" name="prioritas">
                    <?php foreach ($prioritasList as $option): ?>
                        <option value="<?= htmlspecialchars($option) ?>" <?= (($_POST['prioritas'] ?? 'Medium') === $option) ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi Keluhan</label>
                <textarea class="form-control" name="deskripsi" rows="4" placeholder="Jelaskan detail keluhan..."><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-danger" type="submit">Simpan</button>
                <a class="btn btn-secondary" href="?page=keluhan">Batal</a>
            </div>
        </form>
    </div>
</div>
<script>
(function() {
    const btn = document.getElementById('cek-pelanggan');
    if (!btn) return;
    const status = document.getElementById('cek-status');
    const noHpInput = document.getElementById('no_hp');
    const namaInput = document.getElementById('nama');
    const kotaInput = document.getElementById('kota');

    const setStatus = (msg, isError = false) => {
        if (!status) return;
        status.textContent = msg;
        status.className = 'form-text ' + (isError ? 'text-danger' : 'text-success');
    };

    btn.addEventListener('click', () => {
        const hp = (noHpInput?.value || '').trim();
        if (!hp) {
            setStatus('Isi No HP terlebih dahulu', true);
            return;
        }
        setStatus('Mencari...', false);
        fetch(`?ajax=cek_pelanggan&no_hp=${encodeURIComponent(hp)}`, { headers: { 'Accept': 'application/json' } })
            .then((r) => r.json())
            .then((res) => {
                if (res.ok && res.data) {
                    namaInput.value = res.data.nama_pelanggan || '';
                    kotaInput.value = res.data.kota || '';
                    setStatus('Data ditemukan dan terisi otomatis.');
                } else {
                    setStatus(res.message || 'Pelanggan tidak ditemukan.', true);
                }
            })
            .catch(() => setStatus('Gagal cek pelanggan.', true));
    });
})();
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
