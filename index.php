<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
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
                <form action="/properties.php" method="get" class="re-property-search" id="rePropertySearch">
                    <input type="hidden" name="purpose" id="reSearchPurpose" value="buy">
                    <div class="re-search-field">
                        <label for="reSearchLocation">Location</label>
                        <div class="re-search-input-wrap"><i class="bi bi-geo-alt"></i><input type="text" id="reSearchLocation" name="location" placeholder="City, area or locality" autocomplete="address-level2"></div>
                    </div>
                    <div class="re-search-field">
                        <label for="reSearchType">Property Type</label>
                        <div class="re-search-input-wrap"><i class="bi bi-house-door"></i><select id="reSearchType" name="property_type"><option value="">All Property Types</option><option value="apartment">Apartment / Flat</option><option value="house">Independent House</option><option value="villa">Villa</option><option value="plot">Plot / Land</option><option value="commercial">Commercial Space</option></select></div>
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
                <a href="/properties.php" class="re-featured-view-all">
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
                    <a href="/properties.php?purpose=buy" class="re-featured-category active">Buy Property</a>
                    <a href="/properties.php?purpose=rent" class="re-featured-category">Rent Property</a>
                    <a href="/properties.php?property_type=luxury" class="re-featured-category">Luxury Homes</a>
                </div>
            </div>

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

            <!-- Section Footer CTA -->
            <div class="re-featured-bottom">
                <div class="re-featured-bottom-icon">
                    <i class="ri-customer-service-2-line"></i>
                </div>
                <div class="re-featured-bottom-copy">
                    <h4>Looking for something extraordinary?</h4>
                    <p>Let our property specialists find a home that matches your vision.</p>
                </div>
                <a href="/contact.php" class="re-featured-contact">
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
                <a href="/properties.php?location=gurugram" class="re-location-card re-location-large">
                    <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1000&q=85" alt="Residential property in Gurugram" loading="lazy">
                    <span class="re-location-overlay"></span>
                    <span class="re-location-content">
                        <span class="re-location-count">Explore Properties</span>
                        <span class="re-location-name">Gurugram</span>
                        <span class="re-location-link">View Properties <i class="bi bi-arrow-up-right"></i></span>
                    </span>
                </a>
                <a href="/properties.php?location=delhi" class="re-location-card">
                    <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=800&q=85" alt="Modern residential property in Delhi" loading="lazy">
                    <span class="re-location-overlay"></span>
                    <span class="re-location-content">
                        <span class="re-location-count">Explore Properties</span>
                        <span class="re-location-name">New Delhi</span>
                        <span class="re-location-link">View Properties <i class="bi bi-arrow-up-right"></i></span>
                    </span>
                </a>
                <a href="/properties.php?location=noida" class="re-location-card">
                    <img src="https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=800&q=85" alt="Contemporary home in Noida" loading="lazy">
                    <span class="re-location-overlay"></span>
                    <span class="re-location-content">
                        <span class="re-location-count">Explore Properties</span>
                        <span class="re-location-name">Noida</span>
                        <span class="re-location-link">View Properties <i class="bi bi-arrow-up-right"></i></span>
                    </span>
                </a>
                <a href="/properties.php?location=jaipur" class="re-location-card">
                    <img src="https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?auto=format&fit=crop&w=800&q=85" alt="Beautiful residential home in Jaipur" loading="lazy">
                    <span class="re-location-overlay"></span>
                    <span class="re-location-content">
                        <span class="re-location-count">Explore Properties</span>
                        <span class="re-location-name">Jaipur</span>
                        <span class="re-location-link">View Properties <i class="bi bi-arrow-up-right"></i></span>
                    </span>
                </a>
                <a href="/properties.php?location=chandigarh" class="re-location-card">
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
                <a href="/locations.php" class="btn btn-primary">Explore All Locations <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </section>

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
                        <a href="/properties.php" class="re-how-link">Explore Properties <i class="ri-arrow-right-line"></i></a>
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
                        <a href="/contact.php" class="re-how-link">Book a Visit <i class="ri-arrow-right-line"></i></a>
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
                        <a href="/contact.php" class="re-how-link">Talk to an Expert <i class="ri-arrow-right-line"></i></a>
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
                <a href="/contact.php" class="btn btn-primary re-how-bottom-btn">Get Expert Assistance <i class="ri-arrow-right-up-line"></i></a>
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
                    <a href="/properties.php" class="re-categories-view-all">
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
                    <a href="/properties.php?property_type=apartment" class="re-category-card">
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
                    <a href="/properties.php?property_type=house" class="re-category-card">
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
                    <a href="/properties.php?property_type=villa" class="re-category-card">
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
                    <a href="/properties.php?property_type=plot" class="re-category-card">
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
                    <a href="/properties.php?property_type=commercial" class="re-category-card">
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
                    <a href="/properties.php?purpose=buy"
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

                        <a href="/properties.php?purpose=buy"
                        class="home-service-link">
                            Explore Properties
                            <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </article>

                <!-- Rent Property -->
                <article class="home-service-card">
                    <a href="/properties.php?purpose=rent"
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

                        <a href="/properties.php?purpose=rent"
                        class="home-service-link">
                            Find Rentals
                            <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </article>

                <!-- Sell Property -->
                <article class="home-service-card">
                    <a href="/contact-us.php?service=sell"
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

                        <a href="/contact-us.php?service=sell"
                        class="home-service-link">
                            Talk to Our Team
                            <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </article>

                <!-- Investment -->
                <article class="home-service-card">
                    <a href="/projects.php"
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

                        <a href="/projects.php"
                        class="home-service-link">
                            Explore Opportunities
                            <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </article>

            </div>

            <div class="home-services-bottom">
                <p>Looking for something specific?</p>
                <a href="/contact-us.php" class="btn btn-outline-primary">
                    Discuss Your Requirements
                    <i class="ri-arrow-right-line"></i>
                </a>
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
            <div class="testimonials-slider  ">

                <div class="testimonial-slide">
                    <article class="testimonial-card">
                        <div class="testimonial-top">
                            <span class="testimonial-quote">
                                <i class="ri-double-quotes-l"></i>
                            </span>
                            <div class="testimonial-stars" aria-label="Sample 5-star rating">
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                            </div>
                        </div>

                        <p class="testimonial-text">
                            The property search process felt straightforward.
                            Having different options to compare helped us
                            understand what suited our needs.
                        </p>

                        <div class="testimonial-divider"></div>

                        <div class="testimonial-user">
                            <div class="testimonial-avatar avatar-one">RK</div>
                            <div class="testimonial-user-info">
                                <h3>Rahul Kumar</h3>
                                <span><i class="ri-map-pin-line"></i> Gurugram, Haryana</span>
                            </div>
                        </div>
                    </article>
                </div>

                <div class="testimonial-slide">
                    <article class="testimonial-card">
                        <div class="testimonial-top">
                            <span class="testimonial-quote">
                                <i class="ri-double-quotes-l"></i>
                            </span>
                            <div class="testimonial-stars" aria-label="Sample 5-star rating">
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                            </div>
                        </div>

                        <p class="testimonial-text">
                            I wanted a home that matched my budget and lifestyle.
                            Comparing property options helped me narrow down
                            my search more efficiently.
                        </p>

                        <div class="testimonial-divider"></div>

                        <div class="testimonial-user">
                            <div class="testimonial-avatar avatar-two">PS</div>
                            <div class="testimonial-user-info">
                                <h3>Priya Sharma</h3>
                                <span><i class="ri-map-pin-line"></i> Noida, Uttar Pradesh</span>
                            </div>
                        </div>
                    </article>
                </div>

                <div class="testimonial-slide">
                    <article class="testimonial-card">
                        <div class="testimonial-top">
                            <span class="testimonial-quote">
                                <i class="ri-double-quotes-l"></i>
                            </span>
                            <div class="testimonial-stars" aria-label="Sample 5-star rating">
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                            </div>
                        </div>

                        <p class="testimonial-text">
                            Exploring different properties helped me better
                            understand the available options for my
                            real estate investment goals.
                        </p>

                        <div class="testimonial-divider"></div>

                        <div class="testimonial-user">
                            <div class="testimonial-avatar avatar-three">AM</div>
                            <div class="testimonial-user-info">
                                <h3>Amit Mehta</h3>
                                <span><i class="ri-map-pin-line"></i> New Delhi</span>
                            </div>
                        </div>
                    </article>
                </div>

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
                        <a href="/properties.php" class="btn btn-primary">
                            Explore Properties
                            <i class="ri-arrow-right-line"></i>
                        </a>

                        <a href="/contact.php" class="property-cta-contact">
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

                <a href="/blogs.php" class="btn btn-outline-primary">
                    View All Articles <i class="ri-arrow-right-line"></i>
                </a>
            </div>

            <div class="blog-grid">

                <!-- Blog 1 -->
                <article class="blog-card">
                    <a href="/blog-details.php?slug=first-time-home-buying-guide"
                    class="blog-image">
                        <img
                            src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=900&q=85"
                            alt="Modern home for first-time buyers"
                            loading="lazy">
                        <span class="blog-category">Buying Guide</span>
                    </a>

                    <div class="blog-content">
                        <div class="blog-meta">
                            <span><i class="ri-calendar-line"></i> Oct 12, 2026</span>
                            <span><i class="ri-time-line"></i> 5 min read</span>
                        </div>

                        <h3>
                            <a href="/blog-details.php?slug=first-time-home-buying-guide">
                                A Complete Guide to Buying Your First Home
                            </a>
                        </h3>

                        <p>
                            Learn the essential steps, budgeting tips and important
                            checks to consider before purchasing your first property.
                        </p>

                        <a href="/blog-details.php?slug=first-time-home-buying-guide"
                        class="blog-read-more">
                            Read Article <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </article>

                <!-- Blog 2 -->
                <article class="blog-card">
                    <a href="/blog-details.php?slug=real-estate-investment-tips"
                    class="blog-image">
                        <img
                            src="https://images.unsplash.com/photo-1460472178825-e5240623afd5?auto=format&fit=crop&w=900&q=85"
                            alt="Real estate investment property"
                            loading="lazy">
                        <span class="blog-category">Investment</span>
                    </a>

                    <div class="blog-content">
                        <div class="blog-meta">
                            <span><i class="ri-calendar-line"></i> Oct 08, 2026</span>
                            <span><i class="ri-time-line"></i> 7 min read</span>
                        </div>

                        <h3>
                            <a href="/blog-details.php?slug=real-estate-investment-tips">
                                Smart Real Estate Investment Tips for Beginners
                            </a>
                        </h3>

                        <p>
                            Understand location selection, property evaluation and
                            the factors that influence long-term investment returns.
                        </p>

                        <a href="/blog-details.php?slug=real-estate-investment-tips"
                        class="blog-read-more">
                            Read Article <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </article>

                <!-- Blog 3 -->
                <article class="blog-card">
                    <a href="/blog-details.php?slug=choosing-the-right-location"
                    class="blog-image">
                        <img
                            src="https://images.unsplash.com/photo-1449158743715-0a90ebb6d2d8?auto=format&fit=crop&w=900&q=85"
                            alt="Homes in a residential neighbourhood"
                            loading="lazy">
                        <span class="blog-category">Location Guide</span>
                    </a>

                    <div class="blog-content">
                        <div class="blog-meta">
                            <span><i class="ri-calendar-line"></i> Oct 04, 2026</span>
                            <span><i class="ri-time-line"></i> 4 min read</span>
                        </div>

                        <h3>
                            <a href="/blog-details.php?slug=choosing-the-right-location">
                                How to Choose the Right Location for Your New Home
                            </a>
                        </h3>

                        <p>
                            Compare neighbourhood facilities, daily commute and
                            future development before selecting your next home.
                        </p>

                        <a href="/blog-details.php?slug=choosing-the-right-location"
                        class="blog-read-more">
                            Read Article <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </article>

            </div>

            <div class="blog-mobile-action">
                <a href="/blogs.php" class="btn btn-primary">
                    Explore All Articles <i class="ri-arrow-right-line"></i>
                </a>
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
</body>

</html>