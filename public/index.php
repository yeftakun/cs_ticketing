<?php
// Front controller dengan autentikasi dan data DB.
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

session_start([
    'cookie_httponly' => true,
]);

function forbidden(string $msg = 'Forbidden'): void
{
    http_response_code(403);
    echo $msg;
    exit;
}

function requireRole(array $allowedRoles, array $currentUser): void
{
    if (!in_array($currentUser['role'] ?? '', $allowedRoles, true)) {
        forbidden('Tidak memiliki akses.');
    }
}

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

// AJAX cek pelanggan
if (($_GET['ajax'] ?? '') === 'cek_pelanggan') {
    header('Content-Type: application/json');
    $noHp = trim($_GET['no_hp'] ?? '');
    if ($noHp === '') {
        echo json_encode(['ok' => false, 'message' => 'No HP wajib diisi']);
        exit;
    }
    $stmt = $db->prepare("SELECT id, nama_pelanggan, no_hp, kota FROM pelanggan WHERE no_hp = :hp LIMIT 1");
    $stmt->execute([':hp' => $noHp]);
    $pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($pelanggan) {
        echo json_encode(['ok' => true, 'data' => $pelanggan]);
    } else {
        echo json_encode(['ok' => false, 'message' => 'Pelanggan tidak ditemukan']);
    }
    exit;
}

// Data umum
$statusList = ['Open', 'On Progress', 'Pending', 'Solved', 'Closed'];
$prioritasList = ['Low', 'Medium', 'High', 'Critical'];
$channelList = ['Call Center', 'Grapari', 'WhatsApp', 'Aplikasi', 'Live Chat', 'Media Sosial', 'Email', 'Lainnya'];

