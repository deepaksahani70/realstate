
<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$currentDirectory = basename(dirname($_SERVER['PHP_SELF']));

$isDashboard = (
    $currentPage === 'dashboard.php' &&
    $currentDirectory === 'admin'
);

$isProperties = $currentDirectory === 'properties';
$isLocations = $currentDirectory === 'locations';
$isBlogs = $currentDirectory === 'blog';
$isUserReviews = $currentDirectory === 'user-review';
?>

<aside class="admin-sidebar" id="adminSidebar">

    <a href="<?= htmlspecialchars(APP_URL, ENT_QUOTES, 'UTF-8') ?>/admin/dashboard.php" class="admin-brand">
        <span class="admin-brand-icon">
            <i class="ri-building-2-line"></i>
        </span>

        <span>
            <strong>EstateAdmin</strong>
            <small>PROPERTY MANAGEMENT</small>
        </span>
    </a>

    <p class="admin-nav-label">MAIN MENU</p>

    <nav class="admin-nav">

        <a href="<?= htmlspecialchars(APP_URL, ENT_QUOTES, 'UTF-8') ?>/admin/dashboard.php"
           class="<?= $isDashboard ? 'active' : '' ?>">
            <i class="ri-dashboard-line"></i>
            <span>Dashboard</span>
        </a>

        <a href="<?= htmlspecialchars(APP_URL, ENT_QUOTES, 'UTF-8') ?>/admin/properties/index.php"
           class="<?= $isProperties ? 'active' : '' ?>">
            <i class="ri-building-line"></i>
            <span>Properties</span>
        </a>

        <a href="<?= htmlspecialchars(APP_URL, ENT_QUOTES, 'UTF-8') ?>/admin/locations/index.php"
           class="<?= $isLocations ? 'active' : '' ?>">
            <i class="ri-map-pin-line"></i>
            <span>Locations</span>
        </a>

        <a href="<?= htmlspecialchars(APP_URL, ENT_QUOTES, 'UTF-8') ?>/admin/blog/index.php"
           class="<?= $isBlogs ? 'active' : '' ?>">
            <i class="ri-article-line"></i>
            <span>Blogs</span>
        </a>

        <a href="<?= APP_URL ?>/admin/team/index.php"
            class="<?= $currentDirectory === 'team' ? 'active' : '' ?>">
            <i class="ri-team-line"></i>
            <span>Team</span>
        </a>

        <a href="<?= htmlspecialchars(APP_URL, ENT_QUOTES, 'UTF-8') ?>/admin/user-review/index.php"
           class="<?= $isUserReviews ? 'active' : '' ?>">
            <i class="ri-star-line"></i>
            <span>User Review</span>
        </a>

    </nav>

    <div class="admin-sidebar-bottom">

        <a href="<?= htmlspecialchars(APP_URL, ENT_QUOTES, 'UTF-8') ?>/"
           target="_blank"
           rel="noopener noreferrer">
            <i class="ri-external-link-line"></i>
            <span>Visit Website</span>
        </a>

        <form action="<?= htmlspecialchars(APP_URL, ENT_QUOTES, 'UTF-8') ?>/admin/logout.php" method="POST">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <button type="submit" class="admin-logout-link">
                <i class="ri-logout-box-r-line"></i>
                <span>Logout</span>
            </button>

        </form>

    </div>

</aside>

<div class="admin-overlay" id="adminOverlay"></div>
