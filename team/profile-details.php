
<?php
require_once __DIR__ . '/../admin/config/database.php';

$baseUrl = rtrim(APP_URL, '/');

function team_e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function team_profile_image($image): string
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

function team_profile_lines($value): array
{
    if (empty($value)) {
        return [];
    }

    return array_values(array_filter(
        array_map('trim', preg_split('/\r\n|\r|\n/', (string) $value)),
        static fn($item) => $item !== ''
    ));
}

$slug = trim($_GET['member'] ?? '');

if ($slug === '') {
    http_response_code(404);
    exit('Team member not found.');
}

try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM team_members
        WHERE slug = ? AND is_active = 1
        LIMIT 1
    ");

    $stmt->execute([$slug]);
    $member = $stmt->fetch();

} catch (PDOException $e) {
    error_log('Team profile query error: ' . $e->getMessage());
    $member = false;
}

if (!$member) {
    http_response_code(404);
    exit('Team member not found.');
}

$member['bio'] = team_profile_lines($member['bio']);
$member['skills'] = team_profile_lines($member['skills']);
$member['achievements'] = team_profile_lines($member['achievements']);

$pageTitle = $member['name'] . ' | Team Profile';
$pageDescription = $member['name'] . ' - ' . $member['designation'] . '. Learn more about our real estate professional and expertise.';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= team_e($pageTitle) ?></title>
    <meta name="description" content="<?= team_e($pageDescription) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= team_e($baseUrl) ?>/css/style.css">
    <link rel="stylesheet" href="<?= team_e($baseUrl) ?>/css/header.css">
    <link rel="stylesheet" href="<?= team_e($baseUrl) ?>/css/footer.css">
    <link rel="stylesheet" href="<?= team_e($baseUrl) ?>/team/css/profile-details.css">
</head>

