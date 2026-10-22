<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blogs</title>
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
        $pageTitle = 'Blog';
        $breadcrumbItems = [
            ['label' => 'Blogs', 'url' => null]
        ];

        include __DIR__ . '/includes/breadcrumb.php';
       ?> 

       <section class="blog-section section section-white" id="blogs">
            <div class="container">  

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