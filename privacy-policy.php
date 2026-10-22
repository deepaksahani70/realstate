
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Privacy Policy | Your Real Estate Website</title>
    <meta name="description" content="Learn how we collect, use, and protect your personal information when you use our real estate website.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
</head>

<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <main>

        <?php
        $pageTitle = 'Privacy Policy';
        $breadcrumbItems = [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Privacy Policy', 'url' => null]
        ];
        include __DIR__ . '/includes/breadcrumb.php';
        ?>

        <section class="section section-white">
            <div class="container-custom">

                <div class="section-heading">
                    <span class="section-subtitle">Your Privacy Matters</span>
                    <h1 class="section-title">Privacy Policy</h1>
                    <p class="section-description">
                        We respect your privacy and are committed to protecting your
                        personal information. This policy explains how we collect,
                        use, and safeguard your information.
                    </p>
                    <p class="small text-muted-custom mt-3 mb-0">
                        <i class="ri-calendar-line"></i>
                        Last updated: October 22, 2026
                    </p>
                </div>

                <div class="privacy-layout">

                    <aside class="privacy-sidebar">
                        <h5>On This Page</h5>

                        <a href="#information-collected">Information We Collect</a>
                        <a href="#information-use">How We Use Information</a>
                        <a href="#cookies">Cookies</a>
                        <a href="#information-sharing">Information Sharing</a>
                        <a href="#data-security">Data Security</a>
                        <a href="#data-retention">Data Retention</a>
                        <a href="#your-rights">Your Privacy Rights</a>
                        <a href="#third-party">Third-Party Links</a>
                        <a href="#policy-changes">Policy Changes</a>
                        <a href="#contact-privacy">Contact Us</a>
                    </aside>

                    <article class="privacy-article">

                        <section id="information-collected">
                            <h2>1. Information We Collect</h2>
                            <p>
                                We may collect information you provide when submitting a
                                property enquiry, requesting information, or contacting
                                our team.
                            </p>
                            <ul>
                                <li>Your name, email address, and phone number.</li>
                                <li>Property preferences, location, budget, and property type.</li>
                                <li>Information included in your messages or enquiry forms.</li>
                                <li>Technical information such as browser type, device details,
                                    and website usage data where applicable.</li>
                            </ul>
                        </section>

                        <section id="information-use">
                            <h2>2. How We Use Your Information</h2>
                            <p>We may use your information to:</p>
                            <ul>
                                <li>Respond to property enquiries and requests.</li>
                                <li>Help you find properties matching your preferences.</li>
                                <li>Provide updates about services you have requested.</li>
                                <li>Improve website performance and user experience.</li>
                                <li>Protect our website and comply with legal obligations.</li>
                            </ul>
                            <p>
                                Marketing communications will be sent only where permitted
                                by applicable law. You may request to stop receiving them.
                            </p>
                        </section>

                        <section id="cookies">
                            <h2>3. Cookies and Tracking Technologies</h2>
                            <p>
                                Our website may use cookies to support essential functionality,
                                remember preferences, and understand website usage. You can
                                manage cookies through your browser settings. Disabling some
                                cookies may affect website functionality.
                            </p>
                        </section>

                        <section id="information-sharing">
                            <h2>4. Information Sharing</h2>
                            <p>
                                We do not sell your personal information. We may share
                                relevant information with:
                            </p>
                            <ul>
                                <li>Property owners, developers, or partners when necessary
                                    to respond to your enquiry.</li>
                                <li>Service providers that support website hosting or
                                    communications.</li>
                                <li>Authorities where disclosure is required by law.</li>
                            </ul>
                        </section>

                        <section id="data-security">
                            <h2>5. Data Security</h2>
                            <p>
                                We take reasonable technical and organizational measures
                                to protect your information against unauthorized access,
                                loss, misuse, or disclosure. However, no online transmission
                                or storage method can be guaranteed completely secure.
                            </p>
                        </section>

                        <section id="data-retention">
                            <h2>6. Data Retention</h2>
                            <p>
                                We retain personal information for as long as reasonably
                                necessary to respond to enquiries, provide requested services,
                                maintain business records, and meet applicable legal
                                requirements.
                            </p>
                        </section>

                        <section id="your-rights">
                            <h2>7. Your Privacy Rights</h2>
                            <p>
                                Depending on applicable law, you may be able to request
                                access to, correction of, or deletion of your personal
                                information, or withdraw consent where processing is
                                based on consent.
                            </p>
                            <p>
                                Contact us to make a privacy-related request. We may need
                                to verify your identity before responding.
                            </p>
                        </section>

                        <section id="third-party">
                            <h2>8. Third-Party Links</h2>
                            <p>
                                Our website may contain links to third-party websites.
                                Their privacy practices are governed by their own policies,
                                which we encourage you to review.
                            </p>
                        </section>

                        <section id="policy-changes">
                            <h2>9. Changes to This Policy</h2>
                            <p>
                                We may update this policy from time to time. Changes will
                                be published on this page along with the revised update date.
                            </p>
                        </section>

                        <section id="contact-privacy">
                            <h2>10. Contact Us</h2>
                            <p>
                                If you have questions about this Privacy Policy or how
                                your information is handled, please contact our team.
                            </p>
                            <a href="contact-us.php" class="btn btn-primary">
                                Contact Us
                                <i class="ri-arrow-right-line"></i>
                            </a>
                        </section>

                    </article>

                </div>
            </div>
        </section>

    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/header.js" defer></script>
    <script src="../js/main.js"></script>

</body>
</html>
