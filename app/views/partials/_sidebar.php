<?php
$activeMenu = $activeMenu ?? '';
$isActive = function (string $menu) use ($activeMenu): string {
    return $activeMenu === $menu ? 'active' : '';
};
?>
<aside class="sidebar p-3" id="sidebar">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <div class="brand">CS Ticketing</div>
            <small class="text-white-50">Telkomsel Care</small>
        </div>
        <button class="btn btn-sm btn-outline-light d-lg-none" data-toggle="sidebar" type="button" aria-label="Close sidebar">
            <i class="bi bi-x"></i>
        </button>
    </div>
    <div class="sidebar-menu">
        <a class="nav-link <?= $isActive('dashboard') ?>" href="#">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>
        <a class="nav-link <?= $isActive('keluhan') ?>" href="#">
            <i class="bi bi-chat-dots-fill"></i> Keluhan
        </a>
        <a class="nav-link <?= $isActive('pelanggan') ?>" href="#">
            <i class="bi bi-people-fill"></i> Pelanggan
        </a>

        <div class="section-title mt-3">Admin</div>
        <a class="nav-link sub-link <?= $isActive('users') ?>" href="#">
            <i class="bi bi-person-gear"></i> User Management
        </a>
        <a class="nav-link sub-link <?= $isActive('kategori') ?>" href="#">
            <i class="bi bi-tags-fill"></i> Kategori Keluhan
        </a>
    </div>
</aside>
