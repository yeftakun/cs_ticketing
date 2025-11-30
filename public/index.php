<?php
// Simple front controller to preview static views with dummy data.
$page = $_GET['page'] ?? 'login';

$routes = [
    'login' => __DIR__ . '/../app/views/auth/login.php',
    'dashboard' => __DIR__ . '/../app/views/dashboard/index.php',
    'keluhan' => __DIR__ . '/../app/views/keluhan/index.php',
    'keluhan-create' => __DIR__ . '/../app/views/keluhan/create.php',
    'keluhan-edit' => __DIR__ . '/../app/views/keluhan/edit.php',
    'keluhan-show' => __DIR__ . '/../app/views/keluhan/show.php',
    'pelanggan' => __DIR__ . '/../app/views/pelanggan/index.php',
    'pelanggan-form' => __DIR__ . '/../app/views/pelanggan/form.php',
    'admin-kategori' => __DIR__ . '/../app/views/admin/kategori_index.php',
    'admin-users' => __DIR__ . '/../app/views/admin/users_index.php',
];

if (isset($routes[$page])) {
    require $routes[$page];
} else {
    http_response_code(404);
    echo "<h1>404</h1><p>Halaman tidak ditemukan.</p>";
}
