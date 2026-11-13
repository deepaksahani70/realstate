<?php
require_once __DIR__ . '/admin/config/database.php';

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$locationSearch = isset($_GET['search']) && is_string($_GET['search'])
    ? trim($_GET['search'])
    : '';

$selectedState = isset($_GET['state']) && is_string($_GET['state'])
    ? trim($_GET['state'])
    : '';

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

/* Get states for filter */
$stateStmt = $pdo->query(
    'SELECT DISTINCT state
     FROM locations
     WHERE state <> ""
     ORDER BY state ASC'
);
$states = $stateStmt->fetchAll(PDO::FETCH_COLUMN);

/* Build filters */
$where = [];
$params = [];

if ($locationSearch !== '') {
    $where[] = '(
        name LIKE :search_name
        OR district LIKE :search_district
        OR state LIKE :search_state
        OR pincode LIKE :search_pincode
    )';

    $searchValue = '%' . $locationSearch . '%';

    $params['search_name'] = $searchValue;
    $params['search_district'] = $searchValue;
    $params['search_state'] = $searchValue;
    $params['search_pincode'] = $searchValue;
}

if ($selectedState !== '') {
    $where[] = 'state = :selected_state';
    $params['selected_state'] = $selectedState;
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* Total locations */
$countStmt = $pdo->prepare(
    "SELECT COUNT(*) FROM locations {$whereSql}"
);
$countStmt->execute($params);

$totalLocations = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalLocations / $perPage));

if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

/* Fetch locations */
$locationStmt = $pdo->prepare(
    "SELECT id, name, district, state, pincode, image
     FROM locations
     {$whereSql}
     ORDER BY name ASC
     LIMIT :limit OFFSET :offset"
);

foreach ($params as $key => $value) {
    $locationStmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
}

$locationStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$locationStmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$locationStmt->execute();
$locations = $locationStmt->fetchAll();

$baseUrl = rtrim(APP_URL, '/');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Explore Locations | Real Estate</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet" />

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css">
</head>

