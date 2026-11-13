
<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

/*
|--------------------------------------------------------------------------
| Authentication already includes database.php and provides $pdo
|--------------------------------------------------------------------------
*/

$adminPageTitle = 'Dashboard';
$baseUrl = rtrim(APP_URL, '/');

/*
|--------------------------------------------------------------------------
| Dashboard Statistics
|--------------------------------------------------------------------------
*/

$dashboardStats = [
    'properties' => 0,
    'new_properties' => 0,
    'enquiries' => 0,
    'blogs' => 0,
    'locations' => 0
];

try {
    // Total Properties
    $stmt = $pdo->query("SELECT COUNT(*) FROM properties");
    $dashboardStats['properties'] = (int) $stmt->fetchColumn();

    // Properties added this month
    $stmt = $pdo->query("
        SELECT COUNT(*)
        FROM properties
        WHERE created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
    ");
    $dashboardStats['new_properties'] = (int) $stmt->fetchColumn();

} catch (PDOException $e) {
    error_log('Dashboard properties count error: ' . $e->getMessage());
}

try {
    // Total Enquiries
    $stmt = $pdo->query("SELECT COUNT(*) FROM enquiries");
    $dashboardStats['enquiries'] = (int) $stmt->fetchColumn();

} catch (PDOException $e) {
    error_log('Dashboard enquiries count error: ' . $e->getMessage());
}

try {
    // Published Blogs
    $stmt = $pdo->query("
        SELECT COUNT(*)
        FROM blogs
        WHERE status = 'published'
          AND published_at IS NOT NULL
    ");
    $dashboardStats['blogs'] = (int) $stmt->fetchColumn();

} catch (PDOException $e) {
    error_log('Dashboard blogs count error: ' . $e->getMessage());
}

try {
    // Total Locations
    $stmt = $pdo->query("SELECT COUNT(*) FROM locations");
    $dashboardStats['locations'] = (int) $stmt->fetchColumn();

} catch (PDOException $e) {
    error_log('Dashboard locations count error: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | EstateAdmin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/css/style.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/admin/css/admin.css">
</head>

<body class="admin-body">

    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <div class="admin-main">

        <?php include __DIR__ . '/includes/header.php'; ?>

        <main class="admin-content">

            <!-- Welcome Section -->
            <div class="admin-welcome">

                <div>
                    <h2>Welcome back, Admin!</h2>
                    <p>Here's what's happening with your website today.</p>
                </div>

                <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/admin/properties/index.php"
                   class="btn btn-primary"> 
                    <i class="ri-add-line"></i>
                    Add Property 
                </a>

            </div>

            <!-- Statistics -->
            <div class="row g-3 mb-4">

                <!-- Properties -->
                <div class="col-12 col-sm-6 col-xl-3">

                    <div class="admin-stat-card">

                        <div class="admin-stat-top">
                            <span>Total Properties</span>
                            <i class="ri-building-2-line"></i>
                        </div>

                        <h3><?= number_format($dashboardStats['properties']) ?></h3>

                        <p>
                            <span class="text-secondary-brand">
                                <?= number_format($dashboardStats['new_properties']) ?> new
                            </span>
                            this month
                        </p>

                    </div>

                </div>

                <!-- Enquiries -->
                <div class="col-12 col-sm-6 col-xl-3">

                    <div class="admin-stat-card">

                        <div class="admin-stat-top">
                            <span>Total Enquiries</span>
                            <i class="ri-mail-line"></i>
                        </div>

                        <h3><?= number_format($dashboardStats['enquiries']) ?></h3>

                        <p>Total received enquiries</p>

                    </div>

                </div>

                <!-- Blogs -->
                <div class="col-12 col-sm-6 col-xl-3">

                    <div class="admin-stat-card">

                        <div class="admin-stat-top">
                            <span>Published Blogs</span>
                            <i class="ri-article-line"></i>
                        </div>

                        <h3><?= number_format($dashboardStats['blogs']) ?></h3>

                        <p>Published articles</p>

                    </div>

                </div>

                <!-- Locations -->
                <div class="col-12 col-sm-6 col-xl-3">

                    <div class="admin-stat-card">

                        <div class="admin-stat-top">
                            <span>Locations</span>
                            <i class="ri-map-pin-line"></i>
                        </div>

                        <h3><?= number_format($dashboardStats['locations']) ?></h3>

                        <p>Available locations</p>

                    </div>

                </div>

            </div>

            <!-- Quick Actions -->
            <section class="admin-quick-actions">

                <h2>Quick Actions</h2>

                <div class="row g-3">

                    <!-- Add Property -->
                    <div class="col-12 col-md-4">

                        <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/admin/properties/index.php"
                           class="admin-quick-card">

                            <i class="ri-home-add-line"></i>

                            <span>
                                <strong>Add Property</strong>
                                <small>Create a new property listing</small>
                            </span>

                            <i class="ri-arrow-right-line"></i>

                        </a>

                    </div>

                    <!-- Manage Blogs -->
                    <div class="col-12 col-md-4">

                        <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/admin/blog/index.php"
                           class="admin-quick-card">
                            <i class="ri-file-edit-line"></i>
                            <span>
                                <strong>Create Blog</strong>
                                <small>Manage and publish real estate content</small>
                            </span>

                            <i class="ri-arrow-right-line"></i>

                        </a>

                    </div>

                    <!-- Manage Locations -->
                    <div class="col-12 col-md-4">

                        <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/admin/locations/index.php"
                           class="admin-quick-card"> 
                            <i class="ri-map-pin-add-line"></i> 
                            <span>
                                <strong>Manage Locations</strong>
                                <small>Update available locations</small>
                            </span> 
                            <i class="ri-arrow-right-line"></i>

                        </a>

                    </div>

                </div>

            </section> 
        </main> 
        <?php include __DIR__ . '/includes/footer.php'; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/admin/js/admin.js"></script>

</body>
</html>
