<?php
$currentUser = $currentUser ?? ['name' => 'Ayu Rahma', 'role' => 'Supervisor'];
$initials = strtoupper(substr($currentUser['name'], 0, 1) . substr(strstr($currentUser['name'], ' ') ?: $currentUser['name'], 1, 1));
?>
<nav class="navbar navbar-expand-lg navbar-dark topbar sticky-top py-2 shadow-sm">
    <div class="container-fluid">
        <button class="btn btn-icon me-3" data-toggle="sidebar" type="button" aria-label="Toggle sidebar">
            <i class="bi bi-list fs-4"></i>
        </button>
        <a class="navbar-brand d-none d-sm-block" href="#">
            Customer Complaint Dashboard
        </a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <button class="btn btn-icon position-relative" type="button">
                <i class="bi bi-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">3</span>
            </button>
            <div class="dropdown">
                <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar text-uppercase"><?= htmlspecialchars($initials) ?></div>
                    <div class="ms-2 text-start">
                        <div class="small fw-semibold"><?= htmlspecialchars($currentUser['name']) ?></div>
                        <div class="small text-white-50"><?= htmlspecialchars(ucfirst($currentUser['role'])) ?></div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="?page=logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
