
<header class="re-header" id="siteHeader">
    <div class="container">
        <div class="re-header-inner">

            <!-- Logo -->
            <a href="/" class="re-logo" aria-label="Home">
                <span class="re-logo-icon">
                    <i class="bi bi-buildings-fill"></i>
                </span>
                <span class="re-logo-text">
                    <strong>your<span>logo</span></strong>
                    <small>FIND YOUR PLACE</small>
                </span>
            </a>

            <!-- Desktop Navigation -->
            <nav class="re-desktop-nav" aria-label="Main navigation">
                <ul class="re-nav-list">

                    <li class="re-nav-item">
                        <a href="index.php" class="re-nav-link active">Home</a>
                    </li>

                    <!-- Properties Dropdown -->
                    <li class="re-nav-item re-has-dropdown">
                        <a href="properties.php" class="re-nav-link">
                            Properties
                            <i class="bi bi-chevron-down re-chevron"></i>
                        </a>

                        <ul class="re-dropdown">
                            <li>
                                <a href="properties.php?type=buy">
                                    <i class="bi bi-house-door"></i>
                                    Buy Property
                                </a>
                            </li>
                            <li>
                                <a href="properties.php?type=rent">
                                    <i class="bi bi-key"></i>
                                    Rent Property
                                </a>
                            </li>
                            <li>
                                <a href="properties.php?type=commercial">
                                    <i class="bi bi-building"></i>
                                    Commercial Property
                                </a>
                            </li>
                            <li>
                                <a href="properties.php?type=plot">
                                    <i class="bi bi-geo-alt"></i>
                                    Plots & Land
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Locations Dropdown -->
                    <li class="re-nav-item re-has-dropdown">
                        <a href="locations.php" class="re-nav-link">
                            Locations
                            <i class="bi bi-chevron-down re-chevron"></i>
                        </a>

                        <ul class="re-dropdown">
                            <li>
                                <a href="locations.php?city=gurgaon">
                                    Gurgaon
                                </a>
                            </li>
                            <li>
                                <a href="locations.php?city=delhi">
                                    Delhi
                                </a>
                            </li>
                            <li>
                                <a href="locations.php?city=noida">
                                    Noida
                                </a>
                            </li>
                            <li>
                                <a href="locations.php">
                                    All Locations
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="re-nav-item">
                        <a href="about.php" class="re-nav-link">About Us</a>
                    </li>

                    <li class="re-nav-item">
                        <a href="blog.php" class="re-nav-link">Blog</a>
                    </li>

                    <li class="re-nav-item">
                        <a href="contact.php" class="re-nav-link">Contact</a>
                    </li>

                </ul>
            </nav>

            <!-- Desktop CTA -->
            <div class="re-header-actions">
                <a href="add-property.php" class="btn btn-primary re-header-cta">
                    <i class="bi bi-plus-lg"></i>
                    List Your Property
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <button
                type="button"
                class="re-menu-toggle"
                id="reMenuToggle"
                aria-label="Open navigation menu"
                aria-controls="reMobileDrawer"
                aria-expanded="false"
            >
                <i class="bi bi-list"></i>
            </button>

        </div>
    </div>
</header>

<!-- Mobile Overlay -->
<div class="re-mobile-overlay" id="reMobileOverlay"></div>

<!-- Mobile Right-to-Left Drawer -->
<aside
    class="re-mobile-drawer"
    id="reMobileDrawer"
    aria-label="Mobile navigation"
    aria-hidden="true"
    inert
>
    <div class="re-drawer-header">

        <a href="index.php" class="re-logo">
            <span class="re-logo-icon">
                <i class="bi bi-buildings-fill"></i>
            </span>
            <span class="re-logo-text">
                <strong>Your<span>logo</span></strong>
                <small>FIND YOUR PLACE</small>
            </span>
        </a>

        <button type="button" class="re-drawer-close" id="reDrawerClose" aria-label="Close navigation menu">
            <i class="bi bi-x-lg"></i>
        </button>

    </div>

    <nav class="re-mobile-nav" aria-label="Mobile main navigation">
        <ul class="re-mobile-nav-list">

            <li>
                <a href="index.php" class="re-mobile-link active">
                    <i class="bi bi-house-door"></i>
                    <span>Home</span>
                </a>
            </li>

            <!-- Mobile Properties Accordion -->
            <li class="re-mobile-has-dropdown">
                <div class="re-mobile-link-row">
                    <a href="/properties.php" class="re-mobile-link">
                        <i class="bi bi-buildings"></i>
                        <span>Properties</span>
                    </a>
                    <button type="button" class="re-mobile-submenu-toggle" aria-label="Toggle Properties submenu" aria-expanded="false" aria-controls="rePropertiesSubmenu">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>

                <ul class="re-mobile-submenu" id="rePropertiesSubmenu" hidden>
                    <li>
                        <a href="properties.php?type=buy">Buy Property</a>
                    </li>
                    <li>
                        <a href="properties.php?type=rent">Rent Property</a>
                    </li>
                    <li>
                        <a href="properties.php?type=commercial">
                            Commercial Property
                        </a>
                    </li>
                    <li>
                        <a href="properties.php?type=plot">Plots & Land</a>
                    </li>
                </ul>
            </li>

            <!-- Mobile Locations Accordion -->
            <li class="re-mobile-has-dropdown">
                <div class="re-mobile-link-row">
                    <a href="locations.php" class="re-mobile-link">
                        <i class="bi bi-geo-alt"></i>
                        <span>Locations</span>
                    </a>

                    <button type="button" class="re-mobile-submenu-toggle" aria-label="Toggle Locations submenu" aria-expanded="false"
                        aria-controls="reLocationsSubmenu"
                    >
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>

                <ul class="re-mobile-submenu" id="reLocationsSubmenu" hidden>
                    <li><a href="locations.php?city=gurgaon">Gurgaon</a></li>
                    <li><a href="locations.php?city=delhi">Delhi</a></li>
                    <li><a href="locations.php?city=noida">Noida</a></li>
                    <li><a href="locations.php">All Locations</a></li>
                </ul>
            </li>

            <li>
                <a href="about.php" class="re-mobile-link">
                    <i class="bi bi-info-circle"></i>
                    <span>About Us</span>
                </a>
            </li>

            <li>
                <a href="blog.php" class="re-mobile-link">
                    <i class="bi bi-journal-text"></i>
                    <span>Blog</span>
                </a>
            </li>

            <li>
                <a href="contact.php" class="re-mobile-link">
                    <i class="bi bi-envelope"></i>
                    <span>Contact</span>
                </a>
            </li>

        </ul>
    </nav>

    <div class="re-drawer-footer">
        <p>Looking to sell or rent your property?</p>
        <a href="/add-property.php" class="btn btn-primary w-100">
            <i class="bi bi-plus-lg"></i>
            List Your Property
        </a>
        <a href="tel:+911234567890" class="re-drawer-phone">
            <i class="bi bi-telephone"></i>
            +91 12345 67890
        </a>
    </div>
</aside>
