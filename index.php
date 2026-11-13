<?php
require_once __DIR__ . '/admin/config/database.php';

/* ---------------------------------------
   Homepage Testimonials
--------------------------------------- */

$testimonials = [];
try {
    $testimonialStmt = $pdo->query("
        SELECT
            id,
            customer_name,
            location,
            review,
            rating
        FROM user_reviews
        WHERE is_active = 1
        ORDER BY sort_order ASC, id DESC
    ");
    $testimonials = $testimonialStmt->fetchAll();
} catch (PDOException $e) {
    error_log('Homepage testimonials error: ' . $e->getMessage());
    $testimonials = [];
}
/* Generate Customer Initials */
$testimonialInitials = static function ($name) {
    $name = trim((string) $name);
    if ($name === '') {
        return 'CU';
    }
    $words = preg_split('/\s+/', $name);
    $initials = '';

    foreach (array_slice($words, 0, 2) as $word) {
        $initials .= mb_substr($word, 0, 1);
    }
    return mb_strtoupper($initials);
};
/* ---------------------------------------
   Homepage property filters
--------------------------------------- */

$purpose = $_GET['purpose'] ?? 'buy';
$allowedPurposes = ['buy', 'rent', 'commercial'];
if (!in_array($purpose, $allowedPurposes, true)) {
    $purpose = 'buy';
}

$location = trim($_GET['location'] ?? '');
$propertyType = trim($_GET['property_type'] ?? '');
$budget = (float) ($_GET['budget'] ?? 0);

$where = ['p.is_active = 1'];
$params = [];

if ($purpose === 'buy') {
    $where[] = "p.listing_type = 'sale'";
} elseif ($purpose === 'rent') {
    $where[] = "p.listing_type = 'rent'";
} elseif ($purpose === 'commercial') {
    $where[] = "p.property_type = 'commercial'";
}

/* Property type filter */
if ($propertyType !== '' && $purpose !== 'commercial') {
    $where[] = 'p.property_type = :property_type';
    $params[':property_type'] = $propertyType;
}


if ($location !== '') {
    $where[] = "(
        l.name LIKE :location1
        OR l.district LIKE :location2
        OR l.state LIKE :location3
        OR l.pincode LIKE :location4
        OR p.address LIKE :location5
        OR p.title LIKE :location6
    )";

    $searchLocation = '%' . $location . '%';

    $params[':location1'] = $searchLocation;
    $params[':location2'] = $searchLocation;
    $params[':location3'] = $searchLocation;
    $params[':location4'] = $searchLocation;
    $params[':location5'] = $searchLocation;
    $params[':location6'] = $searchLocation;
}

/* Budget filter */
if ($budget > 0) {
    if ($budget >= 50000000) {
        $where[] = 'p.price >= :budget';
    } else {
        // Up to selected budget
        $where[] = 'p.price <= :budget';
    }

    $params[':budget'] = $budget;
}

/* Fetch latest 3 properties */
$sql = "
    SELECT
        p.id,
        p.property_code,
        p.title,
        p.property_type,
        p.listing_type,
        p.listing_status,
        p.price,
        p.price_label,
        p.bedrooms,
        p.bathrooms,
        p.area,
        p.area_unit,
        p.address,
        p.is_featured,
        l.name AS location_name,
        l.district,
        l.state,
        (
            SELECT pi.image_path
            FROM property_images pi
            WHERE pi.property_id = p.id
            ORDER BY pi.is_primary DESC, pi.sort_order ASC, pi.id ASC
            LIMIT 1
        ) AS property_image
    FROM properties p
    LEFT JOIN locations l ON l.id = p.location_id
    WHERE " . implode(' AND ', $where) . "
    ORDER BY p.created_at DESC, p.id DESC
    LIMIT 3
";

try {
    $propertyStmt = $pdo->prepare($sql);
    $propertyStmt->execute($params);
    $latestProperties = $propertyStmt->fetchAll();
} catch (PDOException $e) {
    error_log('Homepage properties error: ' . $e->getMessage());
    $latestProperties = [];
}

$escape = static function ($value) {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};

// BLogs section updates

