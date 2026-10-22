
<?php
$pageTitle = $pageTitle ?? 'Explore Properties';

$breadcrumbItems = $breadcrumbItems ?? [
    ['label' => $pageTitle, 'url' => null]
];

$breadcrumbImage = $breadcrumbImage
    ?? 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=2000&q=85';

$breadcrumbImage = htmlspecialchars(
    $breadcrumbImage,
    ENT_QUOTES,
    'UTF-8'
);
?>

<!-- Reusable Page Breadcrumb -->
<section
    class="page-breadcrumb"
    style="--breadcrumb-image: url('<?= $breadcrumbImage ?>');"
    aria-label="Page banner"
>
    <div class="container">
        <div class="breadcrumb-content">

            <span class="breadcrumb-eyebrow">
                <i class="ri-building-2-line"></i>
                YOUR TRUSTED REAL ESTATE PARTNER
            </span>

            <h1><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
            <nav class="breadcrumb-nav" aria-label="Breadcrumb">
                <a href="/">
                    <i class="ri-home-4-line"></i>
                    Home
                </a>

                <?php foreach ($breadcrumbItems as $index => $item): ?>
                    <i class="ri-arrow-right-s-line breadcrumb-separator"
                        aria-hidden="true"></i>

                    <?php if (
                        !empty($item['url'])
                        && $index !== array_key_last($breadcrumbItems)
                    ): ?>
                        <a href="<?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php else: ?>
                        <span class="breadcrumb-current" aria-current="page">
                            <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    <?php endif; ?>
                <?php endforeach; ?>

            </nav>

        </div>
    </div>
</section>
