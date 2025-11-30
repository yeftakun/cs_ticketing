<?php
$pageTitle = $pageTitle ?? 'Login';
$basePath = $basePath ?? rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($basePath === '/' || $basePath === '\\') {
    $basePath = '';
}
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
    <div class="auth-bg d-flex align-items-center justify-content-center min-vh-100">
        <?= $content ?? '' ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
