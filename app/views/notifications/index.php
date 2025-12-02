<?php
$pageTitle = 'Notifikasi';
$subtitle = 'Semua notifikasi Anda';
$breadcrumbs = ['Home' => '?page=dashboard', 'Notifikasi' => null];
$activeMenu = '';
ob_start();
?>
<div class="card">
    <div class="card-body">
        <h6 class="card-title mb-3">Notifikasi Terbaru</h6>
        <div id="notif-feed">
            <?php foreach ($items as $item): ?>
                <div class="border rounded-3 p-3 mb-2 <?= $item['is_read'] ? 'bg-light' : '' ?>">
                    <div class="d-flex justify-content-between">
                        <div class="fw-semibold"><?= htmlspecialchars($item['title']) ?></div>
                        <div class="text-muted small"><?= htmlspecialchars($item['created_at']) ?></div>
                    </div>
                    <div class="text-muted"><?= htmlspecialchars($item['message']) ?></div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
                <div class="text-muted">Tidak ada notifikasi.</div>
            <?php endif; ?>
        </div>
        <?php if ($hasMore): ?>
            <div class="d-grid mt-2">
                <button class="btn btn-outline-danger" id="notif-load-more" data-offset="<?= $limit + $offset ?>">Muat lebih banyak</button>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
(function() {
    const btn = document.getElementById('notif-load-more');
    if (!btn) return;
    const feed = document.getElementById('notif-feed');
    btn.addEventListener('click', function() {
        const offset = parseInt(btn.getAttribute('data-offset'), 10) || 0;
        fetch(`?page=notifications&ajax=1&offset=${offset}`, { headers: { 'Accept': 'application/json' } })
            .then((r) => r.json())
            .then((res) => {
                if (!res.items) return;
                res.items.forEach((item) => {
                    const div = document.createElement('div');
                    div.className = 'border rounded-3 p-3 mb-2 ' + (item.is_read ? 'bg-light' : '');
                    div.innerHTML = `
                        <div class="d-flex justify-content-between">
                            <div class="fw-semibold">${item.title || ''}</div>
                            <div class="text-muted small">${item.created_at || ''}</div>
                        </div>
                        <div class="text-muted">${item.message || ''}</div>
                    `;
                    feed.appendChild(div);
                });
                if (res.has_more) {
                    btn.setAttribute('data-offset', offset + (res.items.length || 0));
                } else {
                    btn.remove();
                }
            })
            .catch(() => {});
    });
})();
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
