
<?php
require_once __DIR__ . '/../admin/config/database.php';
function blog_detail_e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$baseUrl = rtrim(APP_URL, '/');
$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {
    http_response_code(404);
    exit('Blog not found.');
}

/*
|--------------------------------------------------------------------------
| Fetch current blog
|--------------------------------------------------------------------------
*/

try {
    $stmt = $pdo->prepare("
        SELECT
            id,
            title,
            slug,
            category,
            excerpt,
            intro,
            content,
            featured_image,
            image_alt,
            author_name,
            author_role,
            reading_time,
            tags,
            seo_description,
            published_at,
            created_at
        FROM blogs
        WHERE slug = ?
          AND status = 'published'
          AND published_at IS NOT NULL
        LIMIT 1
    ");

    $stmt->execute([$slug]);
    $blog = $stmt->fetch();

} catch (PDOException $e) {
    error_log('Blog detail query error: ' . $e->getMessage());
    http_response_code(500);
    exit('Unable to load this article.');
}

if (!$blog) {
    http_response_code(404);
    exit('Blog not found.');
}

/*
|--------------------------------------------------------------------------
| Blog details
|--------------------------------------------------------------------------
*/

$pageTitle = $blog['title'];

$metaDescription = !empty($blog['seo_description'])
    ? $blog['seo_description']
    : ($blog['excerpt'] ?? '');

$publishedDate = !empty($blog['published_at'])
    ? $blog['published_at']
    : $blog['created_at'];

$formattedDate = $publishedDate
    ? date('F d, Y', strtotime($publishedDate))
    : '';

$readingTime = max(1, (int) ($blog['reading_time'] ?? 5));

$authorName = $blog['author_name'] ?: 'Real Estate Editorial Team';
$authorRole = $blog['author_role'] ?: 'Property Insights';

$imageUrl = !empty($blog['featured_image'])
    ? (
        preg_match('/^https?:\/\//i', $blog['featured_image'])
            ? $blog['featured_image']
            : $baseUrl . '/' . ltrim($blog['featured_image'], '/')
    )
    : 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1800&q=85';

$imageAlt = $blog['image_alt'] ?: $blog['title'];

$articleUrl = $baseUrl . '/blog/blog-details.php?slug=' . rawurlencode($blog['slug']);

$encodedArticleUrl = urlencode($articleUrl);
$encodedArticleTitle = urlencode($blog['title']);

/*
|--------------------------------------------------------------------------
| Generate Table of Contents from article headings
|--------------------------------------------------------------------------
*/

$articleContent = $blog['content'] ?? '';
$tableOfContents = [];

if (class_exists('DOMDocument') && trim($articleContent) !== '') {
    $previousLibxmlState = libxml_use_internal_errors(true);

    $dom = new DOMDocument('1.0', 'UTF-8');

    $dom->loadHTML(
        '<?xml encoding="UTF-8"><div id="blog-content-root">' .
        $articleContent .
        '</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );

    $xpath = new DOMXPath($dom);
    $root = $xpath->query('//*[@id="blog-content-root"]')->item(0);

    if ($root) {
        $headings = $xpath->query('.//h2 | .//h3', $root);
        $usedIds = [];

        foreach ($headings as $heading) {
            $headingText = trim($heading->textContent);

            if ($headingText === '') {
                continue;
            }

            $headingSlug = strtolower($headingText);
            $headingSlug = preg_replace('/[^a-z0-9]+/', '-', $headingSlug);
            $headingSlug = trim($headingSlug, '-');

            if ($headingSlug === '') {
                $headingSlug = 'section';
            }

            $headingId = $headingSlug;
            $counter = 2;

            while (isset($usedIds[$headingId])) {
                $headingId = $headingSlug . '-' . $counter;
                $counter++;
            }

            $usedIds[$headingId] = true;
            $heading->setAttribute('id', $headingId);

            $tableOfContents[] = [
                'title' => $headingText,
                'id' => $headingId,
                'level' => strtolower($heading->nodeName)
            ];
        }

        $articleContent = '';

        foreach ($root->childNodes as $child) {
            $articleContent .= $dom->saveHTML($child);
        }
    }

    libxml_clear_errors();
    libxml_use_internal_errors($previousLibxmlState);
}

/*
|--------------------------------------------------------------------------
| Tags
|--------------------------------------------------------------------------
*/

$blogTags = [];

if (!empty($blog['tags'])) {
    $blogTags = array_filter(
        array_map('trim', explode(',', $blog['tags']))
    );
}

/*
|--------------------------------------------------------------------------
| Related articles
|--------------------------------------------------------------------------
*/

$relatedBlogs = [];

try {
    $relatedStmt = $pdo->prepare("
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
          AND id != ?
        ORDER BY
            CASE WHEN category = ? THEN 0 ELSE 1 END,
            published_at DESC,
            id DESC
        LIMIT 3
    ");

    $relatedStmt->execute([
        $blog['id'],
        $blog['category']
    ]);

    $relatedBlogs = $relatedStmt->fetchAll();

} catch (PDOException $e) {
    error_log('Related blogs query error: ' . $e->getMessage());
}

/*
|--------------------------------------------------------------------------
| Breadcrumb
|--------------------------------------------------------------------------
*/

$breadcrumbItems = [
    [
        'label' => 'Blogs',
        'url' => $baseUrl . '/blogs.php'
    ],
    [
        'label' => $blog['title'],
        'url' => null
    ]
];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= blog_detail_e($pageTitle) ?> | Real Estate Blog</title>

    <meta name="description" content="<?= blog_detail_e($metaDescription) ?>">

    <link rel="canonical" href="<?= blog_detail_e($articleUrl) ?>">

    <meta property="og:type" content="article">
    <meta property="og:title" content="<?= blog_detail_e($blog['title']) ?>">
    <meta property="og:description" content="<?= blog_detail_e($metaDescription) ?>">
    <meta property="og:url" content="<?= blog_detail_e($articleUrl) ?>">
    <meta property="og:image" content="<?= blog_detail_e($imageUrl) ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= $baseUrl ?>/css/style.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/header.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/footer.css">
</head>

<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main>

    <!-- Breadcrumb -->
    <?php
        $pageTitle = 'Blog Details';
        include __DIR__ . '/../includes/breadcrumb.php';
    ?>

    <!-- Article Header -->
    <section class="blog-detail-header section section-white">
        <div class="container">

            <div class="blog-detail-header-inner">

                <a href="<?= $baseUrl ?>/blogs.php" class="blog-back-link">
                    <i class="ri-arrow-left-line"></i> Back to Blogs
                </a>

                <span class="blog-detail-category">
                    <?= blog_detail_e($blog['category']) ?>
                </span>

                <h1><?= blog_detail_e($blog['title']) ?></h1>

                <?php if (!empty($blog['intro'])): ?>
                    <p class="blog-detail-intro">
                        <?= blog_detail_e($blog['intro']) ?>
                    </p>
                <?php elseif (!empty($blog['excerpt'])): ?>
                    <p class="blog-detail-intro">
                        <?= blog_detail_e($blog['excerpt']) ?>
                    </p>
                <?php endif; ?>

                <div class="blog-detail-meta">

                    <div class="blog-author-info">
                        <span class="blog-author-avatar">
                            <i class="ri-user-3-line"></i>
                        </span>

                        <div>
                            <strong><?= blog_detail_e($authorName) ?></strong>
                            <span><?= blog_detail_e($authorRole) ?></span>
                        </div>
                    </div>

                    <span class="blog-meta-item">
                        <i class="ri-calendar-line"></i>
                        <?= blog_detail_e($formattedDate) ?>
                    </span>

                    <span class="blog-meta-item">
                        <i class="ri-time-line"></i>
                        <?= $readingTime ?> min read
                    </span>

                </div>

            </div>

        </div>
    </section>

    <!-- Featured Article Image -->
    <section class="blog-detail-cover-section">
        <div class="container">

            <div class="blog-detail-cover">
                <img
                    src="<?= blog_detail_e($imageUrl) ?>"
                    alt="<?= blog_detail_e($imageAlt) ?>"
                    loading="eager">
                <span class="blog-cover-caption">
                    <?= blog_detail_e($blog['title']) ?>
                </span>

            </div>

        </div>
    </section>

    <!-- Article Content and Sidebar -->
    <section class="blog-detail-body section section-white">
        <div class="container">

            <div class="blog-detail-layout">

                <!-- Main Article -->
                <article class="blog-article">

                    <div class="blog-article-content">
                        <?php if (!empty($blog['intro'])): ?>
                            <p class="blog-article-lead">
                                <?= blog_detail_e($blog['intro']) ?>
                            </p>
                        <?php endif; ?>

                        <!-- Table of Contents -->
                        <?php if (!empty($tableOfContents)): ?>

                            <div class="blog-table-of-contents">
                                <h2>
                                    <i class="ri-list-check-3"></i>
                                    In This Article
                                </h2>
                                <ol>
                                    <?php foreach ($tableOfContents as $item): ?>
                                        <li>
                                            <a href="#<?= blog_detail_e($item['id']) ?>">
                                                <?= blog_detail_e($item['title']) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>

                        <?php endif; ?>

                        <!-- Dynamic Article Content -->
                        <div class="blog-dynamic-content">
                            <?= $articleContent ?>
                        </div>
                        <!-- Article Tags -->
                        <?php if (!empty($blogTags)): ?>
                            <div class="blog-article-tags">
                                <strong>Tags:</strong>
                                <?php foreach ($blogTags as $tag): ?>
                                    <a href="<?= $baseUrl ?>/blogs.php?tag=<?= urlencode($tag) ?>">
                                        <?= blog_detail_e($tag) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Share Article -->
                        <div class="blog-article-share">
                            <strong>Share this article</strong>
                            <div class="blog-share-links">
                                <a
                                    href="https://www.facebook.com/sharer/sharer.php?u=<?= $encodedArticleUrl ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="Share on Facebook">
                                    <i class="ri-facebook-fill"></i>
                                </a>
                                <a
                                    href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $encodedArticleUrl ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="Share on LinkedIn">
                                    <i class="ri-linkedin-fill"></i>
                                </a>

                                <a
                                    href="https://wa.me/?text=<?= $encodedArticleTitle ?>%20<?= $encodedArticleUrl ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="Share on WhatsApp">
                                    <i class="ri-whatsapp-line"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Author Box -->
                        <div class="blog-author-box">
                            <span class="blog-author-avatar blog-author-avatar-large">
                                <i class="ri-user-3-line"></i>
                            </span>
                            <div>
                                <span class="blog-author-label">
                                    ABOUT THE AUTHOR
                                </span>
                                <h3>
                                    <?= blog_detail_e($authorName) ?>
                                </h3>
                                <p>
                                    Sharing practical property guides, home-buying
                                    information and real estate insights to help
                                    readers make informed decisions.
                                </p>
                            </div>
                        </div>
                    </div>

                </article>

                <!-- Sidebar -->
                <aside class="blog-detail-sidebar">

                    <!-- Contents -->
                    <?php if (!empty($tableOfContents)): ?>
                        <div class="blog-sidebar-card">
                            <h3>
                                <i class="ri-book-open-line"></i>
                                Article Contents
                            </h3>
                            <nav class="blog-sidebar-nav" aria-label="Article contents">
                                <?php foreach ($tableOfContents as $item): ?>
                                    <a href="#<?= blog_detail_e($item['id']) ?>">
                                        <?= blog_detail_e($item['title']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </nav>
                        </div>

                    <?php endif; ?>

                    <!-- Property CTA -->
                    <div class="blog-sidebar-cta">
                        <span class="blog-sidebar-cta-icon">
                            <i class="ri-home-4-line"></i>
                        </span>
                        <h3>Looking for Your Dream Home?</h3>
                        <p>
                            Explore available properties and find options
                            that match your requirements.
                        </p>
                        <a href="<?= $baseUrl ?>/properties.php" class="btn btn-primary">
                            Explore Properties
                            <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>

                    <!-- Contact -->
                    <div class="blog-sidebar-card blog-sidebar-contact">
                        <span class="blog-contact-icon">
                            <i class="ri-customer-service-2-line"></i>
                        </span>
                        <h3>Need Property Guidance?</h3>
                        <p>
                            Get in touch with our team for help exploring property options.
                        </p>
                        <a href="<?= $baseUrl ?>/contact.php">
                            Contact Our Team
                            <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- Related Articles -->
    <?php if (!empty($relatedBlogs)): ?>
        <section class="blog-related-section section section-light">
            <div class="container"> 
                <div class="section-heading"> 
                    <span class="section-subtitle">KEEP EXPLORING</span> 
                    <h2 class="section-title">Related Articles</h2> 
                    <p class="section-description">
                        Discover more insights to help you navigate the property market.
                    </p>
                </div>
                <div class="blog-related-grid">
                    <?php foreach ($relatedBlogs as $related): ?>
                        <?php
                            $relatedImage = !empty($related['featured_image'])
                                ? (
                                    preg_match('/^https?:\/\//i', $related['featured_image'])
                                        ? $related['featured_image']
                                        : $baseUrl . '/' . ltrim($related['featured_image'], '/')
                                )
                                : 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=85';

                            $relatedUrl = $baseUrl . '/blog/blog-details.php?slug=' .
                                rawurlencode($related['slug']);
                        ?>

                        <article class="blog-related-card"> 
                            <a href="<?= blog_detail_e($relatedUrl) ?>" class="blog-related-image">
                                <img
                                    src="<?= blog_detail_e($relatedImage) ?>"
                                    alt="<?= blog_detail_e($related['image_alt'] ?: $related['title']) ?>"
                                    loading="lazy"> 
                            </a>

                            <div class="blog-related-content">

                                <span>
                                    <?= blog_detail_e($related['category']) ?>
                                </span>

                                <h3>
                                    <a href="<?= blog_detail_e($relatedUrl) ?>">
                                        <?= blog_detail_e($related['title']) ?>
                                    </a>
                                </h3>

                                <a href="<?= blog_detail_e($relatedUrl) ?>" class="blog-read-more">
                                    Read Article
                                    <i class="ri-arrow-right-line"></i>
                                </a>

                            </div>

                        </article>
                    <?php endforeach; ?>
                </div>
                <div class="blog-related-action">
                    <a href="<?= $baseUrl ?>/blogs.php" class="btn btn-outline-primary">
                        View All Articles
                        <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
