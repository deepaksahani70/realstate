<?php 
$property = [
    'title' => 'Luxury Family Home',
    'location' => 'Sector 15, Gurugram, Haryana',
    'price' => '₹1.85 Crore',
    'type' => 'Independent House',
    'status' => 'For Sale',
    'bedrooms' => 4,
    'bathrooms' => 3,
    'area' => '2,400 sq. ft.',
    'parking' => '2 Cars',
    'id' => 'PROP-1024',
    'description' => 'Discover a thoughtfully designed family home featuring spacious living areas, contemporary interiors, and a welcoming outdoor space. The property offers a practical layout for everyday living, with convenient access to local shops, schools, and essential services.',
    'address' => 'Sector 15, Gurugram, Haryana, India'
];

$propertyImages = [
    [
        'src' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1400&q=85',
        'alt' => 'Front exterior of a contemporary family home'
    ],
    [
        'src' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=800&q=85',
        'alt' => 'Bright modern living room'
    ],
    [
        'src' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=800&q=85',
        'alt' => 'Modern residential interior'
    ],
    [
        'src' => 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=800&q=85',
        'alt' => 'Elegant home design'
    ],
    [
        'src' => 'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=800&q=85',
        'alt' => 'Contemporary home interior'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>locations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font: Inter -->
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet"/>
    <!-- Global Website CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <!-- Slick Carousel CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css">

</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Property Details Page -->
<main class="property-details-page">
    <?php
        $pageTitle = 'Explore Locations';
        $breadcrumbItems = [
              ['label' => 'Properties', 'url' => '/properties.php'],
              ['label' => 'Luxury Family Home', 'url' => null]
        ];
        include __DIR__ . '/../includes/breadcrumb.php';

        $locationSearch = trim($_GET['search'] ?? '');
       ?>
    <!-- Property Gallery -->
    <section class="property-detail-gallery-section">
        <div class="container">

            <div class="property-gallery">
                <a
                    href="<?= htmlspecialchars($propertyImages[0]['src'], ENT_QUOTES, 'UTF-8') ?>"
                    class="property-gallery-main"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Open main property image"
                >
                    <img
                        src="<?= htmlspecialchars($propertyImages[0]['src'], ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($propertyImages[0]['alt'], ENT_QUOTES, 'UTF-8') ?>"
                    >
                </a>

                <div class="property-gallery-thumbnails">
                    <?php foreach (array_slice($propertyImages, 1, 4) as $index => $image): ?>
                        <a
                            href="<?= htmlspecialchars($image['src'], ENT_QUOTES, 'UTF-8') ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Open property photo <?= $index + 2 ?>"
                        >
                            <img
                                src="<?= htmlspecialchars($image['src'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($image['alt'], ENT_QUOTES, 'UTF-8') ?>"
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
                <div class="property-detail-main">
                    <div class="property-overview-top">
                        <div class="property-overview-info">
                            <div class="property-status-row">
                                <span class="property-status">
                                    <?= htmlspecialchars($property['status'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <span class="property-reference">
                                    ID: <?= htmlspecialchars($property['id'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>
                            <h1>
                                <?= htmlspecialchars($property['title'], ENT_QUOTES, 'UTF-8') ?>
                            </h1>
                            <p class="property-overview-location">
                                <i class="ri-map-pin-line"></i>
                                <?= htmlspecialchars($property['location'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>

                        <div class="property-overview-price">
                            <span>Asking Price</span>
                            <strong><?= htmlspecialchars($property['price'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </div>
                    </div>

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
                                <strong><?= htmlspecialchars($property['area'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <span>Built-up Area</span>
                            </div>
                        </div>

                        <div class="property-quick-spec">
                            <i class="ri-car-line"></i>
                            <div>
                                <strong><?= htmlspecialchars($property['parking'], ENT_QUOTES, 'UTF-8') ?></strong>
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
                            <?= htmlspecialchars($property['description'], ENT_QUOTES, 'UTF-8') ?>
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
                                <strong><?= htmlspecialchars($property['type'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                            <div>
                                <span>Listing Type</span>
                                <strong><?= htmlspecialchars($property['status'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                            <div>
                                <span>Property ID</span>
                                <strong><?= htmlspecialchars($property['id'], ENT_QUOTES, 'UTF-8') ?></strong>
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
                                <strong><?= htmlspecialchars($property['area'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                            <div>
                                <span>Parking</span>
                                <strong><?= htmlspecialchars($property['parking'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                            <div>
                                <span>Location</span>
                                <strong><?= htmlspecialchars($property['location'], ENT_QUOTES, 'UTF-8') ?></strong>
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
                            <div><i class="ri-check-line"></i> Modular Kitchen</div>
                            <div><i class="ri-check-line"></i> Balcony</div>
                            <div><i class="ri-check-line"></i> Reserved Parking</div>
                            <div><i class="ri-check-line"></i> 24/7 Water Supply</div>
                            <div><i class="ri-check-line"></i> Power Backup</div>
                            <div><i class="ri-check-line"></i> Security</div>
                            <div><i class="ri-check-line"></i> Nearby Markets</div>
                            <div><i class="ri-check-line"></i> Family-Friendly Area</div>
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
                                <p><?= htmlspecialchars($property['address'], ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </div>

                        <a
                            class="property-map-link"
                            href="https://www.google.com/maps/search/?api=1&amp;query=<?= rawurlencode($property['address']) ?>"
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
                        <p>Send your details and our team can contact you about this listing.</p>

                        <form action="/property-enquiry.php" method="POST" class="property-enquiry-form">
                            <input
                                type="hidden"
                                name="property_id"
                                value="<?= htmlspecialchars($property['id'], ENT_QUOTES, 'UTF-8') ?>"
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
                                >I'm interested in <?= htmlspecialchars($property['title'], ENT_QUOTES, 'UTF-8') ?>. Please contact me with more details.</textarea>
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
                            <a href="/contact-us.php">Contact our team <i class="ri-arrow-right-line"></i></a>
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

            <div class="similar-properties-placeholder">
                <i class="ri-building-4-line"></i>
                <h3>Discover More Properties</h3>
                <p>Browse our listings to find more homes and investment opportunities.</p>
                <a href="/properties.php" class="btn btn-primary">
                    Browse All Properties
                    <i class="ri-arrow-right-line"></i>
                </a>
            </div>

        </div>
    </section>

</main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="../js/header.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>
