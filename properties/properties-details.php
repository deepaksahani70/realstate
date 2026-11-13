
<?php
require_once __DIR__ . '/../admin/config/database.php';

function details_e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$baseUrl = rtrim(APP_URL, '/');
$propertyId = max(0, (int) ($_GET['id'] ?? 0));

if (!$propertyId) {
    http_response_code(404);
    exit('Property not found.');
}

/*
 * FETCH PROPERTY
 */
$stmt = $pdo->prepare("
    SELECT
        p.*,
        l.name AS location_name,
        l.district,
        l.state,
        l.pincode
    FROM properties p
    INNER JOIN locations l ON l.id = p.location_id
    WHERE p.id = ? AND p.is_active = 1
    LIMIT 1
");
$stmt->execute([$propertyId]);
$property = $stmt->fetch();

if (!$property) {
    http_response_code(404);
    exit('Property not found or currently unavailable.');
}

/*
 * FETCH PROPERTY IMAGES
 */
$stmt = $pdo->prepare("
    SELECT *
    FROM property_images
    WHERE property_id = ?
    ORDER BY is_primary DESC, sort_order ASC, id ASC
");

$stmt->execute([$propertyId]);
$propertyImages = $stmt->fetchAll();

$fallbackImage = 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1400&q=85';

if (!$propertyImages) {
    $propertyImages[] = [
        'image_path' => null,
        'is_primary' => 1
    ];
}

/*
 * PROPERTY AMENITIES
 */
$amenities = json_decode($property['amenities'] ?? '[]', true);

if (!is_array($amenities)) {
    $amenities = [];
}

/*
 * DISPLAY VALUES
 */
$propertyLocation = implode(', ', array_filter([
    $property['location_name'],
    $property['district'],
    $property['state']
]));

$propertyAddress = $property['address'] ?: $propertyLocation;

$priceText = $property['price_label'] ?: '₹' . number_format((float) $property['price']);

$listingLabel = $property['listing_type'] === 'rent'
    ? 'For Rent'
    : 'For Sale';

$propertyStatus = [
    'available' => $listingLabel,
    'sold' => 'Sold',
    'rented' => 'Rented'
][$property['listing_status']] ?? $listingLabel;

/*
 * SIMILAR PROPERTIES
 */
$similarStmt = $pdo->prepare("
    SELECT
        p.*,
        l.name AS location_name,
        l.state,
        (
            SELECT pi.image_path
            FROM property_images pi
            WHERE pi.property_id = p.id
            ORDER BY pi.is_primary DESC, pi.sort_order ASC, pi.id ASC
            LIMIT 1
        ) AS main_image
    FROM properties p
    INNER JOIN locations l ON l.id = p.location_id
    WHERE
        p.is_active = 1
        AND p.id != ?
        AND p.location_id = ?
    ORDER BY p.is_featured DESC, p.created_at DESC
    LIMIT 3
");

$similarStmt->execute([
    $propertyId,
    $property['location_id']
]);

$similarProperties = $similarStmt->fetchAll();
$pageTitle = $property['title'];
$breadcrumbItems = [
    [
        'label' => 'Properties',
        'url' => $baseUrl . '/properties.php'
    ],
    [
        'label' => $property['title'],
        'url' => null
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= details_e($property['title']) ?> | Property Details</title>
    <meta
        name="description"
        content="<?= details_e(substr(strip_tags($property['description'] ?? ''), 0, 155)) ?>"
    >
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="<?= details_e($baseUrl) ?>/css/style.css">
    <link rel="stylesheet" href="<?= details_e($baseUrl) ?>/css/header.css">
    <link rel="stylesheet" href="<?= details_e($baseUrl) ?>/css/footer.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css">
</head>

<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<main class="property-details-page">
    <?php include __DIR__ . '/../includes/breadcrumb.php'; ?>

    <!-- Property Gallery -->
    <section class="property-detail-gallery-section">
        <div class="container">
            <div class="property-gallery">
                <?php
                $mainImage = $propertyImages[0]['image_path']
                    ? $baseUrl . '/' . $propertyImages[0]['image_path']
                    : $fallbackImage;
                ?>
                <a
                    href="<?= details_e($mainImage) ?>"
                    class="property-gallery-main"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Open main property image"
                >
                    <img
                        src="<?= details_e($mainImage) ?>"
                        alt="<?= details_e($property['title']) ?>"
                    >
                </a>
                <div class="property-gallery-thumbnails">
                    <?php foreach (array_slice($propertyImages, 1, 4) as $index => $image): ?>
                        <?php
                        $imageUrl = $image['image_path']
                            ? $baseUrl . '/' . $image['image_path']
                            : $fallbackImage;
                        ?>

                        <a
                            href="<?= details_e($imageUrl) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Open property photo <?= $index + 2 ?>"
                        >
                            <img
                                src="<?= details_e($imageUrl) ?>"
                                alt="<?= details_e($property['title']) ?> photo <?= $index + 2 ?>"
                                loading="lazy"
                            >
                        </a>
                    <?php endforeach; ?>
                </div>
                <span class="property-gallery-badge">
                    <i class="ri-camera-line"></i>
                    <?= count($propertyImages) ?> Photos
                </span>
            </div>
        </div>

    </section>


    <!-- Property Overview + Enquiry Sidebar -->
    <section class="property-overview-section section section-white">
        <div class="container">
            <div class="property-detail-layout">
                <!-- Main Content -->
                <div class="property-detail-main">
                    <!-- Property Overview -->
                    <div class="property-overview-top">
                        <div class="property-overview-info">
                            <div class="property-status-row">
                                <span class="property-status">
                                    <?= details_e($propertyStatus) ?>
                                </span>
                                <span class="property-reference">
                                    ID: <?= details_e($property['property_code']) ?>
                                </span>
                            </div>
                            <h1>
                                <?= details_e($property['title']) ?>
                            </h1>
                            <p class="property-overview-location">
                                <i class="ri-map-pin-line"></i>
                                <?= details_e($propertyLocation) ?>
                            </p>
                        </div>
                        <div class="property-overview-price">
                            <span>Asking Price</span>
                            <strong>
                                <?= details_e($priceText) ?>
                            </strong>
                        </div>
                    </div>
                    <!-- Quick Specifications -->
                    <div class="property-quick-specs">

                        <div class="property-quick-spec">

                            <i class="ri-hotel-bed-line"></i>

                            <div>
                                <strong><?= (int) $property['bedrooms'] ?></strong>
                                <span>Bedrooms</span>
                            </div>

                        </div>


                        <div class="property-quick-spec">

                            <i class="ri-showers-line"></i>

                            <div>
                                <strong><?= (int) $property['bathrooms'] ?></strong>
                                <span>Bathrooms</span>
                            </div>

                        </div>


                        <div class="property-quick-spec">

                            <i class="ri-ruler-line"></i>

                            <div>
                                <strong>
                                    <?= $property['area'] !== null ? details_e($property['area']) : 'N/A' ?>
                                    <?= details_e($property['area_unit']) ?>
                                </strong>
                                <span>Built-up Area</span>
                            </div>

                        </div>


                        <div class="property-quick-spec">

                            <i class="ri-car-line"></i>

                            <div>
                                <strong><?= details_e($property['parking'] ?: 'N/A') ?></strong>
                                <span>Parking</span>
                            </div>

                        </div>

                    </div>


                    <!-- Description -->
                    <section class="property-detail-block" id="property-description">

                        <div class="property-block-heading">

                            <span class="property-block-icon">
                                <i class="ri-file-text-line"></i>
                            </span>

                            <h2>About This Property</h2>

                        </div>

                        <p class="property-description-text">
                            <?= nl2br(details_e($property['description'] ?: 'Property description will be updated soon.')) ?>
                        </p>

                    </section>


                    <!-- Key Details -->
                    <section class="property-detail-block" id="property-features">

                        <div class="property-block-heading">

                            <span class="property-block-icon">
                                <i class="ri-list-check-3"></i>
                            </span>

                            <h2>Property Details</h2>

                        </div>


                        <div class="property-details-table">

                            <div>
                                <span>Property Type</span>
                                <strong><?= details_e($property['property_type']) ?></strong>
                            </div>

                            <div>
                                <span>Listing Type</span>
                                <strong><?= details_e($listingLabel) ?></strong>
                            </div>

                            <div>
                                <span>Listing Status</span>
                                <strong><?= details_e($propertyStatus) ?></strong>
                            </div>

                            <div>
                                <span>Property ID</span>
                                <strong><?= details_e($property['property_code']) ?></strong>
                            </div>

                            <div>
                                <span>Bedrooms</span>
                                <strong><?= (int) $property['bedrooms'] ?></strong>
                            </div>

                            <div>
                                <span>Bathrooms</span>
                                <strong><?= (int) $property['bathrooms'] ?></strong>
                            </div>

                            <div>
                                <span>Built-up Area</span>
                                <strong>
                                    <?= $property['area'] !== null ? details_e($property['area']) : 'N/A' ?>
                                    <?= details_e($property['area_unit']) ?>
                                </strong>
                            </div>

                            <div>
                                <span>Parking</span>
                                <strong><?= details_e($property['parking'] ?: 'N/A') ?></strong>
                            </div>

                            <div>
                                <span>Location</span>
                                <strong><?= details_e($propertyLocation) ?></strong>
                            </div>

                        </div>

                    </section>


                    <!-- Amenities -->
                    <section class="property-detail-block" id="property-amenities">

                        <div class="property-block-heading">

                            <span class="property-block-icon">
                                <i class="ri-star-line"></i>
                            </span>

                            <h2>Features & Amenities</h2>

                        </div>


                        <div class="property-amenities-grid">

                            <?php if ($amenities): ?>

                                <?php foreach ($amenities as $amenity): ?>

                                    <div>
                                        <i class="ri-check-line"></i>
                                        <?= details_e($amenity) ?>
                                    </div>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <p>Amenities information will be updated soon.</p>

                            <?php endif; ?>

                        </div>

                    </section>


                    <!-- Location -->
                    <section class="property-detail-block" id="property-location">

                        <div class="property-block-heading">

                            <span class="property-block-icon">
                                <i class="ri-map-pin-line"></i>
                            </span>

                            <h2>Property Location</h2>

                        </div>


                        <div class="property-address-card">

                            <i class="ri-map-pin-2-line"></i>

                            <div>

                                <strong>Address</strong>

                                <p><?= details_e($propertyAddress) ?></p>

                            </div>

                        </div>


                        <a
                            class="property-map-link"
                            href="https://www.google.com/maps/search/?api=1&query=<?= rawurlencode($propertyAddress) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                        >

                            <i class="ri-map-2-line"></i>

                            View on Google Maps

                            <i class="ri-arrow-right-up-line"></i>

                        </a>

                    </section>

                </div>


                <!-- Enquiry Sidebar -->
                <aside class="property-enquiry-sidebar">

                    <div class="property-enquiry-card">

                        <span class="property-enquiry-eyebrow">
                            INTERESTED IN THIS PROPERTY?
                        </span>

                        <h2>Schedule a Visit</h2>

                        <p>
                            Send your details and our team can contact you about this listing.
                        </p>


                        <form
                            action="<?= details_e($baseUrl) ?>/property-enquiry.php"
                            method="POST"
                            class="property-enquiry-form"
                        >

                            <input
                                type="hidden"
                                name="property_id"
                                value="<?= (int) $property['id'] ?>"
                            >


                            <div class="property-enquiry-field">

                                <label for="enquiryName">Full Name</label>

                                <input
                                    type="text"
                                    id="enquiryName"
                                    name="name"
                                    placeholder="Enter your name"
                                    maxlength="100"
                                    autocomplete="name"
                                    required
                                >

                            </div>


                            <div class="property-enquiry-field">

                                <label for="enquiryPhone">Phone Number</label>

                                <input
                                    type="tel"
                                    id="enquiryPhone"
                                    name="phone"
                                    placeholder="+91 00000 00000"
                                    maxlength="20"
                                    autocomplete="tel"
                                    required
                                >

                            </div>


                            <div class="property-enquiry-field">

                                <label for="enquiryEmail">Email Address</label>

                                <input
                                    type="email"
                                    id="enquiryEmail"
                                    name="email"
                                    placeholder="you@example.com"
                                    maxlength="254"
                                    autocomplete="email"
                                    required
                                >

                            </div>


                            <div class="property-enquiry-field">

                                <label for="enquiryMessage">Message</label>

                                <textarea
                                    id="enquiryMessage"
                                    name="message"
                                    rows="4"
                                    maxlength="2000"
                                    required
                                >I'm interested in <?= details_e($property['title']) ?>. Please contact me with more details.</textarea>

                            </div>


                            <button type="submit" class="btn btn-primary property-enquiry-submit">

                                Send Enquiry

                                <i class="ri-arrow-right-line"></i>

                            </button>


                            <p class="property-enquiry-privacy">

                                <i class="ri-lock-line"></i>

                                Your details will be handled responsibly.

                            </p>

                        </form>

                    </div>


                    <div class="property-help-card">

                        <div class="property-help-icon">
                            <i class="ri-customer-service-2-line"></i>
                        </div>

                        <div>

                            <h3>Need Assistance?</h3>

                            <p>Have questions about this listing?</p>

                            <a href="<?= details_e($baseUrl) ?>/contact-us.php">
                                Contact our team
                                <i class="ri-arrow-right-line"></i>
                            </a>

                        </div>

                    </div>

                </aside>

            </div>

        </div>

    </section>


    <!-- Similar Properties -->
    <section class="similar-properties-section section section-soft" id="similar-properties">

        <div class="container">

            <div class="section-heading similar-properties-heading">

                <span class="section-subtitle">

                    <i class="ri-home-heart-line"></i>

                    YOU MAY ALSO LIKE

                </span>

                <h2 class="section-title">
                    Explore Similar <span>Properties</span>
                </h2>

                <p class="section-description">
                    Discover more properties that may match your preferences.
                </p>

            </div>


            <?php if ($similarProperties): ?>

                <div class="re-featured-grid">

                    <?php foreach ($similarProperties as $similar): ?>

                        <?php
                        $similarImage = $similar['main_image']
                            ? $baseUrl . '/' . $similar['main_image']
                            : $fallbackImage;

                        $similarPrice = $similar['price_label']
                            ?: '₹' . number_format((float) $similar['price']);

                        $similarUrl = $baseUrl . '/properties/properties-details.php?id=' . (int) $similar['id'];
                        ?>

                        <article class="re-premium-property">

                            <div class="re-premium-property-image">

                                <img
                                    src="<?= details_e($similarImage) ?>"
                                    alt="<?= details_e($similar['title']) ?>"
                                    loading="lazy"
                                >

                                <div class="re-premium-image-overlay"></div>

                                <?php if ($similar['is_featured']): ?>

                                    <span class="re-premium-badge">
                                        <i class="ri-verified-badge-fill"></i>
                                        Featured
                                    </span>

                                <?php endif; ?>

                                <div class="re-premium-image-bottom">

                                    <span>
                                        <i class="ri-map-pin-line"></i>
                                        <?= details_e($similar['location_name']) ?>,
                                        <?= details_e($similar['state']) ?>
                                    </span>

                                    <span class="re-premium-status">
                                        <?= $similar['listing_type'] === 'rent' ? 'For Rent' : 'For Sale' ?>
                                    </span>

                                </div>

                            </div>


                            <div class="re-premium-property-content">

                                <div class="re-premium-price-row">

                                    <div class="re-premium-price">
                                        <?= details_e($similarPrice) ?>
                                    </div>

                                    <span class="re-premium-property-id">
                                        <?= details_e($similar['property_code']) ?>
                                    </span>

                                </div>


                                <h4 class="re-premium-property-title">
                                    <a href="<?= details_e($similarUrl) ?>">
                                        <?= details_e($similar['title']) ?>
                                    </a>
                                </h4>


                                <p class="re-premium-address">
                                    <i class="ri-map-pin-2-line"></i>
                                    <?= details_e($similar['location_name']) ?>,
                                    <?= details_e($similar['state']) ?>
                                </p>


                                <div class="re-premium-specs">

                                    <div>
                                        <i class="ri-hotel-bed-line"></i>
                                        <span><?= (int) $similar['bedrooms'] ?> Beds</span>
                                    </div>

                                    <div>
                                        <i class="ri-showers-line"></i>
                                        <span><?= (int) $similar['bathrooms'] ?> Baths</span>
                                    </div>

                                    <div>
                                        <i class="ri-ruler-line"></i>
                                        <span>
                                            <?= $similar['area'] !== null ? details_e($similar['area']) : 'N/A' ?>
                                            <?= details_e($similar['area_unit']) ?>
                                        </span>
                                    </div>

                                </div>


                                <div class="re-premium-card-footer">

                                    <span class="re-premium-verified">
                                        <i class="ri-checkbox-circle-fill"></i>
                                        <?= details_e($similar['property_type']) ?>
                                    </span>

                                    <a href="<?= details_e($similarUrl) ?>">
                                        <i class="ri-arrow-right-up-line"></i>
                                    </a>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="similar-properties-placeholder">

                    <i class="ri-building-4-line"></i>
                    <h3>Discover More Properties</h3>
                    <p>
                        Browse our listings to find more homes and investment opportunities.
                    </p>
                    <a href="<?= details_e($baseUrl) ?>/properties.php" class="btn btn-primary">
                        Browse All Properties
                        <i class="ri-arrow-right-line"></i>
                    </a>
                </div>

            <?php endif; ?>

        </div>

    </section>

</main>


<?php include __DIR__ . '/../includes/footer.php'; ?>


<script src="<?= details_e($baseUrl) ?>/js/header.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script src="<?= details_e($baseUrl) ?>/js/main.js"></script>

</body>
</html>
