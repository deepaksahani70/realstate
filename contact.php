<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>contact-us</title>
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
  
    <?php
      $pageTitle = 'Contact Us';
      $breadcrumbItems = [
         ['label' => 'Contact Us', 'url' => null]
      ];
      include __DIR__ . '/includes/breadcrumb.php';
    ?> 

    <!-- Contact Section -->
    <section class="contact-section section section-soft" id="contact">
        <div class="container">
            <div class="contact-layout">

                <!-- Left: Contact Information -->
                <div class="contact-info">
                    <span class="contact-eyebrow">
                        <i class="ri-customer-service-2-line"></i>
                        WE'RE HERE TO HELP
                    </span>

                    <h2 class="contact-title">
                        Let's Find Your
                        <span>Perfect Property</span>
                    </h2>

                    <p class="contact-description">
                        Have a question about buying, selling, renting, or investing
                        in property? Send us a message and our team will help you
                        explore the right options.
                    </p>

                    <div class="contact-details">

                        <div class="contact-detail-item">
                            <div class="contact-detail-icon">
                                <i class="ri-phone-line"></i>
                            </div>
                            <div class="contact-detail-content">
                                <span>Call Us</span>
                                <h3>
                                    <a href="tel:+910000000000">
                                        +91 00000 00000
                                    </a>
                                </h3>
                                <p>Speak with our property experts.</p>
                            </div>
                        </div>

                        <div class="contact-detail-item">
                            <div class="contact-detail-icon">
                                <i class="ri-mail-line"></i>
                            </div>
                            <div class="contact-detail-content">
                                <span>Email Us</span>
                                <h3>
                                    <a href="mailto:info@example.com">
                                        info@example.com
                                    </a>
                                </h3>
                                <p>Send us your questions anytime.</p>
                            </div>
                        </div>

                        <div class="contact-detail-item">
                            <div class="contact-detail-icon">
                                <i class="ri-map-pin-line"></i>
                            </div>
                            <div class="contact-detail-content">
                                <span>Visit Our Office</span>
                                <h3>Our Office Address</h3>
                                <p>Add your complete business address here.</p>
                            </div>
                        </div>

                    </div>

                    <div class="contact-info-note">
                        <div class="contact-note-icon">
                            <i class="ri-shield-check-line"></i>
                        </div>
                        <div>
                            <strong>Your Property Journey Matters</strong>
                            <p>Get helpful guidance for your next real estate decision.</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Contact Form -->
                <div class="contact-form-card">
                    <div class="contact-form-heading">
                        <span>GET IN TOUCH</span>
                        <h2>Send Us a Message</h2>
                        <p>Fill in the details below and we'll get back to you.</p>
                    </div>

                    <form action="/contact-submit.php" method="POST" class="contact-form">

                        <div class="contact-form-row">
                            <div class="contact-field">
                                <label for="contactName">Full Name <span>*</span></label>
                                <div class="contact-input-wrap">
                                    <i class="ri-user-3-line"></i>
                                    <input
                                        type="text"
                                        id="contactName"
                                        name="name"
                                        placeholder="Enter your full name"
                                        autocomplete="name"
                                        maxlength="100"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="contact-field">
                                <label for="contactPhone">Phone Number <span>*</span></label>
                                <div class="contact-input-wrap">
                                    <i class="ri-phone-line"></i>
                                    <input
                                        type="tel"
                                        id="contactPhone"
                                        name="phone"
                                        placeholder="+91 00000 00000"
                                        autocomplete="tel"
                                        maxlength="20"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="contact-form-row">
                            <div class="contact-field">
                                <label for="contactEmail">Email Address <span>*</span></label>
                                <div class="contact-input-wrap">
                                    <i class="ri-mail-line"></i>
                                    <input
                                        type="email"
                                        id="contactEmail"
                                        name="email"
                                        placeholder="you@example.com"
                                        autocomplete="email"
                                        maxlength="254"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="contact-field">
                                <label for="contactSubject">I'm Interested In <span>*</span></label>
                                <div class="contact-input-wrap">
                                    <i class="ri-home-4-line"></i>
                                    <select id="contactSubject" name="subject" required>
                                        <option value="" disabled selected>
                                            Select an option
                                        </option>
                                        <option value="Buying a Property">Buying a Property</option>
                                        <option value="Renting a Property">Renting a Property</option>
                                        <option value="Selling a Property">Selling a Property</option>
                                        <option value="Property Investment">Property Investment</option>
                                        <option value="Commercial Property">Commercial Property</option>
                                        <option value="Other Inquiry">Other Inquiry</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="contact-field">
                            <label for="contactMessage">Your Message <span>*</span></label>
                            <div class="contact-input-wrap contact-textarea-wrap">
                                <i class="ri-chat-3-line"></i>
                                <textarea
                                    id="contactMessage"
                                    name="message"
                                    rows="5"
                                    placeholder="Tell us what you're looking for..."
                                    maxlength="3000"
                                    required
                                ></textarea>
                            </div>
                        </div>

                        <!-- Honeypot field for basic bot detection -->
                        <div class="contact-honeypot" aria-hidden="true">
                            <label for="contactWebsite">Leave this field empty</label>
                            <input
                                type="text"
                                id="contactWebsite"
                                name="website"
                                tabindex="-1"
                                autocomplete="off"
                            >
                        </div>

                        <div class="contact-form-footer">
                            <p>
                                <i class="ri-lock-line"></i>
                                Your information will be handled responsibly.
                            </p>

                            <button type="submit" class="btn btn-primary contact-submit">
                                Send Message
                                <i class="ri-arrow-right-line"></i>
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </section>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="js/header.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>