<?php
// Ensure this file is not accessed directly
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__FILE__)));
}
?>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(to right, #04060aff, #080b10ff);">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <i class="bi bi-shop me-2" style="color: #f6f5efff;"></i>
            <span class="fw-bold">ZIARA</span>
            <span class="ms-2 badge bg-light text-dark rounded-pill">Admin</span>
        </a>
        
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- Notification Bell -->
                <li class="nav-item me-3">
                    <a class="nav-link position-relative" href="#" data-bs-toggle="tooltip" title="Notifications">
                        <i class="bi bi-bell-fill"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                        </span>
                    </a>
                </li>
                
                <!-- View Site Button -->
                <li class="nav-item me-3">
                    <a class="nav-link btn btn-outline-light btn-sm rounded-pill px-3" href="../index.php">
                        <i class="bi bi-shop-window me-1"></i> View Site
                    </a>
                </li>

                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" 
                       role="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-light p-1 me-2">
                            <i class="bi bi-person-circle text-dark"></i>
                        </div>
                        <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                        <li class="px-3 py-2 text-muted small">Signed in as <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></strong></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2" href="profile.php">
                                <i class="bi bi-person me-2 text-primary"></i> Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="settings.php">
                                <i class="bi bi-gear me-2 text-secondary"></i> Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2 text-danger" href="../logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Spacer for fixed navbar -->
<div style="margin-top: 70px;"></div>

<!-- Add this style section -->
<style>
.navbar {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.navbar-brand {
    font-size: 1.4rem;
    letter-spacing: 0.5px;
}

.nav-link {
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.nav-link:hover {
    transform: translateY(-1px);
}

.dropdown-menu {
    border-radius: 0.5rem;
    margin-top: 10px;
}

.dropdown-item {
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    padding-left: 1.8rem;
}

.badge {
    font-weight: 500;
}

/* Animation for notification bell */
@keyframes bellShake {
    0% { transform: rotate(0); }
    15% { transform: rotate(5deg); }
    30% { transform: rotate(-5deg); }
    45% { transform: rotate(4deg); }
    60% { transform: rotate(-4deg); }
    75% { transform: rotate(2deg); }
    85% { transform: rotate(-2deg); }
    92% { transform: rotate(1deg); }
    100% { transform: rotate(0); }
}

.bi-bell-fill:hover {
    animation: bellShake 0.5s cubic-bezier(.36,.07,.19,.97) both;
    transform-origin: top;
}
</style>

<!-- Initialize Bootstrap tooltips -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>