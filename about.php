<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>about-us</title>
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
        $pageTitle = 'About Us';
        $breadcrumbItems = [
            ['label' => 'About Us', 'url' => null]
        ];

        include __DIR__ . '/includes/breadcrumb.php';
        ?> 
        
        <!-- About Company Section -->
        <section class="about-company-section section section-white" id="about-company">
            <div class="container">
                <div class="about-company-grid">

                    <!-- Image Side -->
                    <div class="about-company-visual">
                        <div class="about-company-image">
                            <img
                                src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=85"
                                alt="Beautiful modern residential property"
                                loading="lazy"
                            >
                        </div>

                        <div class="about-company-experience">
                            <i class="ri-home-heart-line"></i>
                            <div>
                                <strong>Your Property Journey</strong>
                                <span>Starts with the right guidance</span>
                            </div>
                        </div>

                        <div class="about-company-decoration"></div>
                    </div>

                    <!-- Content Side -->
                    <div class="about-company-content">
                        <span class="section-subtitle">
                            <i class="ri-building-2-line"></i>
                            ABOUT OUR COMPANY
                        </span>

                        <h2 class="section-title">
                            Helping You Find a Place
                            <span>To Call Home</span>
                        </h2>

                        <p class="about-company-intro">
                            Finding the right property is more than a transaction.
                            It is about discovering a place where your future,
                            aspirations, and investments can grow.
                        </p>

                        <p class="about-company-description">
                            We help property seekers explore residential and
                            commercial opportunities that match their needs,
                            preferences, and budget. From discovering suitable
                            properties to understanding available options, our goal
                            is to make your real estate journey clearer and easier.
                        </p>

                        <div class="about-company-points">
                            <div class="about-company-point">
                                <span><i class="ri-check-line"></i></span>
                                <div>
                                    <h3>Personalized Property Guidance</h3>
                                    <p>Explore options suited to your individual requirements.</p>
                                </div>
                            </div>

                            <div class="about-company-point">
                                <span><i class="ri-check-line"></i></span>
                                <div>
                                    <h3>Clear & Informed Decisions</h3>
                                    <p>Understand your options before taking the next step.</p>
                                </div>
                            </div>

                            <div class="about-company-point">
                                <span><i class="ri-check-line"></i></span>
                                <div>
                                    <h3>Support Throughout Your Journey</h3>
                                    <p>Get assistance as you explore your property choices.</p>
                                </div>
                            </div>
                        </div>

                        <div class="about-company-actions">
                            <a href="/properties.php" class="btn btn-primary">
                                Explore Properties
                                <i class="ri-arrow-right-line"></i>
                            </a>

                            <a href="/contact-us.php" class="about-company-contact">
                                <i class="ri-customer-service-2-line"></i>
                                Talk to Our Team
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

                
        <!-- Company Highlights -->
        <section class="about-highlights-section section section-soft">
            <div class="container">
                <div class="about-highlights-grid">

                    <div class="about-highlight-card">
                        <div class="about-highlight-icon">
                            <i class="ri-home-4-line"></i>
                        </div>
                        <h3>500+</h3>
                        <p>Properties Listed</p>
                    </div>

                    <div class="about-highlight-card">
                        <div class="about-highlight-icon">
                            <i class="ri-user-heart-line"></i>
                        </div>
                        <h3>300+</h3>
                        <p>Happy Customers</p>
                    </div>

                    <div class="about-highlight-card">
                        <div class="about-highlight-icon">
                            <i class="ri-map-pin-line"></i>
                        </div>
                        <h3>25+</h3>
                        <p>Locations Covered</p>
                    </div>

                    <div class="about-highlight-card">
                        <div class="about-highlight-icon">
                            <i class="ri-hand-heart-line"></i>
                        </div>
                        <h3>Personal</h3>
                        <p>Customer Support</p>
                    </div>

                </div>
            </div>
        </section>

        
        <!-- Mission & Vision -->
        <section class="about-purpose-section section section-white">
            <div class="container">

                <div class="section-heading about-purpose-heading">
                    <span class="section-subtitle">
                        <i class="ri-compass-3-line"></i>
                        WHAT DRIVES US
                    </span>
                    <h2 class="section-title">
                        Our Purpose, <span>Our Promise</span>
                    </h2>
                    <p class="section-description">
                        Our approach is built around helping people make informed
                        property decisions with clarity, care, and confidence.
                    </p>
                </div>

                <div class="about-purpose-grid">

                    <article class="about-purpose-card about-mission-card">
                        <div class="about-purpose-icon">
                            <i class="ri-focus-3-line"></i>
                        </div>

                        <span class="about-purpose-label">OUR MISSION</span>

                        <h3>Making Property Decisions Easier</h3>

                        <p>
                            Our mission is to make property discovery more accessible
                            by helping buyers, sellers, tenants, and investors explore
                            suitable opportunities and understand their options.
                        </p>

                        <div class="about-purpose-footer">
                            <i class="ri-check-double-line"></i>
                            <span>Clarity in every step</span>
                        </div>
                    </article>

                    <article class="about-purpose-card about-vision-card">
                        <div class="about-purpose-icon">
                            <i class="ri-eye-line"></i>
                        </div>

                        <span class="about-purpose-label">OUR VISION</span>

                        <h3>Building Trust Through Better Experiences</h3>

                        <p>
                            Our vision is to create a dependable property discovery
                            experience where people can explore opportunities,
                            compare their choices, and move forward with confidence.
                        </p>

                        <div class="about-purpose-footer">
                            <i class="ri-heart-line"></i>
                            <span>People at the centre</span>
                        </div>
                    </article>

                </div>
            </div>
        </section>

        
        <!-- Why Choose Us -->
        <section class="about-benefits-section section section-soft">
            <div class="container">

                <div class="section-heading about-benefits-heading">
                    <span class="section-subtitle">
                        <i class="ri-award-line"></i>
                        THE DIFFERENCE WE MAKE
                    </span>

                    <h2 class="section-title">
                        Why Choose <span>Our Team?</span>
                    </h2>

                    <p class="section-description">
                        A property decision deserves careful consideration.
                        We aim to make your search simpler, more informed, and
                        focused on your needs.
                    </p>
                </div>

                <div class="about-benefits-grid">

                    <article class="about-benefit-card">
                        <div class="about-benefit-icon">
                            <i class="ri-user-search-line"></i>
                        </div>
                        <h3>Personalized Search</h3>
                        <p>Explore properties based on your preferences, budget, and goals.</p>
                    </article>

                    <article class="about-benefit-card">
                        <div class="about-benefit-icon">
                            <i class="ri-shield-check-line"></i>
                        </div>
                        <h3>Clear Information</h3>
                        <p>Review available property details to help you compare your options.</p>
                    </article>

                    <article class="about-benefit-card">
                        <div class="about-benefit-icon">
                            <i class="ri-customer-service-2-line"></i>
                        </div>
                        <h3>Helpful Support</h3>
                        <p>Get assistance with your questions throughout the discovery process.</p>
                    </article>

                    <article class="about-benefit-card">
                        <div class="about-benefit-icon">
                            <i class="ri-map-2-line"></i>
                        </div>
                        <h3>Location-Based Options</h3>
                        <p>Discover properties in areas that fit your lifestyle and requirements.</p>
                    </article>

                    <article class="about-benefit-card">
                        <div class="about-benefit-icon">
                            <i class="ri-scales-3-line"></i>
                        </div>
                        <h3>Informed Comparisons</h3>
                        <p>Evaluate property features and pricing before deciding what suits you.</p>
                    </article>

                    <article class="about-benefit-card">
                        <div class="about-benefit-icon">
                            <i class="ri-time-line"></i>
                        </div>
                        <h3>A Simpler Experience</h3>
                        <p>Find relevant property information in one convenient place.</p>
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