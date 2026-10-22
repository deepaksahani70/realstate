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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <!-- Slick Carousel CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css">

</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
  
    <main>
    <?php
        $pageTitle = 'Explore Properties';
        $breadcrumbItems = [
            ['label' => 'Properties', 'url' => null]
        ];
        include __DIR__ . '/includes/breadcrumb.php';
        $locationSearch = trim($_GET['search'] ?? '');
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
                <form action="/locations.php" method="GET" class="location-search-form">
                    <div class="location-search-input-wrap">
                        <i class="ri-map-pin-2-line"></i>
                        <input
                            type="search"
                            name="search"
                            value="<?= htmlspecialchars($locationSearch, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Search city, area or neighbourhood..."
                            aria-label="Search properties by location"
                            autocomplete="off"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary location-search-button">
                        <i class="ri-search-line"></i>
                        Search Locations
                    </button>
                </form>

                <div class="location-popular-searches">
                    <span>Popular searches:</span>
                    <a href="/locations.php?search=Delhi">Delhi</a>
                    <a href="/locations.php?search=Gurugram">Gurugram</a>
                    <a href="/locations.php?search=Noida">Noida</a>
                    <a href="/locations.php?search=Panipat">Panipat</a>
                </div>
            </div>

        </div>
    </section>
    
        <!-- Featured Properties Section -->
    <section class="re-featured-section" id="featured-properties">
        <div class="container"> 

            <!-- Property Cards -->
            <div class="re-featured-grid">
                <!-- Property 1 -->
                <article class="re-premium-property">
                    <div class="re-premium-property-image">
                        <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1000&q=85"
                            alt="Luxury contemporary villa with landscaped garden" loading="lazy">
                        <div class="re-premium-image-overlay"></div>
                        <span class="re-premium-badge">
                            <i class="ri-verified-badge-fill"></i> Featured
                        </span>
                        <button class="re-premium-favorite" type="button"
                                aria-label="Add Grand Contemporary Villa to favorites" aria-pressed="false">
                            <i class="ri-heart-line"></i>
                        </button>
                        <div class="re-premium-image-bottom">
                            <span><i class="ri-map-pin-line"></i> Gurugram, Haryana</span>
                            <span class="re-premium-status">For Sale</span>
                        </div>
                    </div>
                    <div class="re-premium-property-content">
                        <div class="re-premium-price-row">
                            <div class="re-premium-price">₹1.85 Cr <span>onwards</span></div>
                            <span class="re-premium-property-id">ECI-001</span>
                        </div>
                        <h4 class="re-premium-property-title">
                            <a href="/property-details.php?id=1">The Grand Contemporary Villa</a>
                        </h4>
                        <p class="re-premium-address">
                            <i class="ri-map-pin-2-line"></i> Golf Course Road, Gurugram
                        </p>
                        <div class="re-premium-specs">
                            <div><i class="ri-hotel-bed-line"></i><span>4 Beds</span></div>
                            <div><i class="ri-showers-line"></i><span>3 Baths</span></div>
                            <div><i class="ri-ruler-line"></i><span>2,400 sq.ft.</span></div>
                        </div>
                        <div class="re-premium-card-footer">
                            <span class="re-premium-verified">
                                <i class="ri-checkbox-circle-fill"></i> Verified Listing
                            </span>
                            <a href="/property-details.php?id=1" aria-label="View Grand Contemporary Villa">
                                <i class="ri-arrow-right-up-line"></i>
                            </a>
                        </div>
                    </div>
                </article>

                <!-- Property 2 -->
                <article class="re-premium-property">
                    <div class="re-premium-property-image">
                        <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1000&q=85"
                            alt="Modern family residence with contemporary architecture" loading="lazy">
                        <div class="re-premium-image-overlay"></div>
                        <span class="re-premium-badge re-badge-popular">
                            <i class="ri-fire-line"></i> Popular
                        </span>
                        <button class="re-premium-favorite" type="button"
                                aria-label="Add Signature Family Residence to favorites" aria-pressed="false">
                            <i class="ri-heart-line"></i>
                        </button>
                        <div class="re-premium-image-bottom">
                            <span><i class="ri-map-pin-line"></i> Sector 57, Gurugram</span>
                            <span class="re-premium-status">For Sale</span>
                        </div>
                    </div>
                    <div class="re-premium-property-content">
                        <div class="re-premium-price-row">
                            <div class="re-premium-price">₹95 Lac <span>negotiable</span></div>
                            <span class="re-premium-property-id">ECI-002</span>
                        </div>
                        <h4 class="re-premium-property-title">
                            <a href="/property-details.php?id=2">The Signature Family Residence</a>
                        </h4>
                        <p class="re-premium-address">
                            <i class="ri-map-pin-2-line"></i> Sector 57, Gurugram
                        </p>
                        <div class="re-premium-specs">
                            <div><i class="ri-hotel-bed-line"></i><span>3 Beds</span></div>
                            <div><i class="ri-showers-line"></i><span>3 Baths</span></div>
                            <div><i class="ri-ruler-line"></i><span>1,850 sq.ft.</span></div>
                        </div>
                        <div class="re-premium-card-footer">
                            <span class="re-premium-verified">
                                <i class="ri-checkbox-circle-fill"></i> Verified Listing
                            </span>
                            <a href="/property-details.php?id=2" aria-label="View Signature Family Residence">
                                <i class="ri-arrow-right-up-line"></i>
                            </a>
                        </div>
                    </div>
                </article>

                <!-- Property 3 -->
                <article class="re-premium-property">
                    <div class="re-premium-property-image">
                        <img src="https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1000&q=85"
                            alt="Elegant modern urban residence" loading="lazy">
                        <div class="re-premium-image-overlay"></div>
                        <span class="re-premium-badge re-badge-new">
                            <i class="ri-sparkling-line"></i> New Listing
                        </span>
                        <button class="re-premium-favorite" type="button"
                                aria-label="Add Elegant Urban Residence to favorites" aria-pressed="false">
                            <i class="ri-heart-line"></i>
                        </button>
                        <div class="re-premium-image-bottom">
                            <span><i class="ri-map-pin-line"></i> DLF Phase 3</span>
                            <span class="re-premium-status re-status-rent">For Rent</span>
                        </div>
                    </div>
                    <div class="re-premium-property-content">
                        <div class="re-premium-price-row">
                            <div class="re-premium-price">₹45,000 <span>/ month</span></div>
                            <span class="re-premium-property-id">ECI-003</span>
                        </div>
                        <h4 class="re-premium-property-title">
                            <a href="/property-details.php?id=3">The Elegant Urban Residence</a>
                        </h4>
                        <p class="re-premium-address">
                            <i class="ri-map-pin-2-line"></i> DLF Phase 3, Gurugram
                        </p>
                        <div class="re-premium-specs">
                            <div><i class="ri-hotel-bed-line"></i><span>3 Beds</span></div>
                            <div><i class="ri-showers-line"></i><span>2 Baths</span></div>
                            <div><i class="ri-ruler-line"></i><span>1,600 sq.ft.</span></div>
                        </div>
                        <div class="re-premium-card-footer">
                            <span class="re-premium-verified">
                                <i class="ri-checkbox-circle-fill"></i> Verified Listing
                            </span>
                            <a href="/property-details.php?id=3" aria-label="View Elegant Urban Residence">
                                <i class="ri-arrow-right-up-line"></i>
                            </a>
                        </div>
                    </div>
                </article>
            </div>

          
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