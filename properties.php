
<?php
require_once __DIR__ . '/admin/config/database.php';

function listing_e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$baseUrl = rtrim(APP_URL, '/');

/* ---------------------------------------
   GET Parameters
--------------------------------------- */

$purpose = trim($_GET['purpose'] ?? '');
$locationSearch = trim(
    $_GET['location'] ?? $_GET['search'] ?? ''
);

$propertyType = trim($_GET['property_type'] ?? '');
$locationId = max(0, (int) ($_GET['location_id'] ?? 0));
$budget = isset($_GET['budget']) && is_numeric($_GET['budget'])
    ? (float) $_GET['budget']
    : 0;
$page = max(1, (int) ($_GET['page'] ?? 1));

/* ---------------------------------------
   Build SQL Filters
--------------------------------------- */

$where = ['p.is_active = 1'];
$params = [];

/* Buy / Rent / Commercial */

if ($purpose === 'buy') {
    $where[] = "p.listing_type = ?";
    $params[] = 'sale';

} elseif ($purpose === 'rent') {
    $where[] = "p.listing_type = ?";
    $params[] = 'rent';

} elseif ($purpose === 'commercial') {
    $where[] = "LOWER(p.property_type) LIKE ?";
    $params[] = '%commercial%';
}

/* Location Search */

if ($locationSearch !== '') {

    $where[] = "(
        l.name LIKE ?
        OR l.district LIKE ?
        OR l.state LIKE ?
        OR l.pincode LIKE ?
        OR p.title LIKE ?
        OR p.address LIKE ?
    )";

    $term = '%' . $locationSearch . '%';
    array_push(
        $params,
        $term,
        $term,
        $term,
        $term,
        $term,
        $term
    );
}

/* Location ID */
if ($locationId > 0) {
    $where[] = 'p.location_id = ?';
    $params[] = $locationId;
}

/* Property Type - exact value from Admin */
if ($propertyType !== '') {
    $where[] = 'p.property_type = ?';
    $params[] = $propertyType;
}

/* Budget Filter */
if ($budget > 0) {
    if ($budget >= 50000000) {
        // ₹5 Crore+
        $where[] = 'p.price >= ?';
    } else {
        // Up to selected budget
        $where[] = 'p.price <= ?';
    }

    $params[] = $budget;
}

/* ---------------------------------------
   Pagination
--------------------------------------- */

$whereSql = 'WHERE ' . implode(' AND ', $where);
$perPage = 9;

/* Total Properties */
$countStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM properties p
    INNER JOIN locations l ON l.id = p.location_id
    $whereSql