<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="team-profile-page">

    <?php include __DIR__ . '/../includes/breadcrumb.php'; ?>

    <!-- Profile Hero Start -->
    <section class="team-profile-hero section section-white">
        <div class="container">

            <div class="row align-items-center g-5">

                <div class="col-lg-5">
                    <div class="profile-image-wrapper">

                        <img
                            src="<?= team_e(team_profile_image($member['image'])) ?>"
                            alt="<?= team_e($member['name']) ?>"
                            class="profile-main-image"
                        >

                        <div class="profile-experience-badge">
                            <i class="ri-award-line"></i>
                            <div>
                                <strong><?= team_e($member['experience']) ?></strong>
                                <span>Experience</span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="profile-hero-content">

                        <span class="profile-subtitle">
                            <i class="ri-user-star-line"></i>
                            MEET OUR EXPERT
                        </span>

                        <h1><?= team_e($member['name']) ?></h1>

                        <span class="profile-designation">
                            <?= team_e($member['designation']) ?>
                        </span>

                        <p class="profile-intro">
                            <?= team_e($member['intro']) ?>
                        </p>

                        <div class="profile-quick-info">

                            <div class="profile-info-item">
                                <i class="ri-map-pin-line"></i>
                                <span><?= team_e($member['location']) ?></span>
                            </div>

                            <div class="profile-info-item">
                                <i class="ri-building-2-line"></i>
                                <span><?= team_e($member['specialization']) ?></span>
                            </div>

                        </div>

                        <div class="profile-social-links">
                            <?php if (!empty($member['linkedin'])): ?>
                                <a href="<?= team_e($member['linkedin']) ?>" aria-label="LinkedIn">
                                    <i class="ri-linkedin-fill"></i>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($member['instagram'])): ?>
                                <a href="<?= team_e($member['instagram']) ?>" aria-label="Instagram">
                                    <i class="ri-instagram-line"></i>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($member['facebook'])): ?>
                                <a href="<?= team_e($member['facebook']) ?>" aria-label="Facebook">
                                    <i class="ri-facebook-fill"></i>
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="profile-hero-buttons">
                            <?php if (!empty($member['email'])): ?>
                                <a href="mailto:<?= team_e($member['email']) ?>" class="btn btn-primary">
                                    Contact Me <i class="ri-arrow-right-line"></i>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($member['phone'])): ?>
                                <a href="tel:<?= team_e($member['phone']) ?>" class="btn btn-outline-primary">
                                    <i class="ri-phone-line"></i> Call Now
                                </a>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </section>
    <!-- Profile Hero End -->


    <!-- Profile Overview Start -->
    <section class="profile-overview-section section section-light">
        <div class="container">

            <div class="row g-4">

                <div class="col-lg-4 col-md-6">
                    <div class="profile-stat-card">
                        <div class="profile-stat-icon">
                            <i class="ri-briefcase-4-line"></i>
                        </div>
                        <div>
                            <h3><?= team_e($member['experience']) ?></h3>
                            <p>Industry Experience</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="profile-stat-card">
                        <div class="profile-stat-icon">
                            <i class="ri-home-4-line"></i>
                        </div>
                        <div>
                            <h3><?= team_e($member['projects']) ?></h3>
                            <p>Successful Transactions</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="profile-stat-card">
                        <div class="profile-stat-icon">
                            <i class="ri-user-heart-line"></i>
                        </div>
                        <div>
                            <h3>100%</h3>
                            <p>Client Focused Approach</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>
    <!-- Profile Overview End -->


    <!-- About Profile Start -->
    <section class="profile-about-section section section-white">
        <div class="container">

            <div class="row g-5">

                <div class="col-lg-7">

                    <div class="profile-section-heading">
                        <span class="section-subtitle">GET TO KNOW ME</span>
                        <h2 class="section-title">About <?= team_e($member['name']) ?></h2>
                    </div>

                    <div class="profile-biography">
                        <?php foreach ($member['bio'] as $paragraph): ?>
                            <p><?= team_e($paragraph) ?></p>
                        <?php endforeach; ?>
                    </div>

                    <div class="profile-quote">
                        <i class="ri-double-quotes-l"></i>
                        <p><?= team_e($member['intro']) ?></p>
                    </div>

                </div>

                <div class="col-lg-5">

                    <div class="profile-skills-card">

                        <span class="section-subtitle">MY EXPERTISE</span>
                        <h3>Skills & Specializations</h3>

                        <div class="profile-skills-list">
                            <?php foreach ($member['skills'] as $skill): ?>
                                <div class="profile-skill-item">
                                    <i class="ri-checkbox-circle-fill"></i>
                                    <span><?= team_e($skill) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </section>
    <!-- About Profile End -->


    <!-- Achievements Start -->
    <section class="profile-achievements-section section section-soft">
        <div class="container">

            <div class="section-heading">
                <span class="section-subtitle">PROFESSIONAL JOURNEY</span>
                <h2 class="section-title">Experience & Achievements</h2>
                <p class="section-description">
                    Professional milestones and accomplishments that reflect
                    our commitment to excellence in real estate.
                </p>
            </div>

            <div class="profile-achievements-list">

                <?php foreach ($member['achievements'] as $achievement): ?>
                    <div class="profile-achievement-item">
                        <span class="achievement-icon">
                            <i class="ri-award-line"></i>
                        </span>
                        <p><?= team_e($achievement) ?></p>
                    </div>
                <?php endforeach; ?>

            </div>

        </div>
    </section>
    <!-- Achievements End -->


    <!-- Contact CTA Start -->
    <section class="profile-contact-section section section-white">
        <div class="container">

            <div class="profile-contact-wrapper">

                <div class="profile-contact-content">
                    <span>LET'S CONNECT</span>
                    <h2>Looking for Expert Property Guidance?</h2>
                    <p>
                        Get in touch with <?= team_e($member['name']) ?> for
                        professional advice and personalized property assistance.
                    </p>

                    <div class="profile-contact-actions">
                        <?php if (!empty($member['phone'])): ?>
                            <a href="tel:<?= team_e($member['phone']) ?>" class="btn btn-light">
                                <i class="ri-phone-line"></i> Call Now
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($member['email'])): ?>
                            <a href="mailto:<?= team_e($member['email']) ?>" class="btn btn-accent">
                                Send an Email <i class="ri-arrow-right-line"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-contact-icon">
                    <i class="ri-customer-service-2-line"></i>
                </div>

            </div>

        </div>
    </section>
    <!-- Contact CTA End -->

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
