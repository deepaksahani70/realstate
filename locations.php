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
        $pageTitle = 'Explore Locations';
        $breadcrumbItems = [
            ['label' => 'Locations', 'url' => null]
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

    
    <!-- Properties by Location -->
    <section class="property-locations-section section section-soft" id="property-locations">
        <div class="container"> 

            <div class="property-locations-grid">

                <!-- Delhi -->
                <article class="property-location-card">
                    <a href="/properties.php?location=Delhi"
                    class="property-location-image"
                    aria-label="Explore properties in Delhi">
                        <img
                            src="https://images.unsplash.com/photo-1587474260584-136574528ed5?auto=format&fit=crop&w=900&q=85"
                            alt="Delhi city architecture"
                            loading="lazy"
                        >
                        <span class="property-location-count">Explore Properties</span>
                    </a>
                    <div class="property-location-content">
                        <div>
                            <h3>Delhi</h3>
                            <p><i class="ri-map-pin-line"></i> Delhi NCR</p>
                        </div>
                        <a href="/properties.php?location=Delhi"
                        class="property-location-arrow"
                        aria-label="View properties in Delhi">
                            <i class="ri-arrow-right-up-line"></i>
                        </a>
                    </div>
                </article>

                <!-- Gurugram -->
                <article class="property-location-card">
                    <a href="/properties.php?location=Gurugram"
                    class="property-location-image"
                    aria-label="Explore properties in Gurugram">
                        <img
                            src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=900&q=85"
                            alt="Modern commercial buildings in an urban district"
                            loading="lazy"
                        >
                        <span class="property-location-count">Explore Properties</span>
                    </a>
                    <div class="property-location-content">
                        <div>
                            <h3>Gurugram</h3>
                            <p><i class="ri-map-pin-line"></i> Haryana</p>
                        </div>
                        <a href="/properties.php?location=Gurugram"
                        class="property-location-arrow"
                        aria-label="View properties in Gurugram">
                            <i class="ri-arrow-right-up-line"></i>
                        </a>
                    </div>
                </article>

                <!-- Noida -->
                <article class="property-location-card">
                    <a href="/properties.php?location=Noida"
                    class="property-location-image"
                    aria-label="Explore properties in Noida">
                        <img
                            src="https://images.unsplash.com/photo-1511818966892-d7d671e672a2?auto=format&fit=crop&w=900&q=85"
                            alt="Contemporary urban architecture"
                            loading="lazy"
                        >
                        <span class="property-location-count">Explore Properties</span>
                    </a>
                    <div class="property-location-content">
                        <div>
                            <h3>Noida</h3>
                            <p><i class="ri-map-pin-line"></i> Uttar Pradesh</p>
                        </div>
                        <a href="/properties.php?location=Noida"
                        class="property-location-arrow"
                        aria-label="View properties in Noida">
                            <i class="ri-arrow-right-up-line"></i>
                        </a>
                    </div>
                </article>

                <!-- Panipat -->
                <article class="property-location-card">
                    <a href="/properties.php?location=Panipat"
                    class="property-location-image"
                    aria-label="Explore properties in Panipat">
                        <img
                            src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=900&q=85"
                            alt="Modern residential home"
                            loading="lazy"
                        >
                        <span class="property-location-count">Explore Properties</span>
                    </a>
                    <div class="property-location-content">
                        <div>
                            <h3>Panipat</h3>
                            <p><i class="ri-map-pin-line"></i> Haryana</p>
                        </div>
                        <a href="/properties.php?location=Panipat"
                        class="property-location-arrow"
                        aria-label="View properties in Panipat">
                            <i class="ri-arrow-right-up-line"></i>
                        </a>
                    </div>
                </article>

                <!-- Jaipur -->
                <article class="property-location-card">
                    <a href="/properties.php?location=Jaipur"
                    class="property-location-image"
                    aria-label="Explore properties in Jaipur">
                        <img
                            src="https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=900&q=85"
                            alt="Elegant residential property"
                            loading="lazy"
                        >
                        <span class="property-location-count">Explore Properties</span>
                    </a>
                    <div class="property-location-content">
                        <div>
                            <h3>Jaipur</h3>
                            <p><i class="ri-map-pin-line"></i> Rajasthan</p>
                        </div>
                        <a href="/properties.php?location=Jaipur"
                        class="property-location-arrow"
                        aria-label="View properties in Jaipur">
                            <i class="ri-arrow-right-up-line"></i>
                        </a>
                    </div>
                </article>

                <!-- Chandigarh -->
                <article class="property-location-card">
                    <a href="/properties.php?location=Chandigarh"
                    class="property-location-image"
                    aria-label="Explore properties in Chandigarh">
                        <img
                            src="https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=900&q=85"
                            alt="Contemporary home in a landscaped setting"
                            loading="lazy"
                        >
                        <span class="property-location-count">Explore Properties</span>
                    </a>
                    <div class="property-location-content">
                        <div>
                            <h3>Chandigarh</h3>
                            <p><i class="ri-map-pin-line"></i> Chandigarh</p>
                        </div>
                        <a href="/properties.php?location=Chandigarh"
                        class="property-location-arrow"
                        aria-label="View properties in Chandigarh">
                            <i class="ri-arrow-right-up-line"></i>
                        </a>
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