");
$countStmt->execute($params);
$totalProperties = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalProperties / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;
/* ---------------------------------------
   Fetch Properties
--------------------------------------- */
$stmt = $pdo->prepare("
    SELECT
        p.*,
        l.name AS location_name,
        l.district,
        l.state,
        (
            SELECT pi.image_path
            FROM property_images pi
            WHERE pi.property_id = p.id
            ORDER BY
                pi.is_primary DESC,
                pi.sort_order ASC,
                pi.id ASC
            LIMIT 1
        ) AS main_image
    FROM properties p
    INNER JOIN locations l
        ON l.id = p.location_id
    $whereSql
    ORDER BY
        p.is_featured DESC,
        p.created_at DESC
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$properties = $stmt->fetchAll();
/* ---------------------------------------
   Page Details
--------------------------------------- */

$pageTitle = 'Explore Properties';
$breadcrumbItems = [
    ['label' => 'Properties', 'url' => null]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Explore Properties | Real Estate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="<?= listing_e($baseUrl) ?>/css/style.css">
    <link rel="stylesheet" href="<?= listing_e($baseUrl) ?>/css/header.css">
    <link rel="stylesheet" href="<?= listing_e($baseUrl) ?>/css/footer.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<main>
    <?php include __DIR__ . '/includes/breadcrumb.php'; ?>
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
                <form
                    action="<?= listing_e($baseUrl) ?>/properties.php"
                    method="GET"
                    class="location-search-form"
                >
                    <div class="location-search-input-wrap">
                        <i class="ri-map-pin-2-line"></i>
                        <input
                            type="search"
                            name="search"
                            value="<?= listing_e($locationSearch) ?>"
                            placeholder="Search city, area or neighbourhood..."
                            aria-label="Search properties by location"
                            autocomplete="off"
                        >
                    </div>
                    <button type="submit" class="btn btn-primary location-search-button">
                        <i class="ri-search-line"></i>
                        Search Properties
                    </button>
                </form>

                <div class="location-popular-searches">
                    <span>Popular searches:</span>
                    <?php foreach (['Delhi', 'Gurugram', 'Noida', 'Panipat'] as $popular): ?>
                        <a href="<?= listing_e($baseUrl) ?>/properties.php?search=<?= rawurlencode($popular) ?>">
                            <?= listing_e($popular) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <!-- Featured Properties Section -->
    <section class="re-featured-section" id="featured-properties">
        <div class="container">
            <div class="re-featured-grid">
                <?php if (!$properties): ?>
                    <div class="text-center py-5 w-100">
                        <i class="ri-home-4-line fs-1"></i>
                        <h3>No Properties Found</h3>
                        <p>
                            We couldn't find any properties matching your search.
                        </p>
                        <a href="<?= listing_e($baseUrl) ?>/properties.php" class="btn btn-primary">
                            View All Properties
                        </a>
                    </div>
                <?php endif; ?>

                <?php foreach ($properties as $property): ?>
                    <?php
                    $propertyUrl = $baseUrl . '/properties/properties-details.php?id=' . (int) $property['id'];
                    $imageUrl = !empty($property['main_image'])
                        ? $baseUrl . '/' . $property['main_image']
                        : 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1000&q=85';
                    $priceText = !empty($property['price_label'])
                        ? $property['price_label']
                        : '₹' . number_format((float) $property['price']);
                    $listingLabel = $property['listing_type'] === 'rent'
                        ? 'For Rent'
                        : 'For Sale';

                    $statusLabel = [
                        'available' => $listingLabel,
                        'sold' => 'Sold',
                        'rented' => 'Rented'
                    ][$property['listing_status']] ?? $listingLabel;
                    ?>
                    <article class="re-premium-property">
                        <div class="re-premium-property-image">
                            <img
                                src="<?= listing_e($imageUrl) ?>"
                                alt="<?= listing_e($property['title']) ?>"
                                loading="lazy"
                            >
                            <div class="re-premium-image-overlay"></div>
                            <?php if ($property['is_featured']): ?>
                                <span class="re-premium-badge">
                                    <i class="ri-verified-badge-fill"></i>
                                    Featured
                                </span>
                            <?php endif; ?>
                            <div class="re-premium-image-bottom">
                                <span>
                                    <i class="ri-map-pin-line"></i>
                                    <?= listing_e($property['location_name']) ?>,
                                    <?= listing_e($property['state']) ?>
                                </span>
                                <span class="re-premium-status">
                                    <?= listing_e($statusLabel) ?>
                                </span>
                            </div>
                        </div>

                        <div class="re-premium-property-content">
                            <div class="re-premium-price-row">
                                <div class="re-premium-price">
                                    <?= listing_e($priceText) ?>
                                </div>
                                <span class="re-premium-property-id">
                                    <?= listing_e($property['property_code']) ?>
                                </span>
                            </div>
                            <h4 class="re-premium-property-title">
                                <a href="<?= listing_e($propertyUrl) ?>">
                                    <?= listing_e($property['title']) ?>
                                </a>
                            </h4>
                            <p class="re-premium-address">
                                <i class="ri-map-pin-2-line"></i>
                                <?= listing_e($property['district']) ?>,
                                <?= listing_e($property['state']) ?>
                            </p>
                            <div class="re-premium-specs">
                                <div>
                                    <i class="ri-hotel-bed-line"></i>
                                    <span><?= (int) $property['bedrooms'] ?> Beds</span>
                                </div>
                                <div>
                                    <i class="ri-showers-line"></i>
                                    <span><?= (int) $property['bathrooms'] ?> Baths</span>
                                </div>
                                <div>
                                    <i class="ri-ruler-line"></i>
                                    <span>
                                        <?= $property['area'] !== null ? listing_e($property['area']) : 'N/A' ?>
                                        <?= listing_e($property['area_unit']) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="re-premium-card-footer">
                                <span class="re-premium-verified">
                                    <i class="ri-checkbox-circle-fill"></i>
                                    <?= listing_e($property['property_type']) ?>
                                </span>
                                <a
                                    href="<?= listing_e($propertyUrl) ?>"
                                    aria-label="View <?= listing_e($property['title']) ?>"
                                >
                                    <i class="ri-arrow-right-up-line"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?> 
                <div class="d-flex justify-content-center gap-2 mt-5 flex-wrap">
                    <?php
                    $paginationParams = $_GET;
                    unset($paginationParams['page']);
                    ?>
                    <?php if ($page > 1): ?>
                        <a
                            class="btn btn-outline-primary btn-sm"
                            href="?<?= listing_e(http_build_query($paginationParams)) ?>&page=<?= $page - 1 ?>"
                        >
                            Previous
                        </a>
                    <?php endif; ?>
                    <span class="align-self-center">
                        Page <?= $page ?> of <?= $totalPages ?>
                    </span>
                    <?php if ($page < $totalPages): ?>
                        <a
                            class="btn btn-outline-primary btn-sm"
                            href="?<?= listing_e(http_build_query($paginationParams)) ?>&page=<?= $page + 1 ?>"
                        >
                            Next
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="<?= listing_e($baseUrl) ?>/js/header.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script src="<?= listing_e($baseUrl) ?>/js/main.js"></script>
</body>
</html>
