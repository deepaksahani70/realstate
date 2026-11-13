
<?php
require_once __DIR__ . '/admin/config/database.php';

$baseUrl = rtrim(APP_URL, '/');

function team_page_e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function team_page_image($image): string
{
    $image = trim((string) $image);

    if ($image === '') {
        return '';
    }

    if (preg_match('/^https?:\/\//i', $image)) {
        return $image;
    }

    return rtrim(APP_URL, '/') . '/' . ltrim($image, '/');
}

// Fetch active team members.
$teamMembers = [];

try {
    $stmt = $pdo->prepare("
        SELECT
            id,
            name,
            slug,
            designation,
            short_description AS description,
            image,
            linkedin,
            instagram,
            facebook
        FROM team_members
        WHERE is_active = 1
        ORDER BY sort_order ASC, id DESC
    ");

    $stmt->execute();
    $teamMembers = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log('Frontend team query error: ' . $e->getMessage());
    $teamMembers = [];
}

$pageTitle = 'Our Team | Real Estate Experts';
$pageDescription = 'Meet our experienced real estate professionals dedicated to helping you find the right property.';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= team_page_e($pageTitle) ?></title>
    <meta name="description" content="<?= team_page_e($pageDescription) ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= team_page_e($baseUrl) ?>/css/style.css">
    <link rel="stylesheet" href="<?= team_page_e($baseUrl) ?>/css/header.css">
    <link rel="stylesheet" href="<?= team_page_e($baseUrl) ?>/css/footer.css">
</head>

<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="team-page">

    <?php include __DIR__ . '/includes/breadcrumb.php'; ?>

    <!-- Team Section Start -->
    <section class="team-section section section-white" id="team">
        <div class="container">

            <div class="section-heading">
                <span class="section-subtitle">MEET OUR EXPERTS</span>
                <h2 class="section-title">Meet Our Professional Team</h2>
                <p class="section-description">
                    Get to know the dedicated professionals behind our success.
                    Our experienced team is here to guide you through every step
                    of your real estate journey.
                </p>
            </div>

            <div class="row g-4">

                <?php if (!empty($teamMembers)): ?>

                    <?php foreach ($teamMembers as $member): ?>

                        <div class="col-lg-4 col-md-4 col-sm-6">

                            <div class="team-card">

                                <div class="team-image">

                                    <a href="<?= team_page_e($baseUrl) ?>/team/profile-details.php?member=<?= urlencode($member['slug']) ?>">

                                        <?php if (!empty($member['image'])): ?>
                                            <img
                                                src="<?= team_page_e(team_page_image($member['image'])) ?>"
                                                alt="<?= team_page_e($member['name']) ?>"
                                                loading="lazy"
                                            >
                                        <?php else: ?>
                                            <div class="d-flex align-items-center justify-content-center bg-light"
                                                style="height:360px;">
                                                <i class="ri-user-line fs-1 text-muted"></i>
                                            </div>
                                        <?php endif; ?>

                                    </a>

                                    <div class="team-social">

                                        <?php if (!empty($member['linkedin'])): ?>
                                            <a href="<?= team_page_e($member['linkedin']) ?>"
                                                target="_blank" rel="noopener noreferrer"
                                                aria-label="LinkedIn">
                                                <i class="ri-linkedin-fill"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!empty($member['instagram'])): ?>
                                            <a href="<?= team_page_e($member['instagram']) ?>"
                                                target="_blank" rel="noopener noreferrer"
                                                aria-label="Instagram">
                                                <i class="ri-instagram-line"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!empty($member['facebook'])): ?>
                                            <a href="<?= team_page_e($member['facebook']) ?>"
                                                target="_blank" rel="noopener noreferrer"
                                                aria-label="Facebook">
                                                <i class="ri-facebook-fill"></i>
                                            </a>
                                        <?php endif; ?>

                                    </div>
                                </div>

                                <div class="team-content">

                                    <span class="team-designation">
                                        <?= team_page_e($member['designation']) ?>
                                    </span>

                                    <h4><?= team_page_e($member['name']) ?></h4>

                                    <p>
                                        <?= team_page_e($member['description']) ?>
                                    </p>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="col-12 text-center py-5">
                        <i class="ri-team-line fs-1 text-muted"></i>
                        <h4 class="mt-3">Our team profiles are coming soon.</h4>
                        <p class="text-muted mb-0">
                            Please check back later to meet our professionals.
                        </p>
                    </div>

                <?php endif; ?>

            </div>

        </div>
    </section>
    <!-- Team Section End -->

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
