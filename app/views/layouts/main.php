<?php
$pageTitle = $pageTitle ?? 'Dashboard Keluhan Pelanggan';
$subtitle = $subtitle ?? null;
$breadcrumbs = $breadcrumbs ?? [];
$currentUser = $currentUser ?? ['name' => 'Ayu Rahma', 'role' => 'Supervisor'];
$basePath = $basePath ?? rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($basePath === '/' || $basePath === '\\') {
    $basePath = '';
}
// Normalisasi jika diakses melalui /public
$appBase = preg_replace('#/public$#', '', $basePath);
$assetBase = $appBase . '/public/assets';
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetBase) ?>/css/style.css">
</head>

<body>
    <div class="d-lg-flex app-shell">
        <?php include __DIR__ . '/../partials/_sidebar.php'; ?>
        <div class="flex-grow-1 d-flex flex-column min-vh-100">
            <?php include __DIR__ . '/../partials/_navbar.php'; ?>
            <div class="sidebar-backdrop" id="sidebar-backdrop"></div>
            <main class="flex-grow-1 pt-4">
                <div class="container-fluid">
                    <div class="page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
                        <div>
                            <h4 class="mb-1"><?= htmlspecialchars($pageTitle) ?></h4>
                            <?php if ($subtitle): ?>
                                <div class="text-muted"><?= htmlspecialchars($subtitle) ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($breadcrumbs)): ?>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <?php foreach ($breadcrumbs as $label => $link): ?>
                                        <?php if ($link): ?>
                                            <li class="breadcrumb-item"><a href="<?= htmlspecialchars($link) ?>"><?= htmlspecialchars($label) ?></a></li>
                                        <?php else: ?>
                                            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($label) ?></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ol>
                            </nav>
                        <?php endif; ?>
                    </div>
                    <?= $content ?? '' ?>
                </div>
            </main>
            <?php include __DIR__ . '/../partials/_footer.php'; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="<?= htmlspecialchars($assetBase) ?>/js/app.js"></script>
</body>

</html>
