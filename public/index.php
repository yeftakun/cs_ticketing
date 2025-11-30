<?php
// Front controller dengan autentikasi dan data DB.
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

function generateTicketCode(PDO $db): string
{
    do {
        $code = 'KEL-' . date('Ymd') . '-' . str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $stmt = $db->prepare("SELECT COUNT(*) FROM keluhan WHERE kode_keluhan = :code");
        $stmt->execute([':code' => $code]);
        $exists = $stmt->fetchColumn() > 0;
    } while ($exists);
    return $code;
}

$db = db();
$loggedIn = isset($_SESSION['user']);
$currentUser = $_SESSION['user'] ?? null;
$page = $_GET['page'] ?? ($loggedIn ? 'dashboard' : 'login');
$error = null;
$info = null;

// Logout
if ($page === 'logout') {
    session_unset();
    session_destroy();
    redirect('?page=login');
}

// Tangani login
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
                $stmt = $db->prepare("SELECT id, nama, username, password_hash, role FROM users WHERE username = :username AND is_active = 1 LIMIT 1");
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

    require __DIR__ . '/../app/views/auth/login.php';
    exit;
}

// Proteksi halaman lain
if (!$loggedIn) {
    redirect('?page=login');
}

// Data umum
$statusList = ['Open', 'On Progress', 'Pending', 'Solved', 'Closed'];
$prioritasList = ['Low', 'Medium', 'High', 'Critical'];
$channelList = ['Call Center', 'Grapari', 'WhatsApp', 'Aplikasi', 'Live Chat', 'Media Sosial', 'Email', 'Lainnya'];

