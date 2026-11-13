<header class="admin-topbar">
    <div class="admin-topbar-left">
        <button type="button" class="admin-menu-toggle"
            id="adminMenuToggle" aria-label="Toggle sidebar"
            aria-controls="adminSidebar" aria-expanded="false">
            <i class="ri-menu-line"></i>
        </button>

        <div>
            <h1 class="admin-page-title">
                <?= htmlspecialchars($adminPageTitle ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="admin-page-subtitle">Manage your website</p>
        </div>
    </div>

    <div class="admin-topbar-right">
        <a href="enquiries/index.php" class="admin-icon-button"
            aria-label="View enquiries">
            <i class="ri-notification-3-line"></i>
        </a>

        <div class="admin-profile">
            <span class="admin-avatar">
                <i class="ri-user-line"></i>
            </span>
            <div class="admin-profile-info">
                <strong>Administrator</strong>
                <small>Super Admin</small>
            </div>
        </div>
    </div>
</header>