switch ($page) {
    case 'profile':
        $userId = $currentUser['id'];
        $stmt = $db->prepare("SELECT id, nama, username, role, is_active FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $userProfile = $stmt->fetch();
        if (!$userProfile) {
            redirect('?page=login');
        }
        $errors = [];
        $success = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama'] ?? '');
            $password = $_POST['password'] ?? '';
            if ($nama === '') $errors[] = 'Nama wajib diisi.';
            if (empty($errors)) {
                $params = [':nama' => $nama, ':id' => $userId];
                $sql = "UPDATE users SET nama = :nama";
                if ($password !== '') {
                    $sql .= ", password_hash = :pwd";
                    $params[':pwd'] = password_hash($password, PASSWORD_BCRYPT);
                }
                $sql .= " WHERE id = :id";
                $upd = $db->prepare($sql);
                $upd->execute($params);
                $_SESSION['user']['name'] = $nama;
                $success = 'Profil berhasil diperbarui.';
                $userProfile['nama'] = $nama;
            }
        }
        require __DIR__ . '/../app/views/profile.php';
        break;
    case 'dashboard':
        $filters = [
            'from' => $_GET['from'] ?? date('Y-m-01'),
            'to' => $_GET['to'] ?? date('Y-m-d'),
            'kategori' => $_GET['kategori'] ?? '',
            'status' => $_GET['status'] ?? '',
        ];

        // Base filter (tanpa tanggal) untuk konsistensi perbandingan
        $whereBase = [];
        $paramsBase = [];
        if (!empty($filters['kategori'])) {
            $whereBase[] = "t.kategori_id = :kategori";
            $paramsBase[':kategori'] = $filters['kategori'];
        }
        if (!empty($filters['status'])) {
            $whereBase[] = "t.status_keluhan = :status";
            $paramsBase[':status'] = $filters['status'];
        }
        $dateWhere = [];
        $dateParams = [];
        if (!empty($filters['from'])) {
            $dateWhere[] = "DATE(t.tanggal_lapor) >= :from";
            $dateParams[':from'] = $filters['from'];
        }
        if (!empty($filters['to'])) {
            $dateWhere[] = "DATE(t.tanggal_lapor) <= :to";
            $dateParams[':to'] = $filters['to'];
        }

        $whereAll = array_merge($whereBase, $dateWhere);
        $paramsAll = array_merge($paramsBase, $dateParams);
        $whereSql = $whereAll ? 'WHERE ' . implode(' AND ', $whereAll) : '';
        $whereBaseSql = $whereBase ? 'WHERE ' . implode(' AND ', $whereBase) : '';

        $runCount = function (string $extraCondition, array $extraParams = []) use ($db, $whereBaseSql, $paramsBase) {
            $whereParts = [];
            if ($whereBaseSql) {
                $whereParts[] = substr($whereBaseSql, 6); // remove 'WHERE '
            }
            if ($extraCondition !== '') {
                $whereParts[] = $extraCondition;
            }
            $sqlWhere = $whereParts ? 'WHERE ' . implode(' AND ', $whereParts) : '';
            $stmt = $db->prepare("SELECT COUNT(*) FROM keluhan t {$sqlWhere}");
            foreach ($paramsBase as $k => $v) $stmt->bindValue($k, $v);
            foreach ($extraParams as $k => $v) $stmt->bindValue($k, $v);
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        };

        $buildCondition = function (array $dateWhere, string $extra) use ($dateParams): array {
            $parts = $dateWhere;
            if ($extra !== '') {
                $parts[] = $extra;
            }
            $condition = $parts ? implode(' AND ', $parts) : '';
            $params = $dateWhere ? $dateParams : [];
            return [$condition, $params];
        };

        [$condToday, $paramsToday] = $buildCondition($dateWhere, "DATE(t.tanggal_lapor) = CURDATE()");
        [$condYesterday, $paramsYesterday] = $buildCondition($dateWhere, "DATE(t.tanggal_lapor) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
        [$condMonth, $paramsMonth] = $buildCondition($dateWhere, "YEAR(t.tanggal_lapor) = YEAR(CURDATE()) AND MONTH(t.tanggal_lapor) = MONTH(CURDATE())");
        [$condPrevMonth, $paramsPrevMonth] = $buildCondition($dateWhere, "YEAR(t.tanggal_lapor) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND MONTH(t.tanggal_lapor) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))");
        [$condOpen, $paramsOpen] = $buildCondition($dateWhere, "t.status_keluhan = 'Open'");
        [$condOpenPrev, $paramsOpenPrev] = $buildCondition($dateWhere, "t.status_keluhan = 'Open' AND DATE(t.tanggal_update_terakhir) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
        [$condSolvedPrev, $paramsSolvedPrev] = $buildCondition($dateWhere, "t.status_keluhan = 'Solved' AND DATE(COALESCE(t.tanggal_selesai, t.tanggal_update_terakhir, t.tanggal_lapor)) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)");

        $statToday = $runCount($condToday, $paramsToday);
        $statYesterday = $runCount($condYesterday, $paramsYesterday);

        $statMonth = $runCount($condMonth, $paramsMonth);
        $statPrevMonth = $runCount($condPrevMonth, $paramsPrevMonth);

        $statOpen = $runCount($condOpen, $paramsOpen);
        $statOpenPrev = $runCount($condOpenPrev, $paramsOpenPrev);

        $statSolved = $runCount("t.status_keluhan = 'Solved'");
        $statSolvedPrev = $runCount($condSolvedPrev, $paramsSolvedPrev);

        $formatDelta = function (int $current, int $prev): string {
            if ($prev <= 0) {
                return $current > 0 ? "+100% vs 0" : "Stabil";
            }
            $diff = $current - $prev;
            $pct = round(($diff / $prev) * 100, 1);
            return ($diff >= 0 ? '+' : '') . $pct . '% vs sebelumnya';
        };

        $statNotes = [
            'today' => $formatDelta($statToday, $statYesterday),
            'month' => $formatDelta($statMonth, $statPrevMonth),
            'open' => $formatDelta($statOpen, $statOpenPrev),
            'solved' => $formatDelta($statSolved, $statSolvedPrev),
        ];

        // Bar chart per kategori
        $kategoriStmt = $db->prepare("SELECT k.nama_kategori AS label, COUNT(t.id) AS total FROM kategori_keluhan k LEFT JOIN keluhan t ON t.kategori_id = k.id {$whereSql} GROUP BY k.id ORDER BY total DESC");
        foreach ($paramsAll as $k => $v) $kategoriStmt->bindValue($k, $v);
        $kategoriStmt->execute();
        $kategoriRows = $kategoriStmt->fetchAll();
        $barLabels = array_column($kategoriRows, 'label');
        $barValues = array_map('intval', array_column($kategoriRows, 'total'));

        // Tren per hari (dua seri: baru & selesai)
        $trendStart = $filters['from'] ?: date('Y-m-d', strtotime('-6 days'));
        $trendEnd = $filters['to'] ?: date('Y-m-d');
        $period = new DatePeriod(new DateTime($trendStart), new DateInterval('P1D'), (new DateTime($trendEnd))->modify('+1 day'));
        $trendBaseNew = $trendBaseSolved = [];
        foreach ($period as $dt) {
            $trendBaseNew[$dt->format('Y-m-d')] = 0;
            $trendBaseSolved[$dt->format('Y-m-d')] = 0;
        }
        $trendWhere = $whereSql ?: 'WHERE 1=1';
        $trendStmt = $db->prepare("SELECT DATE(t.tanggal_lapor) AS d, COUNT(*) AS total FROM keluhan t {$trendWhere} GROUP BY DATE(t.tanggal_lapor)");
        foreach ($paramsAll as $k => $v) $trendStmt->bindValue($k, $v);
        $trendStmt->execute();
        foreach ($trendStmt->fetchAll() as $row) {
            if (isset($trendBaseNew[$row['d']])) {
                $trendBaseNew[$row['d']] = (int)$row['total'];
            }
        }
        $trendSolvedStmt = $db->prepare("SELECT DATE(COALESCE(t.tanggal_selesai, t.tanggal_update_terakhir, t.tanggal_lapor)) AS d, COUNT(*) AS total FROM keluhan t {$trendWhere} AND t.status_keluhan IN ('Solved','Closed') GROUP BY DATE(COALESCE(t.tanggal_selesai, t.tanggal_update_terakhir, t.tanggal_lapor))");
        foreach ($paramsAll as $k => $v) $trendSolvedStmt->bindValue($k, $v);
        $trendSolvedStmt->execute();
        foreach ($trendSolvedStmt->fetchAll() as $row) {
            if (isset($trendBaseSolved[$row['d']])) {
                $trendBaseSolved[$row['d']] = (int)$row['total'];
            }
        }
        $trendLabels = array_map(static fn($date) => date('D', strtotime($date)), array_keys($trendBaseNew));
        $trendValues = array_values($trendBaseNew);
        $trendSolvedValues = array_values($trendBaseSolved);

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
        foreach ($paramsAll as $k => $v) $recentStmt->bindValue($k, $v);
        $recentStmt->execute();
        $recentComplaints = $recentStmt->fetchAll();

        $kategoriList = $db->query("SELECT id, nama_kategori FROM kategori_keluhan ORDER BY nama_kategori")->fetchAll();
        require __DIR__ . '/../app/views/dashboard/index.php';
        break;

    case 'keluhan':
        requireRole(['admin', 'supervisor', 'agent'], $currentUser);
        // Quick status update from list (modal)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'quick-status') {
            $id = (int)($_POST['id'] ?? 0);
            $statusBaru = $_POST['status_baru'] ?? '';
            $catatan = trim($_POST['catatan'] ?? '');
            if ($currentUser['role'] === 'agent' && $id > 0) {
                $ownerStmt = $db->prepare("SELECT created_by FROM keluhan WHERE id = :id LIMIT 1");
                $ownerStmt->execute([':id' => $id]);
                $ownerId = $ownerStmt->fetchColumn();
                if ((int)$ownerId !== (int)$currentUser['id']) {
                    $error = 'Tidak boleh mengubah keluhan milik user lain.';
                }
            }
            if (!empty($error)) {
                // skip processing
            } elseif ($id > 0 && in_array($statusBaru, $statusList, true) && $catatan !== '') {
                try {
                    $now = date('Y-m-d H:i:s');
                    $db->beginTransaction();
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
                    redirect('?page=keluhan&info=Status%20berhasil%20diperbarui');
                } catch (PDOException $e) {
                    $db->rollBack();
                    $error = 'Gagal memperbarui status.';
                }
            } else {
                $error = 'Status atau catatan tidak valid.';
            }
        }

        // Bulk action update status
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'bulk-status') {
            $ids = $_POST['ids'] ?? [];
            $statusBaru = $_POST['status_baru'] ?? '';
            $catatan = trim($_POST['catatan'] ?? '');
            $idsInt = array_values(array_filter(array_map('intval', $ids)));
            if ($currentUser['role'] === 'agent' && !empty($idsInt)) {
                $ph = implode(',', array_fill(0, count($idsInt), '?'));
                $chk = $db->prepare("SELECT COUNT(*) FROM keluhan WHERE id IN ($ph) AND created_by = ?");
                $bindIdx = 1;
                foreach ($idsInt as $val) {
                    $chk->bindValue($bindIdx, $val, PDO::PARAM_INT);
                    $bindIdx++;
                }
                $chk->bindValue($bindIdx, $currentUser['id'], PDO::PARAM_INT);
                $chk->execute();
                $owned = (int)$chk->fetchColumn();
                if ($owned !== count($idsInt)) {
                    $error = 'Tidak boleh bulk update keluhan milik user lain.';
                }
            }
            if (!empty($idsInt) && in_array($statusBaru, $statusList, true) && $catatan !== '' && empty($error)) {
                try {
                    $db->beginTransaction();
                    $now = date('Y-m-d H:i:s');
                    // Insert log per keluhan
                    $insLog = $db->prepare("INSERT INTO keluhan_log (keluhan_id, status_log, catatan, tanggal_log, user_id) VALUES (:keluhan_id, :status, :catatan, :tgl, :user_id)");
                    foreach ($idsInt as $keluhanId) {
                        $insLog->execute([
                            ':keluhan_id' => $keluhanId,
                            ':status' => $statusBaru,
                            ':catatan' => $catatan,
                            ':tgl' => $now,
                            ':user_id' => $currentUser['id'],
                        ]);
                    }
                    // Update status secara bulk
                    $selesai = in_array($statusBaru, ['Solved', 'Closed'], true) ? $now : null;
                    $idPlaceholders = [];
                    $bindParams = [
                        ':status' => $statusBaru,
                        ':tgl' => $now,
                        ':selesai' => $selesai,
                        ':user_id' => $currentUser['id'],
                    ];
                    foreach ($idsInt as $idx => $keluhanId) {
                        $ph = ':id' . $idx;
                        $idPlaceholders[] = $ph;
                        $bindParams[$ph] = $keluhanId;
                    }
                    $updSql = "
                        UPDATE keluhan
                        SET status_keluhan = :status,
                            tanggal_update_terakhir = :tgl,
                            tanggal_selesai = :selesai,
                            updated_by = :user_id
                        WHERE id IN (" . implode(',', $idPlaceholders) . ")
                    ";
                    $upd = $db->prepare($updSql);
                    foreach ($bindParams as $k => $v) {
                        $upd->bindValue($k, $v);
                    }
                    $upd->execute();
                    $db->commit();
                    redirect('?page=keluhan&info=Bulk%20status%20berhasil%20diperbarui');
                } catch (PDOException $e) {
                    $db->rollBack();
                    $error = 'Gagal bulk update status.';
                }
            } else {
                $error = 'Pilih tiket dan isi status + catatan.';
            }
        }

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
        $sort = $_GET['sort'] ?? 'tanggal_lapor';
        $dir = strtolower($_GET['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $allowedSort = [
            'tanggal_lapor' => 't.tanggal_lapor',
            'kode_keluhan' => 't.kode_keluhan',
            'status_keluhan' => 't.status_keluhan',
            'prioritas' => 't.prioritas',
            'kategori' => 'k.nama_kategori'
        ];
        $sortSql = $allowedSort[$sort] ?? 't.tanggal_lapor';
        $kategoriList = $db->query("SELECT id, nama_kategori FROM kategori_keluhan ORDER BY nama_kategori")->fetchAll();

        $where = [];
        $params = [];
        if ($filters['q'] !== '') {
            $where[] = "(t.kode_keluhan LIKE :q_kode OR p.nama_pelanggan LIKE :q_nama OR p.no_hp LIKE :q_hp)";
            $like = '%' . $filters['q'] . '%';
            $params[':q_kode'] = $like;
            $params[':q_nama'] = $like;
            $params[':q_hp'] = $like;
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

        // Export handling
        $exportType = $_GET['export_type'] ?? $_GET['export'] ?? null;
        if ($exportType) {
            $exportSql = "
                SELECT t.tanggal_lapor, t.kode_keluhan, p.nama_pelanggan, p.no_hp, k.nama_kategori, t.channel, t.status_keluhan, t.prioritas, u.nama AS petugas
                FROM keluhan t
                LEFT JOIN pelanggan p ON p.id = t.pelanggan_id
                LEFT JOIN kategori_keluhan k ON k.id = t.kategori_id
                LEFT JOIN users u ON u.id = t.updated_by
                {$whereSql}
                ORDER BY {$sortSql} {$dir}
            ";
            $stmt = $db->prepare($exportSql);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $headers = ['Tanggal Lapor', 'Kode Keluhan', 'Nama Pelanggan', 'No HP', 'Kategori', 'Channel', 'Status', 'Prioritas', 'Petugas'];
            if ($exportType === 'excel' || $exportType === 'xlsx') {
                if (class_exists('ZipArchive')) {
                    $filename = 'keluhan-' . date('Ymd-His') . '.xlsx';
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment; filename="' . $filename . '"');

                    $xmlEscape = function ($str) {
                        return htmlspecialchars($str ?? '', ENT_XML1 | ENT_QUOTES, 'UTF-8');
                    };

                    $sheetRows = [];
                    $sheetRows[] = $headers;
                    foreach ($rows as $r) {
                        $sheetRows[] = [
                            $r['tanggal_lapor'],
                            $r['kode_keluhan'],
                            $r['nama_pelanggan'],
                            $r['no_hp'],
                            $r['nama_kategori'],
                            $r['channel'],
                            $r['status_keluhan'],
                            $r['prioritas'],
                            $r['petugas'],
                        ];
                    }

                    $sheetXml = '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
                    $rowNum = 1;
                    foreach ($sheetRows as $row) {
                        $sheetXml .= '<row r="' . $rowNum . '">';
                        $colNum = 0;
                        foreach ($row as $cell) {
                            $sheetXml .= '<c r="" t="inlineStr"><is><t>' . $xmlEscape($cell) . '</t></is></c>';
                            $colNum++;
                        }
                        $sheetXml .= '</row>';
                        $rowNum++;
                    }
                    $sheetXml .= '</sheetData></worksheet>';

                    $workbookXml = '<?xml version="1.0" encoding="UTF-8"?>'
                        . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
                        . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                        . '<sheets><sheet name="Keluhan" sheetId="1" r:id="rId1"/></sheets></workbook>';

                    $relsXml = '<?xml version="1.0" encoding="UTF-8"?>'
                        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
                        . '</Relationships>';

                    $wbRelsXml = '<?xml version="1.0" encoding="UTF-8"?>'
                        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
                        . '</Relationships>';

                    $typesXml = '<?xml version="1.0" encoding="UTF-8"?>'
                        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                        . '<Default Extension="xml" ContentType="application/xml"/>'
                        . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                        . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                        . '</Types>';

                    $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx');
                    $zip = new ZipArchive();
                    $zip->open($tmpFile, ZipArchive::OVERWRITE);
                    $zip->addFromString('[Content_Types].xml', $typesXml);
                    $zip->addEmptyDir('_rels');
                    $zip->addFromString('_rels/.rels', $relsXml);
                    $zip->addEmptyDir('xl');
                    $zip->addFromString('xl/workbook.xml', $workbookXml);
                    $zip->addEmptyDir('xl/_rels');
                    $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRelsXml);
                    $zip->addEmptyDir('xl/worksheets');
                    $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
                    $zip->close();

                    readfile($tmpFile);
                    @unlink($tmpFile);
                    exit;
                } else {
                    // Fallback ke CSV jika ZipArchive tidak tersedia
                    $exportType = 'csv';
                }
            }
            if ($exportType === 'csv') {
                $filename = 'keluhan-' . date('Ymd-His') . '.csv';
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $out = fopen('php://output', 'w');
                fputcsv($out, $headers);
                foreach ($rows as $r) {
                    fputcsv($out, [
                        $r['tanggal_lapor'],
                        $r['kode_keluhan'],
                        $r['nama_pelanggan'],
                        $r['no_hp'],
                        $r['nama_kategori'],
                        $r['channel'],
                        $r['status_keluhan'],
                        $r['prioritas'],
                        $r['petugas'],
                    ]);
                }
                fclose($out);
                exit;
            }
        }

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
            ORDER BY {$sortSql} {$dir}
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
        requireRole(['admin', 'supervisor', 'agent'], $currentUser);
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
        requireRole(['admin', 'supervisor', 'agent'], $currentUser);
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
        if ($currentUser['role'] === 'agent' && (int)$keluhan['created_by'] !== (int)$currentUser['id']) {
            forbidden('Tidak boleh mengedit keluhan milik user lain.');
        }

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
        requireRole(['admin', 'supervisor', 'agent'], $currentUser);
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) redirect('?page=keluhan');

        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        if ($basePath === '/' || $basePath === '\\') {
            $basePath = '';
        }
        $appBase = preg_replace('#/public$#', '', $basePath);

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

        $uploadBaseDir = __DIR__ . '/uploads/keluhan';
        $uploadBaseUrl = $appBase . '/public/uploads/keluhan';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $statusBaru = $_POST['status_baru'] ?? '';
            $catatan = trim($_POST['catatan'] ?? '');
            if ($currentUser['role'] === 'agent' && (int)$keluhan['created_by'] !== (int)$currentUser['id']) {
                $error = 'Tidak boleh mengubah keluhan milik user lain.';
            } elseif (!in_array($statusBaru, $statusList, true)) {
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
                    $logId = (int)$db->lastInsertId();

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

                    // Upload lampiran (opsional)
                    if (!empty($_FILES['lampiran']) && is_array($_FILES['lampiran']['name'])) {
                        $targetDir = $uploadBaseDir . '/' . $id . '/log_' . $logId;
                        if (!is_dir($targetDir)) {
                            mkdir($targetDir, 0777, true);
                        }
                        $countFiles = count($_FILES['lampiran']['name']);
                        for ($i = 0; $i < $countFiles; $i++) {
                            if ($_FILES['lampiran']['error'][$i] !== UPLOAD_ERR_OK) continue;
                            $name = basename($_FILES['lampiran']['name'][$i]);
                            $tmp = $_FILES['lampiran']['tmp_name'][$i];
                            $safeName = uniqid('file_', true) . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $name);
                            move_uploaded_file($tmp, $targetDir . '/' . $safeName);
                        }
                    }

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

        // Lampiran per log
        foreach ($timeline as &$log) {
            $attachments = [];
            $dir = $uploadBaseDir . '/' . $id . '/log_' . $log['id'];
            if (is_dir($dir)) {
                foreach (scandir($dir) as $f) {
                    if ($f === '.' || $f === '..') continue;
                    $attachments[] = [
                        'name' => $f,
                        'url' => $uploadBaseUrl . '/' . $id . '/log_' . $log['id'] . '/' . rawurlencode($f),
                    ];
                }
            }
            $log['attachments'] = $attachments;
        }
        unset($log);

        require __DIR__ . '/../app/views/keluhan/show.php';
        break;

    case 'pelanggan':
        requireRole(['admin', 'supervisor', 'agent'], $currentUser);
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'kota' => trim($_GET['kota'] ?? ''),
        ];
        $where = [];
        $params = [];
        if ($filters['q'] !== '') {
            $where[] = "(p.nama_pelanggan LIKE :q_nama OR p.no_hp LIKE :q_hp OR p.kota LIKE :q_kota)";
            $like = '%' . $filters['q'] . '%';
            $params[':q_nama'] = $like;
            $params[':q_hp'] = $like;
            $params[':q_kota'] = $like;
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
        requireRole(['admin', 'supervisor'], $currentUser);
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
        requireRole(['admin', 'supervisor'], $currentUser);
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (($currentUser['role'] ?? '') !== 'admin') {
                forbidden('Hanya admin yang boleh mengubah kategori.');
            }
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
        requireRole(['admin'], $currentUser);
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
        $stmt = $db->prepare("SELECT id, nama, username, role, is_active FROM users WHERE id <> :me ORDER BY nama");
        $stmt->execute([':me' => $currentUser['id']]);
        $users = $stmt->fetchAll();
        require __DIR__ . '/../app/views/admin/users_index.php';
        break;

    default:
        http_response_code(404);
        echo "<h1>404</h1><p>Halaman tidak ditemukan.</p>";
        break;
}