$latestBlogs = [];
try {
    $blogStmt = $pdo->prepare("
        SELECT
            id,
            title,
            slug,
            category,
            excerpt,
            featured_image,
            image_alt,
            reading_time,
            published_at
        FROM blogs
        WHERE status = 'published'
          AND published_at IS NOT NULL
        ORDER BY published_at DESC, id DESC
        LIMIT 3
    ");

    $blogStmt->execute();
    $latestBlogs = $blogStmt->fetchAll();

} catch (PDOException $e) {
    error_log('Homepage latest blogs error: ' . $e->getMessage());
    $latestBlogs = [];
}

function home_team_e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function home_team_image($image) {
    global $baseUrl;
    if (empty($image)) {
        return '';
    }
    if (preg_match('/^https?:\/\//i', $image)) {
        return $image;
    }
    return $baseUrl . '/' . ltrim($image, '/');
}

try {
    $teamQuery = $pdo->prepare("
        SELECT id, name, slug, designation, short_description, image,
               linkedin, instagram, facebook
        FROM team_members
        WHERE is_active = 1
        ORDER BY sort_order ASC, id DESC
        LIMIT 6
    ");

    $teamQuery->execute();
    $homeTeamMembers = $teamQuery->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Homepage team error: ' . $e->getMessage());
    $homeTeamMembers = [];
}

/* ---------------------------------------
   Homepage Team Members
--------------------------------------- */

$homeTeamMembers = [];

try {
    $teamStmt = $pdo->query("
        SELECT
            id,
            name,
            slug,
            designation,
            short_description,
            image,
            linkedin,
            instagram,
            facebook
        FROM team_members
        WHERE is_active = 1
        ORDER BY sort_order ASC, id DESC
        LIMIT 6
    ");

    $homeTeamMembers = $teamStmt->fetchAll();

} catch (PDOException $e) {
    error_log('Homepage team error: ' . $e->getMessage());
    $homeTeamMembers = [];
}

/* Team image URL */
$teamImage = static function ($image) {
    if (empty($image)) {
        return '';
    }

    if (preg_match('/^https?:\/\//i', $image)) {
        return $image;
    }

    return rtrim(APP_URL, '/') . '/' . ltrim($image, '/');
};
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>realstae: home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet"/>
    <!-- Global Website CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <!-- Slick Carousel CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css">


</head>

<body>  
    <?php include __DIR__ . '/includes/header.php'; ?>

    <section class="re-hero">
        <div class="re-hero-bg"></div>
        <div class="container re-hero-container">
            <div class="re-hero-content">
                <span class="re-hero-eyebrow"><i class="bi bi-patch-check-fill"></i> YOUR TRUSTED REAL ESTATE PARTNER</span>
                <h1>Find a Place You'll <span>Love to Call Home.</span></h1>
                <p>Discover exceptional homes, premium apartments and investment opportunities in your preferred location.</p>
                <div class="re-hero-highlights">
                    <div><i class="bi bi-shield-check"></i><span>Verified Properties</span></div>
                    <div><i class="bi bi-people"></i><span>Expert Guidance</span></div>
                    <div><i class="bi bi-house-check"></i><span>Better Living</span></div>
                </div>
            </div>
            <div class="re-search-card">
                <div class="re-search-header">
                    <div>
                        <span class="re-search-kicker">START YOUR SEARCH</span>
                        <h2>Find Your Perfect Property</h2>
                    </div>
                    <i class="bi bi-buildings re-search-heading-icon"></i>
                </div>
                <div class="re-search-tabs" role="group" aria-label="Property purpose">
                    <button type="button" class="re-search-tab active" data-purpose="buy" aria-pressed="true"><i class="bi bi-house"></i> Buy</button>
                    <button type="button" class="re-search-tab" data-purpose="rent" aria-pressed="false"><i class="bi bi-key"></i> Rent</button>
                    <button type="button" class="re-search-tab" data-purpose="commercial" aria-pressed="false"><i class="bi bi-building"></i> Commercial</button>
                </div>
              <form action="properties.php" method="get" class="re-property-search" id="rePropertySearch">
                    <input type="hidden" name="purpose" id="reSearchPurpose" value="buy">
                    <div class="re-search-field">
                        <label for="reSearchLocation">Location</label>
                        <div class="re-search-input-wrap"><i class="bi bi-geo-alt"></i><input type="text" id="reSearchLocation" name="location" placeholder="City, area or locality" autocomplete="address-level2"></div>
                    </div>
                    <div class="re-search-field">
                        <label for="reSearchType">Property Type</label>
                        <div class="re-search-input-wrap">
                            <i class="bi bi-house-door"></i>
                            <select id="reSearchType" name="property_type">
                                <option value="">All Property Types</option>
                                <option value="Apartment">Apartment</option>
                                <option value="Flat">Flat</option>
                                <option value="Independent House">Independent House</option>
                                <option value="Villa">Villa</option>
                                <option value="Builder Floor">Builder Floor</option>
                                <option value="Plot">Plot</option>
                                <option value="Commercial">Commercial</option>
                                <option value="Office Space">Office Space</option>
                                <option value="Shop">Shop</option>
                                <option value="Land">Land</option>
                            </select>
                    </div>
                    </div>
                    <div class="re-search-field">
                        <label for="reSearchBudget">Budget</label>
                        <div class="re-search-input-wrap"><i class="bi bi-currency-rupee"></i><select id="reSearchBudget" name="budget"><option value="">Any Budget</option><option value="2500000">Up to ₹25 Lac</option><option value="5000000">Up to ₹50 Lac</option><option value="10000000">Up to ₹1 Crore</option><option value="25000000">Up to ₹2.5 Crore</option><option value="50000000">₹5 Crore+</option></select></div>
                    </div>
                    <button type="submit" class="btn btn-primary re-search-submit"><i class="bi bi-search"></i><span>Search Properties</span></button>
                </form>
                <div class="re-search-footer"><i class="bi bi-check-circle-fill"></i> Find the right property for your lifestyle and budget.</div>
            </div>
        </div>
        <div class="re-hero-bottom">
            <div><strong>Find it.</strong> Love it. Live it.</div>
            <a href="#featured-properties">Explore properties <i class="bi bi-arrow-down"></i></a>
        </div>
    </section>
    
    <!-- Featured Properties Section -->
    <section class="re-featured-section" id="featured-properties">
        <div class="container">
            <!-- Section Heading -->
            <div class="re-featured-top">
                <div class="re-featured-intro">
                    <span class="re-section-eyebrow">
                        <i class="ri-vip-crown-line"></i>
                        EXCEPTIONAL PLACES TO LIVE
                    </span>
                    <h2 class="section-title">
                        Discover <span>Exceptional Properties</span>
                    </h2>
                    <p class="section-description">
                        Explore thoughtfully selected homes that bring together timeless design,
                        comfort and outstanding locations.
                    </p>
                </div>
                <a href="properties.php" class="re-featured-view-all">
                    Explore All Properties
                    <i class="ri-arrow-right-up-line"></i>
                </a>
            </div>

            <!-- Category Toolbar -->
            <div class="re-featured-toolbar">
                <div class="re-featured-toolbar-label">
                    <i class="ri-home-5-line"></i>
                    Handpicked for your lifestyle
                </div>
                 <div class="re-featured-categories">
                    <a href="index.php?purpose=buy#featured-properties"
                       class="re-featured-category <?= $purpose === 'buy' ? 'active' : '' ?>">
                        Buy Property
                    </a> 
                    <a href="index.php?purpose=rent#featured-properties"
                       class="re-featured-category <?= $purpose === 'rent' ? 'active' : '' ?>">
                        Rent Property
                    </a>
                </div>
            </div>

            <!-- Property Cards -->
           <div class="re-featured-grid">
                <?php if (!empty($latestProperties)): ?>

                    <?php foreach ($latestProperties as $property): ?>
                        <?php
                        $propertyId = (int) $property['id'];
                        $propertyImage = !empty($property['property_image'])
                            ? $property['property_image']
                            : 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1000&q=85';

                        $propertyUrl = 'properties/properties-details.php?id=' . $propertyId;

                        $propertyPrice = !empty($property['price_label'])
                            ? $property['price_label']
                            : '₹' . number_format((float) $property['price'], 0);

                        $propertyLocation = implode(', ', array_filter([
                            $property['location_name'],
                            $property['state']
                        ]));

                        $listingStatus = $property['listing_type'] === 'rent'
                            ? 'For Rent'
                            : 'For Sale';

                        $areaText = !empty($property['area'])
                            ? number_format((float) $property['area']) . ' ' . $property['area_unit']
                            : 'Area N/A';
                        ?>

                        <article class="re-premium-property">
                            <div class="re-premium-property-image">
                                <img
                                    src="<?= $escape($propertyImage) ?>"
                                    alt="<?= $escape($property['title']) ?>"
                                    loading="lazy"
                                >
                                <div class="re-premium-image-overlay"></div>
                                <span class="re-premium-badge">
                                    <i class="ri-verified-badge-fill"></i>
                                    <?= !empty($property['is_featured']) ? 'Featured' : 'Latest' ?>
                                </span>
                                <div class="re-premium-image-bottom">
                                    <span>
                                        <i class="ri-map-pin-line"></i>
                                        <?= $escape($propertyLocation ?: 'Location unavailable') ?>
                                    </span>
                                    <span class="re-premium-status">
                                        <?= $escape($listingStatus) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="re-premium-property-content">

                                <div class="re-premium-price-row">
                                    <div class="re-premium-price">
                                        <?= $escape($propertyPrice) ?>
                                    </div>
                                    <span class="re-premium-property-id">
                                        <?= $escape($property['property_code']) ?>
                                    </span>
                                </div>

                                <h4 class="re-premium-property-title">
                                    <a href="<?= $escape($propertyUrl) ?>">
                                        <?= $escape($property['title']) ?>
                                    </a>
                                </h4>

                                <p class="re-premium-address">
                                    <i class="ri-map-pin-2-line"></i>
                                    <?= $escape($property['address'] ?: $propertyLocation) ?>
                                </p>

                                <div class="re-premium-specs">
                                    <div>
                                        <i class="ri-hotel-bed-line"></i>
                                        <span>
                                            <?= (int) $property['bedrooms'] ?> Beds
                                        </span>
                                    </div>
                                    <div>
                                        <i class="ri-showers-line"></i>
                                        <span>
                                            <?= (int) $property['bathrooms'] ?> Baths
                                        </span>
                                    </div>
                                    <div>
                                        <i class="ri-ruler-line"></i>
                                        <span>
                                            <?= $escape($areaText) ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="re-premium-card-footer">
                                    <span class="re-premium-verified">
                                        <i class="ri-checkbox-circle-fill"></i>
                                        Verified Listing
                                    </span>
                                    <a
                                        href="<?= $escape($propertyUrl) ?>"
                                        aria-label="View <?= $escape($property['title']) ?>"
                                    >
                                        <i class="ri-arrow-right-up-line"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5" style="grid-column: 1 / -1;">
                        <i class="ri-home-4-line fs-1 text-muted"></i>
                        <h4 class="mt-3">No Properties Found</h4>
                        <p class="text-muted">
                            No properties match your selected filters. Please try another search.
                        </p>
                        <a href="index.php#featured-properties" class="btn btn-primary">
                            View Latest Properties
                        </a>
                    </div>

                <?php endif; ?>
            </div>

            <!-- Section Footer CTA -->
            <div class="re-featured-bottom">
                <div class="re-featured-bottom-icon">
                    <i class="ri-customer-service-2-line"></i>
                </div>
                <div class="re-featured-bottom-copy">
                    <h4>Looking for something extraordinary?</h4>
                    <p>Let our property specialists find a home that matches your vision.</p>
                </div>
                <a href="contact.php" class="re-featured-contact">
                    Talk to an Expert <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Explore Locations Section -->
    <section class="re-locations-section section section-light" id="explore-locations">
        <div class="container">
            <div class="section-heading">
                <span class="section-subtitle">EXPLORE TOP DESTINATIONS</span>
                <h2 class="section-title">Find Properties in <span>Popular Cities</span></h2>
                <p class="section-description">Discover homes, apartments and investment opportunities in India's most sought-after locations.</p>
            </div>
            <div class="re-locations-grid">
                <a href="properties.php?location=gurugram" class="re-location-card re-location-large">
                    <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1000&q=85" alt="Residential property in Gurugram" loading="lazy">
                    <span class="re-location-overlay"></span>
                    <span class="re-location-content">
                        <span class="re-location-count">Explore Properties</span>
                        <span class="re-location-name">Gurugram</span>
                        <span class="re-location-link">View Properties <i class="bi bi-arrow-up-right"></i></span>
                    </span>
                </a>
                <a href="properties.php?location=delhi" class="re-location-card">
                    <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=800&q=85" alt="Modern residential property in Delhi" loading="lazy">
                    <span class="re-location-overlay"></span>
                    <span class="re-location-content">
                        <span class="re-location-count">Explore Properties</span>
                        <span class="re-location-name">New Delhi</span>
                        <span class="re-location-link">View Properties <i class="bi bi-arrow-up-right"></i></span>
                    </span>
                </a>
                <a href="properties.php?location=noida" class="re-location-card">
                    <img src="https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=800&q=85" alt="Contemporary home in Noida" loading="lazy">
                    <span class="re-location-overlay"></span>
                    <span class="re-location-content">
                        <span class="re-location-count">Explore Properties</span>
                        <span class="re-location-name">Noida</span>
                        <span class="re-location-link">View Properties <i class="bi bi-arrow-up-right"></i></span>
                    </span>
                </a>
                <a href="properties.php?location=jaipur" class="re-location-card">
                    <img src="https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?auto=format&fit=crop&w=800&q=85" alt="Beautiful residential home in Jaipur" loading="lazy">
                    <span class="re-location-overlay"></span>
                    <span class="re-location-content">
                        <span class="re-location-count">Explore Properties</span>
                        <span class="re-location-name">Jaipur</span>
                        <span class="re-location-link">View Properties <i class="bi bi-arrow-up-right"></i></span>
                    </span>
                </a>
                <a href="properties.php?location=chandigarh" class="re-location-card">
                    <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=800&q=85" alt="Elegant property interior in Chandigarh" loading="lazy">
                    <span class="re-location-overlay"></span>
                    <span class="re-location-content">
                        <span class="re-location-count">Explore Properties</span>
                        <span class="re-location-name">Chandigarh</span>
                        <span class="re-location-link">View Properties <i class="bi bi-arrow-up-right"></i></span>
                    </span>
                </a>
            </div>
            <div class="re-locations-footer">
                <p>Looking for a different city?</p>
                <a href="locations.php" class="btn btn-primary">Explore All Locations <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </section>

      <!-- Property Categories Start -->
    <section class="re-categories-section section section-white" id="property-categories">
        <div class="container">

            <div class="re-categories-heading">
                <div class="section-heading">
                    <span class="section-subtitle">EXPLORE YOUR OPTIONS</span>
                    <h2 class="section-title">Find a Property That <span>Fits Your Lifestyle</span></h2>
                    <p class="section-description">
                        From your first apartment to your next investment, discover properties designed around your needs.
                    </p>
                </div>

                <div class="re-categories-controls">
                    <a href="properties.php" class="re-categories-view-all">
                        View All <i class="ri-arrow-right-up-line"></i>
                    </a>
                    <button type="button" class="re-category-prev" aria-label="Previous property categories">
                        <i class="ri-arrow-left-line"></i>
                    </button>
                    <button type="button" class="re-category-next" aria-label="Next property categories">
                        <i class="ri-arrow-right-line"></i>
                    </button>
                </div>
            </div>

            <div class="re-categories-slider">

                <!-- Apartment -->
                <div class="re-category-slide">
                    <a href="properties.php?property_type=apartment" class="re-category-card">
                        <div class="re-category-image">
                            <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=900&q=85" alt="Modern apartment building" loading="lazy">
                            <span class="re-category-image-overlay"></span>
                            <span class="re-category-image-icon"><i class="ri-building-2-line"></i></span>
                            <span class="re-category-image-label">MODERN LIVING</span>
                        </div>
                        <div class="re-category-content">
                            <div>
                                <h3>Apartments &amp; Flats</h3>
                                <p>Comfortable homes for every lifestyle.</p>
                            </div>
                            <span class="re-category-arrow"><i class="ri-arrow-right-up-line"></i></span>
                        </div>
                    </a>
                </div>

                <!-- Independent House -->
                <div class="re-category-slide">
                    <a href="properties.php?property_type=house" class="re-category-card">
                        <div class="re-category-image">
                            <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=900&q=85" alt="Independent family house with garden" loading="lazy">
                            <span class="re-category-image-overlay"></span>
                            <span class="re-category-image-icon"><i class="ri-home-4-line"></i></span>
                            <span class="re-category-image-label">SPACE TO GROW</span>
                        </div>
                        <div class="re-category-content">
                            <div>
                                <h3>Independent Houses</h3>
                                <p>More privacy and room for your family.</p>
                            </div>
                            <span class="re-category-arrow"><i class="ri-arrow-right-up-line"></i></span>
                        </div>
                    </a>
                </div>

                <!-- Luxury Villas -->
                <div class="re-category-slide">
                    <a href="properties.php?property_type=villa" class="re-category-card">
                        <div class="re-category-image">
                            <img src="https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=900&q=85" alt="Luxury villa with swimming pool" loading="lazy">
                            <span class="re-category-image-overlay"></span>
                            <span class="re-category-image-icon"><i class="ri-hotel-line"></i></span>
                            <span class="re-category-image-label">ELEVATED LIVING</span>
                        </div>
                        <div class="re-category-content">
                            <div>
                                <h3>Luxury Villas</h3>
                                <p>Discover space, comfort and exclusivity.</p>
                            </div>
                            <span class="re-category-arrow"><i class="ri-arrow-right-up-line"></i></span>
                        </div>
                    </a>
                </div>

                <!-- Plots and Land -->
                <div class="re-category-slide">
                    <a href="properties.php?property_type=plot" class="re-category-card">
                        <div class="re-category-image">
                            <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=900&q=85" alt="Open green land for property development" loading="lazy">
                            <span class="re-category-image-overlay"></span>
                            <span class="re-category-image-icon"><i class="ri-landscape-line"></i></span>
                            <span class="re-category-image-label">BUILD YOUR FUTURE</span>
                        </div>
                        <div class="re-category-content">
                            <div>
                                <h3>Plots &amp; Land</h3>
                                <p>Find land for your future plans.</p>
                            </div>
                            <span class="re-category-arrow"><i class="ri-arrow-right-up-line"></i></span>
                        </div>
                    </a>
                </div>

                <!-- Commercial -->
                <div class="re-category-slide">
                    <a href="properties.php?property_type=commercial" class="re-category-card">
                        <div class="re-category-image">
                            <img src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=900&q=85" alt="Contemporary commercial office space" loading="lazy">
                            <span class="re-category-image-overlay"></span>
                            <span class="re-category-image-icon"><i class="ri-community-line"></i></span>
                            <span class="re-category-image-label">BUSINESS SPACES</span>
                        </div>
                        <div class="re-category-content">
                            <div>
                                <h3>Commercial Properties</h3>
                                <p>Explore spaces for business and growth.</p>
                            </div>
                            <span class="re-category-arrow"><i class="ri-arrow-right-up-line"></i></span>
                        </div>
                    </a>
                </div>

            </div>

            <div class="re-categories-footer">
                <span><i class="ri-checkbox-circle-line"></i> Explore properties that match your needs</span>
                <div class="re-category-progress" aria-hidden="true">
                    <span class="re-category-progress-bar"></span>
                </div>
            </div>

        </div>
    </section>
    <!-- Property Categories End -->

    <section class="re-how-section section section-soft" id="how-it-works">
        <div class="container">
            <div class="section-heading">
                <span class="section-subtitle">A SIMPLE, SMARTER WAY</span>
                <h2 class="section-title">Your Journey to <span>Your Dream Property</span></h2>
                <p class="section-description">
                    From your first search to the keys in your hand, we make finding your perfect property simple and stress-free.
                </p>
            </div>

            <div class="re-how-grid">
                <article class="re-how-card">
                    <div class="re-how-card-top">
                        <span class="re-how-step">01</span>
                        <span class="re-how-icon"><i class="ri-search-eye-line"></i></span>
                    </div>
                    <div class="re-how-card-content">
                        <span class="re-how-label">DISCOVER</span>
                        <h3>Find Your Property</h3>
                        <p>Explore homes, apartments and investment opportunities that match your lifestyle, location and budget.</p>
                        <a href="properties.php" class="re-how-link">Explore Properties <i class="ri-arrow-right-line"></i></a>
                    </div>
                    <span class="re-how-watermark"><i class="ri-search-eye-line"></i></span>
                </article>

                <article class="re-how-card">
                    <div class="re-how-card-top">
                        <span class="re-how-step">02</span>
                        <span class="re-how-icon"><i class="ri-calendar-check-line"></i></span>
                    </div>
                    <div class="re-how-card-content">
                        <span class="re-how-label">CONNECT</span>
                        <h3>Schedule a Visit</h3>
                        <p>Shortlist your favourite properties and connect with our team to arrange a convenient property visit.</p>
                        <a href="contact.php" class="re-how-link">Book a Visit <i class="ri-arrow-right-line"></i></a>
                    </div>
                    <span class="re-how-watermark"><i class="ri-calendar-check-line"></i></span>
                </article>

                <article class="re-how-card re-how-card-featured">
                    <div class="re-how-card-top">
                        <span class="re-how-step">03</span>
                        <span class="re-how-icon"><i class="ri-key-2-line"></i></span>
                    </div>
                    <div class="re-how-card-content">
                        <span class="re-how-label">MAKE IT YOURS</span>
                        <h3>Find Your Place</h3>
                        <p>Compare your options, get the guidance you need and move forward confidently with your property decision.</p>
                        <a href="contact.php" class="re-how-link">Talk to an Expert <i class="ri-arrow-right-line"></i></a>
                    </div>
                    <span class="re-how-watermark"><i class="ri-home-heart-line"></i></span>
                </article>
            </div>

            <div class="re-how-bottom">
                <div class="re-how-bottom-icon"><i class="ri-customer-service-2-line"></i></div>
                <div class="re-how-bottom-content">
                    <h3>Need help finding the right property?</h3>
                    <p>Our team can help you explore options that fit your requirements.</p>
                </div>
                <a href="contact.php" class="btn btn-primary re-how-bottom-btn">Get Expert Assistance <i class="ri-arrow-right-up-line"></i></a>
            </div>
        </div>
    </section>
        
    <!-- Our Services Section -->
    <section class="home-services-section section section-white" id="services">
        <div class="container">

            <div class="section-heading home-services-heading">
                <span class="section-subtitle">
                    <i class="ri-building-2-line"></i>
                    WHAT WE OFFER
                </span>

                <h2 class="section-title">
                    Real Estate Services
                    <span>Designed Around You</span>
                </h2>

                <p class="section-description">
                    Whether you are looking for a new home, planning to rent,
                    or exploring an investment, discover services that help
                    you take the next step with confidence.
                </p>
            </div>

            <div class="home-services-grid">
                <!-- Buy Property -->
                <article class="home-service-card">
                    <a href="properties.php?purpose=buy"
                    class="home-service-image"
                    aria-label="Explore properties to buy">
                        <img
                            src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=900&q=85"
                            alt="Contemporary house available for purchase"
                            loading="lazy"
                        >
                        <span class="home-service-image-label">BUY PROPERTY</span>
                    </a>

                    <div class="home-service-content">
                        <div class="home-service-icon">
                            <i class="ri-home-4-line"></i>
                        </div>

                        <h3>Buy a Property</h3>

                        <p>
                            Explore homes, apartments, and properties that
                            match your lifestyle, location, and budget.
                        </p>

                        <a href="properties.php?purpose=buy"
                        class="home-service-link">
                            Explore Properties
                            <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </article>

                <!-- Rent Property -->
                <article class="home-service-card">
                    <a href="properties.php?purpose=rent"
                    class="home-service-image"
                    aria-label="Explore rental properties">
                        <img
                            src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=900&q=85"
                            alt="Bright modern apartment interior for rent"
                            loading="lazy"
                        >
                        <span class="home-service-image-label">RENT PROPERTY</span>
                    </a>

                    <div class="home-service-content">
                        <div class="home-service-icon">
                            <i class="ri-key-2-line"></i>
                        </div>

                        <h3>Rent a Property</h3>

                        <p>
                            Find comfortable rental homes and spaces that
                            suit your needs and preferred location.
                        </p>

                        <a href="properties.php?purpose=rent"
                        class="home-service-link">
                            Find Rentals
                            <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </article>

                <!-- Sell Property -->
                <article class="home-service-card">
                    <a href="contact-us.php?service=sell"
                    class="home-service-image"
                    aria-label="Get assistance selling a property">
                        <img
                            src="https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=900&q=85"
                            alt="Beautiful residential property exterior"
                            loading="lazy"
                        >
                        <span class="home-service-image-label">SELL PROPERTY</span>
                    </a>

                    <div class="home-service-content">
                        <div class="home-service-icon">
                            <i class="ri-price-tag-3-line"></i>
                        </div>

                        <h3>Sell Your Property</h3>

                        <p>
                            Get guidance on presenting your property and
                            connecting with potential buyers.
                        </p>

                        <a href="contact-us.php?service=sell"
                        class="home-service-link">
                            Talk to Our Team
                            <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </article>

                <!-- Investment -->
                <article class="home-service-card">
                    <a href="projects.php"
                    class="home-service-image"
                    aria-label="Explore property investment opportunities">
                        <img
                            src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=900&q=85"
                            alt="Modern commercial building representing real estate investment"
                            loading="lazy"
                        >
                        <span class="home-service-image-label">INVESTMENT</span>
                    </a>

                    <div class="home-service-content">
                        <div class="home-service-icon">
                            <i class="ri-line-chart-line"></i>
                        </div>

                        <h3>Property Investment</h3>

                        <p>
                            Discover residential and commercial opportunities
                            to evaluate against your investment goals.
                        </p>

                        <a href="projects.php"
                        class="home-service-link">
                            Explore Opportunities
                            <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </article>

            </div>

            <div class="home-services-bottom">
                <p>Looking for something specific?</p>
                <a href="contact-us.php" class="btn btn-outline-primary">
                    Discuss Your Requirements
                    <i class="ri-arrow-right-line"></i>
                </a>
            </div>

        </div>
    </section>
    
    <!-- Final Property CTA -->
    <section class="property-cta-section section" id="property-cta">
        <div class="container">
            <div class="property-cta-wrapper">

                <div class="property-cta-content">
                    <span class="property-cta-tag">
                        <i class="ri-home-heart-line"></i>
                        YOUR NEXT CHAPTER STARTS HERE
                    </span>

                    <h2>
                        Ready to Find Your
                        <span>Dream Property?</span>
                    </h2>

                    <p>
                        Whether you're buying your first home, finding a rental
                        or exploring an investment, let's help you take the next step.
                    </p>

                    <div class="property-cta-actions">
                        <a href="properties.php" class="btn btn-primary">
                            Explore Properties
                            <i class="ri-arrow-right-line"></i>
                        </a>

                        <a href="contact.php" class="property-cta-contact">
                            <i class="ri-phone-line"></i>
                            Talk to Our Team
                        </a>
                    </div>

                    <div class="property-cta-trust">
                        <span><i class="ri-check-line"></i> Explore your options</span>
                        <span><i class="ri-check-line"></i> Get expert guidance</span>
                    </div>
                </div>

                <div class="property-cta-visual">
                    <div class="property-cta-image">
                        <img
                            src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1000&q=85"
                            alt="Modern luxury home with a welcoming living space"
                            loading="lazy"
                        >
                    </div>

                    <div class="property-cta-floating">
                        <span class="property-cta-floating-icon">
                            <i class="ri-key-2-line"></i>
                        </span>
                        <div>
                            <strong>Your Dream Home</strong>
                            <span>Start exploring today</span>
                        </div>
                    </div>

                    <div class="property-cta-decoration"></div>
                </div>

            </div>
        </div>
    </section>

        
    <!-- Blog Section -->
    <section class="blog-section section section-white" id="blogs">
        <div class="container">

            <div class="section-heading">
                <span class="section-subtitle">OUR LATEST INSIGHTS</span>
                <h2 class="section-title">Real Estate Tips & Insights</h2>
                <p class="section-description">
                    Discover expert advice, property market updates and practical
                    guides to help you make smarter real estate decisions.
                </p>
            </div>

            <div class="blog-section-header">
                <span class="blog-results-label">
                    <i class="ri-article-line"></i>
                    Latest Articles
                </span>

                <a href="blog.php" class="btn btn-outline-primary">
                    View All Articles <i class="ri-arrow-right-line"></i>
                </a>
            </div>

            <div class="blog-grid">

                <?php if (!empty($latestBlogs)): ?>
                    <?php foreach ($latestBlogs as $blog): ?>
                        <?php
                            $blogUrl = APP_URL . '/blog/blog-details.php?slug=' .
                                rawurlencode($blog['slug']);
                            $blogImage = !empty($blog['featured_image'])
                                ? (
                                    preg_match('/^https?:\/\//i', $blog['featured_image'])
                                        ? $blog['featured_image']
                                        : APP_URL . '/' . ltrim($blog['featured_image'], '/')
                                )
                                : 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=900&q=85';

                            $blogDate = !empty($blog['published_at'])
                                ? date('M d, Y', strtotime($blog['published_at']))
                                : '';

                            $blogReadingTime = max(1, (int) ($blog['reading_time'] ?? 5));
                        ?>

                        <article class="blog-card">
                            <a href="<?= htmlspecialchars($blogUrl, ENT_QUOTES, 'UTF-8') ?>"
                            class="blog-image">
                                <img
                                    src="<?= htmlspecialchars($blogImage, ENT_QUOTES, 'UTF-8') ?>"
                                    alt="<?= htmlspecialchars($blog['image_alt'] ?: $blog['title'], ENT_QUOTES, 'UTF-8') ?>"
                                    loading="lazy">

                                <span class="blog-category">
                                    <?= htmlspecialchars($blog['category'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </a>

                            <div class="blog-content">

                                <div class="blog-meta">
                                    <span>
                                        <i class="ri-calendar-line"></i>
                                        <?= htmlspecialchars($blogDate, ENT_QUOTES, 'UTF-8') ?>
                                    </span> 
                                    <span>
                                        <i class="ri-time-line"></i>
                                        <?= $blogReadingTime ?> min read
                                    </span> 
                                </div>

                                <h3>
                                    <a href="<?= htmlspecialchars($blogUrl, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($blog['title'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </h3>

                                <p>
                                    <?= htmlspecialchars($blog['excerpt'], ENT_QUOTES, 'UTF-8') ?>
                                </p>

                                <a href="<?= htmlspecialchars($blogUrl, ENT_QUOTES, 'UTF-8') ?>"
                                class="blog-read-more">

                                    Read Article
                                    <i class="ri-arrow-right-line"></i>

                                </a>

                            </div>

                        </article>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="col-12 text-center py-4">
                        <p class="mb-0 text-muted">
                            No articles available at the moment.
                        </p>
                    </div>

                <?php endif; ?>

            </div>

            <div class="blog-mobile-action">
                <a href="blog.php" class="btn btn-primary">
                    Explore All Articles <i class="ri-arrow-right-line"></i>
                </a>
            </div>

        </div>
    </section> 

    <section class="faq-section section section-light" id="faq">
        <div class="container">
            <div class="row align-items-center g-5">

                <div class="col-lg-5">
                    <div class="faq-content">
                        <span class="section-subtitle">GOT QUESTIONS?</span>
                        <h2 class="section-title">Frequently Asked Questions</h2>
                        <p class="section-description">
                            Find answers to common questions about buying, selling,
                            renting and investing in properties. Our team is always
                            here to help you.
                        </p>

                        <div class="faq-help-card mt-4">
                            <div class="faq-help-icon">
                                <i class="ri-customer-service-2-line"></i>
                            </div>
                            <div class="faq-help-content">
                                <h5>Still have questions?</h5>
                                <p>Talk to our property experts for personalized assistance.</p>
                                <a href="/realstate/contact.php">
                                    Contact Our Team <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="accordion faq-accordion" id="faqAccordion">

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeadingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne" aria-expanded="true" aria-controls="faqOne">
                                    <span class="faq-number">01</span>
                                    <span>How can I find the right property?</span>
                                </button>
                            </h2>
                            <div id="faqOne" class="accordion-collapse collapse show" aria-labelledby="faqHeadingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    You can explore properties by location, property type,
                                    budget and other preferences on our website. Our team
                                    can also help you find a property that matches your requirements.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeadingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo" aria-expanded="false" aria-controls="faqTwo">
                                    <span class="faq-number">02</span>
                                    <span>Can I schedule a property visit?</span>
                                </button>
                            </h2>
                            <div id="faqTwo" class="accordion-collapse collapse" aria-labelledby="faqHeadingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes. You can contact our team through the enquiry form
                                    or contact page to request a property visit. Our team
                                    will coordinate a suitable time with you.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeadingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqThree" aria-expanded="false" aria-controls="faqThree">
                                    <span class="faq-number">03</span>
                                    <span>What documents are required to buy a property?</span>
                                </button>
                            </h2>
                            <div id="faqThree" class="accordion-collapse collapse" aria-labelledby="faqHeadingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Common documents include identity proof, address proof
                                    and financial documents. Property-specific legal documents
                                    should also be verified before completing a purchase.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeadingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqFour" aria-expanded="false" aria-controls="faqFour">
                                    <span class="faq-number">04</span>
                                    <span>Do you help with selling properties?</span>
                                </button>
                            </h2>
                            <div id="faqFour" class="accordion-collapse collapse" aria-labelledby="faqHeadingFour" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes. We help property owners connect with potential
                                    buyers and guide them through the property-selling process.
                                </div>
                            </div>
                        </div>

                    

                    </div>
                </div>

            </div>
        </div>
    </section>
    
    <section class="real-estate-content section section-white">
        <div class="container">

            <div class="row align-items-center g-5">

                <!-- Left Content -->
                <div class="col-lg-7">
                    <div class="real-content-main">

                        <span class="section-subtitle">
                            <i class="ri-building-line me-2"></i>
                            YOUR TRUSTED PROPERTY PARTNER
                        </span>

                        <h2 class="section-title">
                            Discover Better Properties.
                            <span>Make Smarter Real Estate Decisions.</span>
                        </h2>

                        <p class="real-content-intro">
                            Finding the perfect property is an important decision,
                            whether you are planning to buy your dream home, invest
                            in real estate, or sell your existing property.
                        </p>

                        <p>
                            Our real estate services are designed to make your
                            property journey simple, convenient, and transparent.
                            We help you explore residential and commercial
                            properties across different locations, budgets,
                            and requirements.
                        </p>

                        <p>
                            With a focus on property knowledge, customer
                            satisfaction, and professional support, we guide you
                            through every stage of the process. From discovering
                            suitable properties and arranging site visits to
                            understanding important property details, our team
                            is committed to helping you make informed decisions.
                        </p>

                        <p>
                            Whether you are a first-time homebuyer, an experienced
                            investor, or a property owner looking for the right
                            opportunities, we are here to assist you. Explore
                            available properties and take the next step towards
                            your real estate goals with confidence.
                        </p>

                    </div>
                </div>

                <!-- Right Highlight Card -->
                <div class="col-lg-5">
                    <div class="real-content-card">

                        <div class="real-content-card-icon">
                            <i class="ri-home-heart-line"></i>
                        </div>

                        <span class="real-content-card-label">
                            PROPERTY MADE SIMPLE
                        </span>

                        <h3>
                            Your Property Journey,
                            Our Priority.
                        </h3>

                        <p>
                            Get the right guidance, explore valuable
                            opportunities, and move forward with confidence.
                        </p>

                        <div class="real-content-features">

                            <div class="real-content-feature">
                                <div class="real-feature-icon">
                                    <i class="ri-search-eye-line"></i>
                                </div>
                                <div>
                                    <h5>Explore Properties</h5>
                                    <p>Discover properties that match your needs.</p>
                                </div>
                            </div>

                            <div class="real-content-feature">
                                <div class="real-feature-icon">
                                    <i class="ri-user-heart-line"></i>
                                </div>
                                <div>
                                    <h5>Expert Assistance</h5>
                                    <p>Get professional support at every step.</p>
                                </div>
                            </div>

                            <div class="real-content-feature">
                                <div class="real-feature-icon">
                                    <i class="ri-shield-check-line"></i>
                                </div>
                                <div>
                                    <h5>Confident Decisions</h5>
                                    <p>Make informed property decisions.</p>
                                </div>
                            </div>

                        </div>

                        <a href="/realstate/properties.php" class="real-content-btn">
                            Explore Properties
                            <i class="ri-arrow-right-line"></i>
                        </a>

                    </div>
                </div>

            </div>

        </div>
    </section> 

             <!-- Customer Testimonials -->
    <section class="testimonials-section section section-soft" id="testimonials">
        <div class="container">

            <!-- Existing root heading classes -->
            <div class="section-heading testimonials-heading">
                <span class="section-subtitle">
                    <i class="ri-double-quotes-l"></i> CLIENT EXPERIENCES
                </span>

                <h2 class="section-title">
                    Stories That Make Us <span>Smile</span>
                </h2> 

                <p class="section-description">
                    Discover how we help people explore property options
                    and make more confident real estate decisions.
                </p>
            </div>

            <!-- Independent Testimonials Slider -->
             
            <div class="testimonials-slider">

                <?php if (!empty($testimonials)): ?>

                    <?php foreach ($testimonials as $testimonial): ?>

                        <div class="testimonial-slide">

                            <article class="testimonial-card">

                                <div class="testimonial-top">

                                    <span class="testimonial-quote">
                                        <i class="ri-double-quotes-l"></i>
                                    </span>

                                    <div class="testimonial-stars"
                                        aria-label="<?= (int) $testimonial['rating'] ?> star rating">

                                        <?php for ($star = 1; $star <= 5; $star++): ?>

                                            <i class="<?= $star <= (int) $testimonial['rating']
                                                ? 'ri-star-fill'
                                                : 'ri-star-line' ?>"></i>

                                        <?php endfor; ?>

                                    </div>

                                </div>

                                <p class="testimonial-text">
                                    <?= $escape($testimonial['review']) ?>
                                </p>

                                <div class="testimonial-divider"></div>

                                <div class="testimonial-user">

                                    <div class="testimonial-avatar avatar-one">
                                        <?= $escape($testimonialInitials($testimonial['customer_name'])) ?>
                                    </div>

                                    <div class="testimonial-user-info">

                                        <h3>
                                            <?= $escape($testimonial['customer_name']) ?>
                                        </h3>

                                        <span>
                                            <i class="ri-map-pin-line"></i>
                                            <?= $escape($testimonial['location']) ?>
                                        </span>

                                    </div>

                                </div>

                            </article>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="testimonial-slide">

                        <article class="testimonial-card">

                            <div class="testimonial-top">
                                <span class="testimonial-quote">
                                    <i class="ri-double-quotes-l"></i>
                                </span>
                            </div>

                            <p class="testimonial-text">
                                Customer experiences will appear here soon.
                            </p>

                        </article>

                    </div>

                <?php endif; ?>

            </div>


            <!-- Independent slider controls -->
            <div class="testimonials-bottom">
                <div class="testimonials-note">
                    <i class="ri-heart-3-line"></i>
                    <span>Finding the right property starts with the right guidance.</span>
                </div>

                <div class="testimonials-controls">
                    <button type="button" class="testimonial-prev"
                        aria-label="Previous testimonial">
                        <i class="ri-arrow-left-line"></i>
                    </button>

                    <div class="testimonial-progress">
                        <span class="testimonial-progress-bar"></span>
                    </div>

                    <button type="button" class="testimonial-next"
                        aria-label="Next testimonial">
                        <i class="ri-arrow-right-line"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

      <section class="team-section section section-white" id="team">
        <div class="container">

            <div class="team-section-heading">

                <div class="section-heading">
                    <span class="section-subtitle">MEET OUR EXPERTS</span>
                    <h2 class="section-title">
                        Meet the People Behind <span>Your Property Journey</span>
                    </h2>
                    <p class="section-description">
                        Our experienced real estate professionals are dedicated to helping
                        you find the right property with confidence and ease.
                    </p>
                </div>

                <div class="team-slider-controls">

                    <a href="about.php#team" class="team-view-all">
                        View All <i class="ri-arrow-right-up-line"></i>
                    </a>

                    <button type="button" class="team-prev" aria-label="Previous team members">
                        <i class="ri-arrow-left-line"></i>
                    </button>

                    <button type="button" class="team-next" aria-label="Next team members">
                        <i class="ri-arrow-right-line"></i>
                    </button>

                </div>

            </div>

             <div class="team-slider">
                <?php if (!empty($homeTeamMembers)): ?>
                    <?php foreach ($homeTeamMembers as $member): ?>
                        <div class="team-slide">
                            <div class="team-card">

                                <div class="team-image">
                                    <a href="<?= $escape(APP_URL) ?>/team/profile-details.php?member=<?= urlencode($member['slug']) ?>">
                                        <?php if (!empty($member['image'])): ?>
                                            <img
                                                src="<?= $escape($teamImage($member['image'])) ?>"
                                                alt="<?= $escape($member['name']) ?>"
                                                loading="lazy"
                                            >
                                        <?php endif; ?>
                                    </a>

                                    <div class="team-social">

                                        <?php if (!empty($member['linkedin'])): ?>
                                            <a href="<?= $escape($member['linkedin']) ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                                                <i class="ri-linkedin-fill"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!empty($member['instagram'])): ?>
                                            <a href="<?= $escape($member['instagram']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                                <i class="ri-instagram-line"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!empty($member['facebook'])): ?>
                                            <a href="<?= $escape($member['facebook']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                                                <i class="ri-facebook-fill"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="team-content">
                                    <span class="team-designation">
                                        <?= $escape($member['designation']) ?>
                                    </span>
                                    <h4><?= $escape($member['name']) ?></h4>
                                    <p><?= $escape($member['short_description']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section> 


    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="js/header.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const tabs = document.querySelectorAll(".re-search-tab");
            const purposeInput = document.getElementById("reSearchPurpose");
            const propertyType = document.getElementById("reSearchType");
            tabs.forEach((tab) => {
                tab.addEventListener("click", () => {
                    const purpose = tab.dataset.purpose;
                    tabs.forEach((item) => {
                        const active = item === tab;
                        item.classList.toggle("active", active);
                        item.setAttribute("aria-pressed", String(active));
                    });
                    if (purposeInput) purposeInput.value = purpose;
                    if (propertyType && purpose === "commercial") {
                        propertyType.value = "commercial";
                    } else if (propertyType && propertyType.value === "commercial") {
                        propertyType.value = "";
                    }
                });
            });
        });
    </script>
    <script>
       document.addEventListener('DOMContentLoaded', function () {

        const tabs = document.querySelectorAll('.re-search-tab');
        const purposeInput = document.getElementById('reSearchPurpose');
        const propertyType = document.getElementById('reSearchType');
        tabs.forEach(function (tab) {

            tab.addEventListener('click', function () {
                const purpose = this.dataset.purpose;
                tabs.forEach(function (item) {
                    item.classList.remove('active');
                    item.setAttribute('aria-pressed', 'false');
                });
                this.classList.add('active');
                this.setAttribute('aria-pressed', 'true');
                purposeInput.value = purpose;
                if (purpose === 'commercial') {
                    propertyType.value = 'commercial';
                } else if (propertyType.value === 'commercial') {
                    propertyType.value = '';
                }
            });
        });
    });

    
</script>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Slick Carousel JS -->
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<!-- Team Slider Initialization -->
<script>
$(document).ready(function () {

    $('.team-slider').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        infinite: false,
        autoplay: false,
        speed: 500,
        arrows: true,
        dots: false,
        draggable: true,
        swipe: true,
        adaptiveHeight: false,

        prevArrow: $('.team-prev'),
        nextArrow: $('.team-next'),

        responsive: [
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 481,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

});
</script>

</body>

</html>