switch ($page) {
    case 'dashboard':
        $filters = [
            'from' => $_GET['from'] ?? date('Y-m-01'),
            'to' => $_GET['to'] ?? date('Y-m-d'),
            'kategori' => $_GET['kategori'] ?? '',
            'status' => $_GET['status'] ?? '',
        ];
        $where = [];
        $params = [];
        if (!empty($filters['from'])) {
            $where[] = "DATE(t.tanggal_lapor) >= :from";
            $params[':from'] = $filters['from'];
        }
        if (!empty($filters['to'])) {
            $where[] = "DATE(t.tanggal_lapor) <= :to";
            $params[':to'] = $filters['to'];
        }
        if (!empty($filters['kategori'])) {
            $where[] = "t.kategori_id = :kategori";
            $params[':kategori'] = $filters['kategori'];
        }
        if (!empty($filters['status'])) {
            $where[] = "t.status_keluhan = :status";
            $params[':status'] = $filters['status'];
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Statistik ringkas (menghormati filter tambahan)
        $statTodayStmt = $db->prepare("SELECT COUNT(*) FROM keluhan t {$whereSql} " . ($where ? " AND " : " WHERE ") . " DATE(t.tanggal_lapor) = CURDATE()");
        foreach ($params as $k => $v) $statTodayStmt->bindValue($k, $v);
        $statTodayStmt->execute();
        $statToday = (int)$statTodayStmt->fetchColumn();

        $statMonthStmt = $db->prepare("SELECT COUNT(*) FROM keluhan t {$whereSql} " . ($where ? " AND " : " WHERE ") . " YEAR(t.tanggal_lapor) = YEAR(CURDATE()) AND MONTH(t.tanggal_lapor) = MONTH(CURDATE())");
        foreach ($params as $k => $v) $statMonthStmt->bindValue($k, $v);
        $statMonthStmt->execute();
        $statMonth = (int)$statMonthStmt->fetchColumn();

        $statOpenStmt = $db->prepare("SELECT COUNT(*) FROM keluhan t {$whereSql} " . ($where ? " AND " : " WHERE ") . " t.status_keluhan = 'Open'");
        foreach ($params as $k => $v) $statOpenStmt->bindValue($k, $v);
        $statOpenStmt->execute();
        $statOpen = (int)$statOpenStmt->fetchColumn();

        $statSolvedStmt = $db->prepare("SELECT COUNT(*) FROM keluhan t {$whereSql} " . ($where ? " AND " : " WHERE ") . " t.status_keluhan = 'Solved'");
        foreach ($params as $k => $v) $statSolvedStmt->bindValue($k, $v);
        $statSolvedStmt->execute();
        $statSolved = (int)$statSolvedStmt->fetchColumn();

        // Bar chart per kategori
        $kategoriStmt = $db->prepare("SELECT k.nama_kategori AS label, COUNT(t.id) AS total FROM kategori_keluhan k LEFT JOIN keluhan t ON t.kategori_id = k.id {$whereSql} GROUP BY k.id ORDER BY total DESC");
        foreach ($params as $k => $v) $kategoriStmt->bindValue($k, $v);
        $kategoriStmt->execute();
        $kategoriRows = $kategoriStmt->fetchAll();
        $barLabels = array_column($kategoriRows, 'label');
        $barValues = array_map('intval', array_column($kategoriRows, 'total'));

        // Tren per hari (gunakan rentang filter jika ada, default 7 hari)
        $trendStart = $filters['from'] ?: date('Y-m-d', strtotime('-6 days'));
        $trendEnd = $filters['to'] ?: date('Y-m-d');
        $period = new DatePeriod(new DateTime($trendStart), new DateInterval('P1D'), (new DateTime($trendEnd))->modify('+1 day'));
        $trendBase = [];
        foreach ($period as $dt) {
            $trendBase[$dt->format('Y-m-d')] = 0;
        }
        $trendWhere = $whereSql ?: 'WHERE 1=1';
        $trendStmt = $db->prepare("SELECT DATE(t.tanggal_lapor) AS d, COUNT(*) AS total FROM keluhan t {$trendWhere} GROUP BY DATE(t.tanggal_lapor)");
        foreach ($params as $k => $v) $trendStmt->bindValue($k, $v);
        $trendStmt->execute();
        $trendRows = $trendStmt->fetchAll();
        foreach ($trendRows as $row) {
            if (isset($trendBase[$row['d']])) {
                $trendBase[$row['d']] = (int)$row['total'];
            }
        }
        $trendLabels = array_map(static fn($date) => date('D', strtotime($date)), array_keys($trendBase));
        $trendValues = array_values($trendBase);

        // Keluhan terbaru (sesuai filter)
        $recentStmt = $db->prepare("
            SELECT t.id, t.kode_keluhan, t.tanggal_lapor, p.nama_pelanggan, p.no_hp, k.nama_kategori, t.status_keluhan, t.prioritas
            FROM keluhan t
            LEFT JOIN pelanggan p ON p.id = t.pelanggan_id
            LEFT JOIN kategori_keluhan k ON k.id = t.kategori_id
            {$whereSql}
            ORDER BY t.tanggal_lapor DESC
            LIMIT 10
        ");
        foreach ($params as $k => $v) $recentStmt->bindValue($k, $v);
        $recentStmt->execute();
        $recentComplaints = $recentStmt->fetchAll();

        $kategoriList = $db->query("SELECT id, nama_kategori FROM kategori_keluhan ORDER BY nama_kategori")->fetchAll();
        require __DIR__ . '/../app/views/dashboard/index.php';
        break;

    case 'keluhan':
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'kategori' => $_GET['kategori'] ?? '',
            'status' => $_GET['status'] ?? '',
            'prioritas' => $_GET['prioritas'] ?? '',
            'channel' => $_GET['channel'] ?? '',
            'from' => $_GET['from'] ?? '',
            'to' => $_GET['to'] ?? '',
            'pelanggan' => $_GET['pelanggan'] ?? '',
        ];
        $kategoriList = $db->query("SELECT id, nama_kategori FROM kategori_keluhan ORDER BY nama_kategori")->fetchAll();

        $where = [];
        $params = [];
        if ($filters['q'] !== '') {
            $where[] = "(t.kode_keluhan LIKE :q OR p.nama_pelanggan LIKE :q OR p.no_hp LIKE :q)";
            $params[':q'] = '%' . $filters['q'] . '%';
        }
        if ($filters['kategori'] !== '') {
            $where[] = "t.kategori_id = :kategori";
            $params[':kategori'] = $filters['kategori'];
        }
        if ($filters['status'] !== '') {
            $where[] = "t.status_keluhan = :status";
            $params[':status'] = $filters['status'];
        }
        if ($filters['prioritas'] !== '') {
            $where[] = "t.prioritas = :prioritas";
            $params[':prioritas'] = $filters['prioritas'];
        }
        if ($filters['channel'] !== '') {
            $where[] = "t.channel = :channel";
            $params[':channel'] = $filters['channel'];
        }
        if ($filters['pelanggan'] !== '') {
            $where[] = "t.pelanggan_id = :pelanggan";
            $params[':pelanggan'] = $filters['pelanggan'];
        }
        if ($filters['from'] !== '') {
            $where[] = "DATE(t.tanggal_lapor) >= :from";
            $params[':from'] = $filters['from'];
        }
        if ($filters['to'] !== '') {
            $where[] = "DATE(t.tanggal_lapor) <= :to";
            $params[':to'] = $filters['to'];
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $pageNum = max(1, (int)($_GET['p'] ?? 1));
        $perPage = 10;
        $offset = ($pageNum - 1) * $perPage;

        $countStmt = $db->prepare("SELECT COUNT(*) FROM keluhan t LEFT JOIN pelanggan p ON p.id = t.pelanggan_id {$whereSql}");
        $countStmt->execute($params);
        $totalRows = (int)$countStmt->fetchColumn();
        $totalPages = max(1, (int)ceil($totalRows / $perPage));

        $sql = "
            SELECT t.id, t.tanggal_lapor, t.kode_keluhan, p.nama_pelanggan, p.no_hp, k.nama_kategori, t.channel, t.status_keluhan, t.prioritas, u.nama AS petugas
            FROM keluhan t
            LEFT JOIN pelanggan p ON p.id = t.pelanggan_id
            LEFT JOIN kategori_keluhan k ON k.id = t.kategori_id
            LEFT JOIN users u ON u.id = t.updated_by
            {$whereSql}
            ORDER BY t.tanggal_lapor DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $complaints = $stmt->fetchAll();

        require __DIR__ . '/../app/views/keluhan/index.php';
        break;

    case 'keluhan-create':
        $kategoriList = $db->query("SELECT id, nama_kategori FROM kategori_keluhan ORDER BY nama_kategori")->fetchAll();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $no_hp = trim($_POST['no_hp'] ?? '');
            $nama = trim($_POST['nama'] ?? '');
            $kota = trim($_POST['kota'] ?? '');
            $kategori_id = (int)($_POST['kategori_id'] ?? 0);
            $channel = $_POST['channel'] ?? '';
            $prioritas = $_POST['prioritas'] ?? 'Medium';
            $deskripsi = trim($_POST['deskripsi'] ?? '');

            if ($no_hp === '') $errors[] = 'No HP wajib diisi.';
            if ($kategori_id <= 0) $errors[] = 'Kategori wajib dipilih.';
            if (!in_array($channel, $channelList, true)) $errors[] = 'Channel tidak valid.';
            if (!in_array($prioritas, $prioritasList, true)) $errors[] = 'Prioritas tidak valid.';
            if ($deskripsi === '') $errors[] = 'Deskripsi wajib diisi.';

            if (empty($errors)) {
                try {
                    $db->beginTransaction();
                    $stmt = $db->prepare("SELECT id, nama_pelanggan, kota FROM pelanggan WHERE no_hp = :no_hp LIMIT 1");
                    $stmt->execute([':no_hp' => $no_hp]);
                    $pelanggan = $stmt->fetch();
                    if ($pelanggan) {
                        $pelangganId = $pelanggan['id'];
                        if ($nama !== '' || $kota !== '') {
                            $upd = $db->prepare("UPDATE pelanggan SET nama_pelanggan = COALESCE(NULLIF(:nama,''), nama_pelanggan), kota = COALESCE(NULLIF(:kota,''), kota) WHERE id = :id");
                            $upd->execute([':nama' => $nama, ':kota' => $kota, ':id' => $pelangganId]);
                        }
                    } else {
                        $ins = $db->prepare("INSERT INTO pelanggan (nama_pelanggan, no_hp, email, kota) VALUES (:nama, :no_hp, NULL, :kota)");
                        $ins->execute([':nama' => $nama ?: $no_hp, ':no_hp' => $no_hp, ':kota' => $kota ?: null]);
                        $pelangganId = (int)$db->lastInsertId();
                    }

                    $kode = generateTicketCode($db);
                    $now = date('Y-m-d H:i:s');
                    $insKeluhan = $db->prepare("
                        INSERT INTO keluhan (kode_keluhan, pelanggan_id, kategori_id, channel, deskripsi_keluhan, status_keluhan, prioritas, tanggal_lapor, tanggal_update_terakhir, tanggal_selesai, created_by, updated_by)
                        VALUES (:kode, :pelanggan_id, :kategori_id, :channel, :deskripsi, 'Open', :prioritas, :tgl, :tgl, NULL, :user_id, :user_id)
                    ");
                    $insKeluhan->execute([
                        ':kode' => $kode,
                        ':pelanggan_id' => $pelangganId,
                        ':kategori_id' => $kategori_id,
                        ':channel' => $channel,
                        ':deskripsi' => $deskripsi,
                        ':prioritas' => $prioritas,
                        ':tgl' => $now,
                        ':user_id' => $currentUser['id'],
                    ]);
                    $keluhanId = (int)$db->lastInsertId();
                    $db->commit();
                    redirect('?page=keluhan-show&id=' . $keluhanId);
                } catch (PDOException $e) {
                    $db->rollBack();
                    $errors[] = 'Gagal menyimpan keluhan.';
                }
            }
        }

        require __DIR__ . '/../app/views/keluhan/create.php';
        break;

    case 'keluhan-edit':
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) redirect('?page=keluhan');

        $stmt = $db->prepare("
            SELECT t.*, p.nama_pelanggan, p.no_hp, p.kota, k.nama_kategori
            FROM keluhan t
            LEFT JOIN pelanggan p ON p.id = t.pelanggan_id
            LEFT JOIN kategori_keluhan k ON k.id = t.kategori_id
            WHERE t.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $keluhan = $stmt->fetch();
        if (!$keluhan) redirect('?page=keluhan');

        $kategoriList = $db->query("SELECT id, nama_kategori FROM kategori_keluhan ORDER BY nama_kategori")->fetchAll();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $kategori_id = (int)($_POST['kategori_id'] ?? 0);
            $channel = $_POST['channel'] ?? '';
            $prioritas = $_POST['prioritas'] ?? '';
            $deskripsi = trim($_POST['deskripsi'] ?? '');

            if ($kategori_id <= 0) $errors[] = 'Kategori wajib dipilih.';
            if (!in_array($channel, $channelList, true)) $errors[] = 'Channel tidak valid.';
            if (!in_array($prioritas, $prioritasList, true)) $errors[] = 'Prioritas tidak valid.';
            if ($deskripsi === '') $errors[] = 'Deskripsi wajib diisi.';

            if (empty($errors)) {
                $now = date('Y-m-d H:i:s');
                $upd = $db->prepare("
                    UPDATE keluhan
                    SET kategori_id = :kategori_id,
                        channel = :channel,
                        prioritas = :prioritas,
                        deskripsi_keluhan = :deskripsi,
                        tanggal_update_terakhir = :tgl,
                        updated_by = :user_id
                    WHERE id = :id
                ");
                $upd->execute([
                    ':kategori_id' => $kategori_id,
                    ':channel' => $channel,
                    ':prioritas' => $prioritas,
                    ':deskripsi' => $deskripsi,
                    ':tgl' => $now,
                    ':user_id' => $currentUser['id'],
                    ':id' => $id,
                ]);
                redirect('?page=keluhan-show&id=' . $id);
            }
        }

        require __DIR__ . '/../app/views/keluhan/edit.php';
        break;

    case 'keluhan-show':
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) redirect('?page=keluhan');

        $stmt = $db->prepare("
            SELECT t.*, p.nama_pelanggan, p.no_hp, p.kota, k.nama_kategori
            FROM keluhan t
            LEFT JOIN pelanggan p ON p.id = t.pelanggan_id
            LEFT JOIN kategori_keluhan k ON k.id = t.kategori_id
            WHERE t.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $keluhan = $stmt->fetch();
        if (!$keluhan) redirect('?page=keluhan');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $statusBaru = $_POST['status_baru'] ?? '';
            $catatan = trim($_POST['catatan'] ?? '');
            if (!in_array($statusBaru, $statusList, true)) {
                $error = 'Status tidak valid.';
            } elseif ($catatan === '') {
                $error = 'Catatan wajib diisi.';
            } else {
                $now = date('Y-m-d H:i:s');
                $db->beginTransaction();
                try {
                    $ins = $db->prepare("INSERT INTO keluhan_log (keluhan_id, status_log, catatan, tanggal_log, user_id) VALUES (:keluhan_id, :status, :catatan, :tgl, :user_id)");
                    $ins->execute([
                        ':keluhan_id' => $id,
                        ':status' => $statusBaru,
                        ':catatan' => $catatan,
                        ':tgl' => $now,
                        ':user_id' => $currentUser['id'],
                    ]);

                    $selesai = in_array($statusBaru, ['Solved', 'Closed'], true) ? $now : null;
                    $upd = $db->prepare("
                        UPDATE keluhan
                        SET status_keluhan = :status,
                            tanggal_update_terakhir = :tgl,
                            tanggal_selesai = :selesai,
                            updated_by = :user_id
                        WHERE id = :id
                    ");
                    $upd->execute([
                        ':status' => $statusBaru,
                        ':tgl' => $now,
                        ':selesai' => $selesai,
                        ':user_id' => $currentUser['id'],
                        ':id' => $id,
                    ]);

                    $db->commit();
                    redirect('?page=keluhan-show&id=' . $id);
                } catch (PDOException $e) {
                    $db->rollBack();
                    $error = 'Gagal menyimpan log.';
                }
            }
        }

        $logsStmt = $db->prepare("
            SELECT l.*, u.nama AS user_nama
            FROM keluhan_log l
            LEFT JOIN users u ON u.id = l.user_id
            WHERE l.keluhan_id = :id
            ORDER BY l.tanggal_log DESC
        ");
        $logsStmt->execute([':id' => $id]);
        $timeline = $logsStmt->fetchAll();

        require __DIR__ . '/../app/views/keluhan/show.php';
        break;

    case 'pelanggan':
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'kota' => trim($_GET['kota'] ?? ''),
        ];
        $where = [];
        $params = [];
        if ($filters['q'] !== '') {
            $where[] = "(p.nama_pelanggan LIKE :q OR p.no_hp LIKE :q OR p.kota LIKE :q)";
            $params[':q'] = '%' . $filters['q'] . '%';
        }
        if ($filters['kota'] !== '') {
            $where[] = "p.kota = :kota";
            $params[':kota'] = $filters['kota'];
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $stmt = $db->prepare("
            SELECT p.*, COUNT(k.id) AS jumlah_keluhan
            FROM pelanggan p
            LEFT JOIN keluhan k ON k.pelanggan_id = p.id
            {$whereSql}
            GROUP BY p.id
            ORDER BY p.nama_pelanggan ASC
        ");
        $stmt->execute($params);
        $pelanggan = $stmt->fetchAll();
        require __DIR__ . '/../app/views/pelanggan/index.php';
        break;

    case 'pelanggan-form':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $pelanggan = ['nama' => '', 'no_hp' => '', 'email' => '', 'kota' => ''];
        $isEdit = false;
        $errors = [];

        if ($id) {
            $stmt = $db->prepare("SELECT * FROM pelanggan WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            if ($row) {
                $pelanggan = [
                    'id' => $row['id'],
                    'nama' => $row['nama_pelanggan'],
                    'no_hp' => $row['no_hp'],
                    'email' => $row['email'],
                    'kota' => $row['kota'],
                ];
                $isEdit = true;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama'] ?? '');
            $no_hp = trim($_POST['no_hp'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $kota = trim($_POST['kota'] ?? '');
            if ($nama === '') $errors[] = 'Nama wajib diisi.';
            if ($no_hp === '') $errors[] = 'No HP wajib diisi.';
            if (empty($errors)) {
                if ($isEdit) {
                    $upd = $db->prepare("UPDATE pelanggan SET nama_pelanggan = :nama, no_hp = :no_hp, email = :email, kota = :kota WHERE id = :id");
                    $upd->execute([':nama' => $nama, ':no_hp' => $no_hp, ':email' => $email ?: null, ':kota' => $kota ?: null, ':id' => $pelanggan['id']]);
                } else {
                    $ins = $db->prepare("INSERT INTO pelanggan (nama_pelanggan, no_hp, email, kota) VALUES (:nama, :no_hp, :email, :kota)");
                    $ins->execute([':nama' => $nama, ':no_hp' => $no_hp, ':email' => $email ?: null, ':kota' => $kota ?: null]);
                }
                redirect('?page=pelanggan');
            }
        }

        require __DIR__ . '/../app/views/pelanggan/form.php';
        break;

    case 'admin-kategori':
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $nama = trim($_POST['nama'] ?? '');
            $deskripsi = trim($_POST['deskripsi'] ?? '');
            if ($nama === '') {
                $errors[] = 'Nama kategori wajib diisi.';
            } else {
                if ($id > 0) {
                    $upd = $db->prepare("UPDATE kategori_keluhan SET nama_kategori = :nama, deskripsi = :deskripsi WHERE id = :id");
                    $upd->execute([':nama' => $nama, ':deskripsi' => $deskripsi ?: null, ':id' => $id]);
                } else {
                    $ins = $db->prepare("INSERT INTO kategori_keluhan (nama_kategori, deskripsi) VALUES (:nama, :deskripsi)");
                    $ins->execute([':nama' => $nama, ':deskripsi' => $deskripsi ?: null]);
                }
                redirect('?page=admin-kategori');
            }
        }
        $kategori = $db->query("SELECT id, nama_kategori, deskripsi, (SELECT COUNT(*) FROM keluhan t WHERE t.kategori_id = k.id) AS jumlah FROM kategori_keluhan k ORDER BY nama_kategori")->fetchAll();
        $editKategori = null;
        if (isset($_GET['id'])) {
            $idEdit = (int)$_GET['id'];
            foreach ($kategori as $row) {
                if ((int)$row['id'] === $idEdit) {
                    $editKategori = $row;
                    break;
                }
            }
        }
        require __DIR__ . '/../app/views/admin/kategori_index.php';
        break;

    case 'admin-users':
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'create') {
                $nama = trim($_POST['nama'] ?? '');
                $username = trim($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';
                $role = $_POST['role'] ?? 'agent';
                $aktif = isset($_POST['aktif']) ? 1 : 0;
                if ($nama === '' || $username === '' || $password === '') {
                    $errors[] = 'Nama, username, dan password wajib diisi.';
                } elseif (!in_array($role, ['agent', 'supervisor', 'admin'], true)) {
                    $errors[] = 'Role tidak valid.';
                } else {
                    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = :u");
                    $stmt->execute([':u' => $username]);
                    if ($stmt->fetchColumn() > 0) {
                        $errors[] = 'Username sudah digunakan.';
                    } else {
                        $hash = password_hash($password, PASSWORD_BCRYPT);
                        $ins = $db->prepare("INSERT INTO users (nama, username, password_hash, role, is_active) VALUES (:nama, :username, :hash, :role, :aktif)");
                        $ins->execute([':nama' => $nama, ':username' => $username, ':hash' => $hash, ':role' => $role, ':aktif' => $aktif]);
                        redirect('?page=admin-users');
                    }
                }
            } elseif ($action === 'toggle') {
                $id = (int)($_POST['id'] ?? 0);
                $stmt = $db->prepare("UPDATE users SET is_active = 1 - is_active WHERE id = :id");
                $stmt->execute([':id' => $id]);
                redirect('?page=admin-users');
            }
        }
        $users = $db->query("SELECT id, nama, username, role, is_active FROM users ORDER BY nama")->fetchAll();
        require __DIR__ . '/../app/views/admin/users_index.php';
        break;

    default:
        http_response_code(404);
        echo "<h1>404</h1><p>Halaman tidak ditemukan.</p>";
        break;
}
