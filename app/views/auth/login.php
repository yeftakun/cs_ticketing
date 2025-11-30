<?php
$pageTitle = 'Login Sistem Keluhan Pelanggan';
ob_start();
?>
<div class="auth-card card shadow-lg border-0 p-4">
    <div class="text-center mb-3">
        <div class="rounded-circle bg-light mx-auto d-flex align-items-center justify-content-center mb-2" style="width:60px;height:60px;">
            <i class="bi bi-headset text-danger fs-3"></i>
        </div>
        <h5 class="mb-1 fw-bold">Customer Complaint Dashboard</h5>
        <div class="text-muted small">Login Agent / Supervisor</div>
    </div>
    <div class="card-body p-0">
        <form>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" placeholder="Masukkan username">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" placeholder="Masukkan password">
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
                <a href="#" class="small text-decoration-none">Lupa password?</a>
            </div>
            <div class="d-grid">
                <button class="btn btn-danger btn-lg" type="button">Login</button>
            </div>
        </form>
        <div class="alert alert-danger mt-3 mb-0 d-none" role="alert">
            Username atau password salah.
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/auth.php';
