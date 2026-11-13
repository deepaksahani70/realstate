
<?php
require_once __DIR__ . '/admin/config/database.php';
function blog_front_e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$baseUrl = rtrim(APP_URL, '/');

/* ---------------------------------------
   Fetch Published Blogs
--------------------------------------- */

try {
    $stmt = $pdo->prepare("
        SELECT
            id,
            title,
            slug,
            category,
            excerpt,
            featured_image,
            image_alt,
            reading_time,
            published_at
        FROM blogs
        WHERE status = 'published'
          AND published_at IS NOT NULL
        ORDER BY published_at DESC, id DESC
    ");

    $stmt->execute();
    $blogs = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log('Frontend blogs error: ' . $e->getMessage());
    $blogs = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blogs | Real Estate</title>
    <meta name="description" content="Explore real estate guides, property insights, home-buying tips and the latest property advice.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <?php if (!empty($blogs)): ?>
                        <?php foreach ($blogs as $blog): ?>

                            <?php
                            $detailUrl = $baseUrl . '/blog/blog-details.php?slug=' . rawurlencode($blog['slug']);
                            $imageUrl = $baseUrl . ($blog['featured_image'] ?? '');
                            ?>

                            <article class="blog-card">
                                <a href="<?= blog_front_e($detailUrl) ?>"
                                   class="blog-image">
                                    <?php if (!empty($blog['featured_image'])): ?>
                                        <img
                                            src="<?= blog_front_e($imageUrl) ?>"
                                            alt="<?= blog_front_e($blog['image_alt'] ?: $blog['title']) ?>"
                                            loading="lazy">

                                    <?php else: ?>
                                        <img
                                            src="https://placehold.co/900x600/eaf1f5/173b57?text=Real+Estate"
                                            alt="<?= blog_front_e($blog['title']) ?>"
                                            loading="lazy">

                                    <?php endif; ?>
                                    <span class="blog-category">
                                        <?= blog_front_e($blog['category']) ?>
                                    </span>

                                </a>

                                <div class="blog-content">
                                    <div class="blog-meta">
                                        <span>
                                            <i class="ri-calendar-line"></i>

                                            <?= !empty($blog['published_at'])
                                                ? date('M d, Y', strtotime($blog['published_at']))
                                                : '' ?>
                                        </span>

                                        <span>
                                            <i class="ri-time-line"></i>
                                            <?= (int)$blog['reading_time'] ?> min read
                                        </span>
                                    </div>

                                    <h3>
                                        <a href="<?= blog_front_e($detailUrl) ?>">
                                            <?= blog_front_e($blog['title']) ?>
                                        </a>
                                    </h3>
                                    <p>
                                        <?= blog_front_e($blog['excerpt']) ?>
                                    </p>

                                    <a href="<?= blog_front_e($detailUrl) ?>"
                                       class="blog-read-more">

                                        Read Article
                                        <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>

                            </article>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="col-12 text-center py-5">
                            <i class="ri-article-line fs-1 text-muted"></i>
                            <h3 class="mt-3">No Blogs Available</h3>
                            <p class="text-muted">
                                Our latest articles and property insights will appear here soon.
                            </p>
                        </div>

                    <?php endif; ?>

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
