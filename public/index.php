<?php
// Simple front controller dengan login & proteksi dasar.
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

session_start([
    'cookie_httponly' => true,
]);

function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

$loggedIn = isset($_SESSION['user']);
$page = $_GET['page'] ?? ($loggedIn ? 'dashboard' : 'login');

// Logout
if ($page === 'logout') {
    session_unset();
    session_destroy();
    redirect('?page=login');
}

// Tangani login
$error = null;
if ($page === 'login') {
    if ($loggedIn) {
        redirect('?page=dashboard');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Username dan password wajib diisi.';
        } else {
            try {
                $stmt = db()->prepare("SELECT id, nama, username, password_hash, role FROM users WHERE username = :username AND is_active = 1 LIMIT 1");
                $stmt->execute([':username' => $username]);
                $user = $stmt->fetch();

                if ($user && !empty($user['password_hash']) && password_verify($password, $user['password_hash'])) {
                    session_regenerate_id(true);
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'name' => $user['nama'] ?: $user['username'],
                        'username' => $user['username'],
                        'role' => $user['role'],
                    ];
                    redirect('?page=dashboard');
                } else {
                    $error = 'Username atau password salah.';
                }
            } catch (PDOException $e) {
                $error = 'Gagal koneksi database. Periksa konfigurasi DB.';
            }
        }
    }
}

// Proteksi halaman lain
if ($page !== 'login' && !$loggedIn) {
    redirect('?page=login');
}

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
    // Lewatkan user ke view lain
    $currentUser = $_SESSION['user'] ?? null;
    require $routes[$page];
} else {
    http_response_code(404);
    echo "<h1>404</h1><p>Halaman tidak ditemukan.</p>";
}