<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <main>

        <?php
        $pageTitle = 'Explore Locations';
        $breadcrumbItems = [
            ['label' => 'Locations', 'url' => null]
        ];
        include __DIR__ . '/includes/breadcrumb.php';
        ?>

        <!-- Location Search Section -->
        <section class="location-search-section section section-white" id="location-search">
            <div class="container">

                <div class="section-heading location-search-heading">

                    <span class="section-subtitle">
                        <i class="ri-map-pin-line"></i>
                        FIND YOUR PLACE
                    </span>

                    <h2 class="section-title">
                        Explore Properties
                        <span>By Location</span>
                    </h2>

                    <p class="section-description">
                        Discover properties in your preferred city or neighbourhood
                        and find a location that feels right for you.
                    </p>

                </div>

                <div class="location-search-box">

                    <form action="<?= e($baseUrl) ?>/locations.php"
                        method="GET"
                        class="location-search-form">

                        <div class="location-search-input-wrap">
                            <i class="ri-map-pin-2-line"></i>

                            <input
                                type="search"
                                name="search"
                                value="<?= e($locationSearch) ?>"
                                placeholder="Search city, area or neighbourhood..."
                                aria-label="Search properties by location"
                                autocomplete="off">
                        </div>

                        <div class="location-search-input-wrap">
                            <i class="ri-government-line"></i>

                            <select name="state" aria-label="Filter by state">
                                <option value="">All States</option>

                                <?php foreach ($states as $state): ?>
                                    <option
                                        value="<?= e($state) ?>"
                                        <?= $selectedState === $state ? 'selected' : '' ?>>
                                        <?= e($state) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary location-search-button">
                            <i class="ri-search-line"></i>
                            Search Locations
                        </button>

                    </form>

                    <div class="location-popular-searches">
                        <span>Popular searches:</span>

                        <?php
                        $popularSearches = ['Delhi', 'Gurugram', 'Noida', 'Panipat'];
                        foreach ($popularSearches as $popular):
                        ?>
                            <a href="<?= e($baseUrl) ?>/locations.php?search=<?= rawurlencode($popular) ?>">
                                <?= e($popular) ?>
                            </a>
                        <?php endforeach; ?>

                    </div>

                </div>

            </div>
        </section>


        <!-- Properties by Location -->
        <section class="property-locations-section section section-soft" id="property-locations">

            <div class="container">

                <?php if ($locationSearch !== '' || $selectedState !== ''): ?>
                    <div class="mb-4">
                        <p class="mb-0">
                            Showing <strong><?= number_format($totalLocations) ?></strong>
                            location(s)

                            <?php if ($locationSearch !== ''): ?>
                                for <strong><?= e($locationSearch) ?></strong>
                            <?php endif; ?>

                            <?php if ($selectedState !== ''): ?>
                                in <strong><?= e($selectedState) ?></strong>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endif; ?>


                <div class="property-locations-grid">

                    <?php if (!empty($locations)): ?>

                        <?php foreach ($locations as $location): ?>

                            <?php
                            $locationUrl = $baseUrl . '/properties.php?location_id='
                                . (int) $location['id'];

                            $locationImage = !empty($location['image'])
                                ? $baseUrl . '/' . ltrim($location['image'], '/')
                                : 'https://images.unsplash.com/photo-1587474260584-136574528ed5?auto=format&fit=crop&w=900&q=85';

                            $locationLabel = $location['district'] !== ''
                                ? $location['district'] . ', ' . $location['state']
                                : $location['state'];
                            ?>

                            <article class="property-location-card">

                                <a href="<?= e($locationUrl) ?>"
                                    class="property-location-image"
                                    aria-label="Explore properties in <?= e($location['name']) ?>">

                                    <img
                                        src="<?= e($locationImage) ?>"
                                        alt="<?= e($location['name']) ?> city"
                                        loading="lazy"
                                        onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1587474260584-136574528ed5?auto=format&fit=crop&w=900&q=85';">

                                    <span class="property-location-count">
                                        Explore Properties
                                    </span>

                                </a>

                                <div class="property-location-content">

                                    <div>
                                        <h3><?= e($location['name']) ?></h3>

                                        <p>
                                            <i class="ri-map-pin-line"></i>
                                            <?= e($locationLabel) ?>
                                        </p>
                                    </div>

                                    <a href="<?= e($locationUrl) ?>"
                                        class="property-location-arrow"
                                        aria-label="View properties in <?= e($location['name']) ?>">

                                        <i class="ri-arrow-right-up-line"></i>

                                    </a>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="w-100 text-center py-5">
                            <i class="ri-map-pin-line fs-1 text-muted"></i>

                            <h3 class="mt-3">No Locations Found</h3>

                            <p class="text-muted">
                                Try another city, district or state.
                            </p>

                            <a href="<?= e($baseUrl) ?>/locations.php" class="btn btn-primary">
                                View All Locations
                            </a>
                        </div>

                    <?php endif; ?>

                </div>


                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>

                    <nav aria-label="Location pagination" class="mt-5">

                        <ul class="pagination justify-content-center">

                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link"
                                    href="?<?= e(http_build_query([
                                        'search' => $locationSearch,
                                        'state' => $selectedState,
                                        'page' => max(1, $page - 1)
                                    ])) ?>">
                                    Previous
                                </a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                                <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="?<?= e(http_build_query([
                                            'search' => $locationSearch,
                                            'state' => $selectedState,
                                            'page' => $i
                                        ])) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>

                            <?php endfor; ?>

                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link"
                                    href="?<?= e(http_build_query([
                                        'search' => $locationSearch,
                                        'state' => $selectedState,
                                        'page' => min($totalPages, $page + 1)
                                    ])) ?>">
                                    Next
                                </a>
                            </li>

                        </ul>

                    </nav>

                <?php endif; ?>

            </div>

        </section>

    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="js/header.js" defer></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <script src="js/main.js"></script>

</body>
</html>