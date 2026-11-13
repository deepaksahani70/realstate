
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$adminPageTitle = 'Property Management';

$projectRoot = dirname(__DIR__, 2);
$uploadDirectory = $projectRoot . '/uploads/properties';
$uploadRelative = 'uploads/properties';

if (!is_dir($uploadDirectory)) {
    mkdir($uploadDirectory, 0755, true);
}

function property_e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function property_redirect(): void
{
    header('Location: index.php');
    exit;
}

function property_delete_image(string $path): void
{
    $path = str_replace('\\', '/', $path);

    if (
        str_starts_with($path, 'uploads/properties/') &&
        !str_contains($path, '..')
    ) {
        $fullPath = dirname(__DIR__, 2) . '/' . $path;

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}

function property_make_slug(string $title): string
{
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');

    return $slug ?: 'property';
}

function property_upload_images(
    array $files,
    int $propertyId,
    PDO $pdo,
    string $uploadDirectory,
    string $uploadRelative
): void {
    if (
        empty($files['name']) ||
        !is_array($files['name'])
    ) {
        return;
    }

    $allowedMime = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    $check = $pdo->prepare(
        "SELECT COUNT(*) FROM property_images WHERE property_id = ?"
    );
    $check->execute([$propertyId]);

    $hasPrimary = (int) $check->fetchColumn() > 0;

    $finfo = new finfo(FILEINFO_MIME_TYPE);

    foreach ($files['name'] as $index => $originalName) {
        if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        if ($files['error'][$index] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('One or more images could not be uploaded.');
        }

        if ($files['size'][$index] > 5 * 1024 * 1024) {
            throw new RuntimeException('Each image must be under 5 MB.');
        }

        $tmpName = $files['tmp_name'][$index];

        $mime = $finfo->file($tmpName);

        if (!isset($allowedMime[$mime]) || !getimagesize($tmpName)) {
            throw new RuntimeException('Only JPG, PNG and WEBP images are allowed.');
        }

        $extension = $allowedMime[$mime];
        $fileName = bin2hex(random_bytes(16)) . '.' . $extension;

        $destination = $uploadDirectory . '/' . $fileName;

        if (!move_uploaded_file($tmpName, $destination)) {
            throw new RuntimeException('Unable to save uploaded image.');
        }

        $imagePath = $uploadRelative . '/' . $fileName;
        $isPrimary = $hasPrimary ? 0 : 1;

        $stmt = $pdo->prepare("
            INSERT INTO property_images
                (property_id, image_path, is_primary, sort_order)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $propertyId,
            $imagePath,
            $isPrimary,
            $index
        ]);

        $hasPrimary = true;
    }
}

function property_generate_unique_slug(PDO $pdo, string $title, int $excludeId = 0): string
{
    $base = property_make_slug($title);
    $slug = $base;

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM properties
        WHERE slug = ?
        AND id != ?
    ");

    $counter = 1;

    while (true) {
        $stmt->execute([$slug, $excludeId]);

        if ((int) $stmt->fetchColumn() === 0) {
            return $slug;
        }

        $slug = $base . '-' . $counter++;
    }
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (
            !hash_equals(
                $_SESSION['csrf_token'] ?? '',
                $_POST['csrf_token'] ?? ''
            )
        ) {
            throw new RuntimeException('Invalid security token. Refresh and try again.');
        }

        $action = $_POST['action'] ?? '';

        /*
         * DELETE PROPERTY
         */
        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);

            $stmt = $pdo->prepare("
                SELECT image_path
                FROM property_images
                WHERE property_id = ?
            ");
            $stmt->execute([$id]);
            $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $delete = $pdo->prepare("DELETE FROM properties WHERE id = ?");
            $delete->execute([$id]);

            foreach ($images as $image) {
                property_delete_image($image);
            }

            $_SESSION['property_flash'] = 'Property deleted successfully.';
            property_redirect();
        }

        /*
         * ACTIVE / INACTIVE
         */
        if ($action === 'toggle_active') {
            $id = (int) ($_POST['id'] ?? 0);

            $stmt = $pdo->prepare("
                UPDATE properties
                SET is_active = IF(is_active = 1, 0, 1)
                WHERE id = ?
            ");
            $stmt->execute([$id]);

            $_SESSION['property_flash'] = 'Property visibility updated.';
            property_redirect();
        }

        /*
         * FEATURED / NORMAL
         */
        if ($action === 'toggle_featured') {
            $id = (int) ($_POST['id'] ?? 0);

            $stmt = $pdo->prepare("
                UPDATE properties
                SET is_featured = IF(is_featured = 1, 0, 1)
                WHERE id = ?
            ");
            $stmt->execute([$id]);

            $_SESSION['property_flash'] = 'Featured status updated.';
            property_redirect();
        }

        /*
         * DELETE SELECTED GALLERY IMAGES
         */
        if ($action === 'delete_images') {
            $id = (int) ($_POST['id'] ?? 0);
            $selectedImages = $_POST['delete_images'] ?? [];

            if (!is_array($selectedImages)) {
                $selectedImages = [];
            }

            foreach ($selectedImages as $imageId) {
                $imageId = (int) $imageId;

                $stmt = $pdo->prepare("
                    SELECT image_path
                    FROM property_images
                    WHERE id = ? AND property_id = ?
                ");
                $stmt->execute([$imageId, $id]);
                $image = $stmt->fetchColumn();

                if ($image) {
                    $delete = $pdo->prepare("
                        DELETE FROM property_images
                        WHERE id = ? AND property_id = ?
                    ");
                    $delete->execute([$imageId, $id]);

                    property_delete_image($image);
                }
            }

            // Make first remaining image primary if no primary image exists.
            $stmt = $pdo->prepare("
                SELECT id
                FROM property_images
                WHERE property_id = ?
                ORDER BY sort_order ASC, id ASC
            ");
            $stmt->execute([$id]);
            $remaining = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if ($remaining) {
                $pdo->prepare("
                    UPDATE property_images
                    SET is_primary = 0
                    WHERE property_id = ?
                ")->execute([$id]);

                $pdo->prepare("
                    UPDATE property_images
                    SET is_primary = 1
                    WHERE id = ?
                ")->execute([(int) $remaining[0]]);
            }

            $_SESSION['property_flash'] = 'Selected images deleted.';
            property_redirect();
        }

        /*
         * SAVE PROPERTY
         */
        if ($action === 'save') {
            $id = (int) ($_POST['id'] ?? 0);

            $locationId = (int) ($_POST['location_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $propertyType = trim($_POST['property_type'] ?? '');
            $listingType = $_POST['listing_type'] ?? 'sale';
            $listingStatus = $_POST['listing_status'] ?? 'available';

            $price = trim($_POST['price'] ?? '');
            $priceLabel = trim($_POST['price_label'] ?? '');

            $bedrooms = max(0, (int) ($_POST['bedrooms'] ?? 0));
            $bathrooms = max(0, (int) ($_POST['bathrooms'] ?? 0));
            $area = trim($_POST['area'] ?? '');
            $areaUnit = trim($_POST['area_unit'] ?? 'sq. ft.');
            $parking = trim($_POST['parking'] ?? '');

            $description = trim($_POST['description'] ?? '');
            $address = trim($_POST['address'] ?? '');

            $amenities = $_POST['amenities'] ?? [];

            if (!is_array($amenities)) {
                $amenities = [];
            }

            $allowedAmenities = [
                'Modular Kitchen',
                'Balcony',
                'Reserved Parking',
                '24/7 Water Supply',
                'Power Backup',
                'Security',
                'Nearby Markets',
                'Family-Friendly Area',
                'Lift',
                'Swimming Pool',
                'Gym',
                'Garden',
                'Club House',
                'CCTV',
                'Fire Safety',
                'Visitor Parking'
            ];

            $amenities = array_values(
                array_intersect($allowedAmenities, $amenities)
            );

            if (!$locationId || $title === '' || $propertyType === '') {
                throw new RuntimeException('Please fill all required fields.');
            }

            if (!in_array($listingType, ['sale', 'rent'], true)) {
                throw new RuntimeException('Invalid listing type.');
            }

            if (!in_array($listingStatus, ['available', 'sold', 'rented'], true)) {
                throw new RuntimeException('Invalid property status.');
            }

            if ($price === '' || !is_numeric($price) || (float) $price < 0) {
                throw new RuntimeException('Enter a valid property price.');
            }

            if ($area !== '' && (!is_numeric($area) || (float) $area < 0)) {
                throw new RuntimeException('Enter a valid property area.');
            }

            $locationCheck = $pdo->prepare("
                SELECT id FROM locations WHERE id = ?
            ");
            $locationCheck->execute([$locationId]);

            if (!$locationCheck->fetchColumn()) {
                throw new RuntimeException('Selected location does not exist.');
            }

            $slug = property_generate_unique_slug($pdo, $title, $id);

            $pdo->beginTransaction();

            if ($id > 0) {
                $stmt = $pdo->prepare("
                    UPDATE properties SET
                        location_id = ?,
                        slug = ?,
                        title = ?,
                        property_type = ?,
                        listing_type = ?,
                        listing_status = ?,
                        price = ?,
                        price_label = ?,
                        bedrooms = ?,
                        bathrooms = ?,
                        area = ?,
                        area_unit = ?,
                        parking = ?,
                        description = ?,
                        address = ?,
                        amenities = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $locationId,
                    $slug,
                    $title,
                    $propertyType,
                    $listingType,
                    $listingStatus,
                    $price,
                    $priceLabel ?: null,
                    $bedrooms,
                    $bathrooms,
                    $area !== '' ? $area : null,
                    $areaUnit,
                    $parking,
                    $description,
                    $address,
                    json_encode($amenities, JSON_UNESCAPED_UNICODE),
                    $id
                ]);

                $propertyId = $id;
            } else {
                $propertyCode = 'PROP-' . strtoupper(bin2hex(random_bytes(4)));

                $stmt = $pdo->prepare("
                    INSERT INTO properties (
                        location_id,
                        property_code,
                        slug,
                        title,
                        property_type,
                        listing_type,
                        listing_status,
                        price,
                        price_label,
                        bedrooms,
                        bathrooms,
                        area,
                        area_unit,
                        parking,
                        description,
                        address,
                        amenities
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $stmt->execute([
                    $locationId,
                    $propertyCode,
                    $slug,
                    $title,
                    $propertyType,
                    $listingType,
                    $listingStatus,
                    $price,
                    $priceLabel ?: null,
                    $bedrooms,
                    $bathrooms,
                    $area !== '' ? $area : null,
                    $areaUnit,
                    $parking,
                    $description,
                    $address,
                    json_encode($amenities, JSON_UNESCAPED_UNICODE)
                ]);

                $propertyId = (int) $pdo->lastInsertId();
            }

            $pdo->commit();

            // Upload images after saving the property.
            property_upload_images(
                $_FILES['images'] ?? [],
                $propertyId,
                $pdo,
                $uploadDirectory,
                $uploadRelative
            );

            $_SESSION['property_flash'] = $id > 0
                ? 'Property updated successfully.'
                : 'Property added successfully.';

            property_redirect();
        }

    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $errorMessage = $e->getMessage();
    }
}

$successMessage = $_SESSION['property_flash'] ?? '';
unset($_SESSION['property_flash']);

/*
 * LOCATIONS
 */
$locations = $pdo->query("
    SELECT id, name, district, state
    FROM locations
    ORDER BY state, name
")->fetchAll();

/*
 * EDIT PROPERTY
 */
$editId = (int) ($_GET['edit'] ?? 0);
$editProperty = null;
$editImages = [];

if ($editId > 0) {
    $stmt = $pdo->prepare("
        SELECT *
        FROM properties
        WHERE id = ?
    ");
    $stmt->execute([$editId]);
    $editProperty = $stmt->fetch();

    if ($editProperty) {
        $stmt = $pdo->prepare("
            SELECT *
            FROM property_images
            WHERE property_id = ?
            ORDER BY is_primary DESC, sort_order ASC, id ASC
        ");
        $stmt->execute([$editId]);
        $editImages = $stmt->fetchAll();
    }
}

/*
 * FILTERS
 */
$search = trim($_GET['search'] ?? '');
$filterStatus = $_GET['status'] ?? '';
$filterVisibility = $_GET['visibility'] ?? '';

$where = [];
$params = [];

if ($search !== '') {
    $where[] = "(
        p.title LIKE ?
        OR p.property_code LIKE ?
        OR l.name LIKE ?
        OR l.district LIKE ?
    )";

    $term = '%' . $search . '%';

    array_push($params, $term, $term, $term, $term);
}

if (in_array($filterStatus, ['available', 'sold', 'rented'], true)) {
    $where[] = "p.listing_status = ?";
    $params[] = $filterStatus;
}

if ($filterVisibility !== '' && in_array($filterVisibility, ['0', '1'], true)) {
    $where[] = "p.is_active = ?";
    $params[] = (int) $filterVisibility;
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM properties p
    INNER JOIN locations l ON l.id = p.location_id
    $whereSql
");
$countStmt->execute($params);

$totalProperties = (int) $countStmt->fetchColumn();

$perPage = 10;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$totalPages = max(1, (int) ceil($totalProperties / $perPage));
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $perPage;

$listStmt = $pdo->prepare("
    SELECT
        p.*,
        l.name AS location_name,
        l.district,
        l.state,
        (
            SELECT image_path
            FROM property_images pi
            WHERE pi.property_id = p.id
            ORDER BY pi.is_primary DESC, pi.sort_order ASC, pi.id ASC
            LIMIT 1
        ) AS main_image
    FROM properties p
    INNER JOIN locations l ON l.id = p.location_id
    $whereSql
    ORDER BY p.created_at DESC
    LIMIT $perPage OFFSET $offset
");
$listStmt->execute($params);
$properties = $listStmt->fetchAll();

$amenityOptions = [
    'Modular Kitchen',
    'Balcony',
    'Reserved Parking',
    '24/7 Water Supply',
    'Power Backup',
    'Security',
    'Nearby Markets',
    'Family-Friendly Area',
    'Lift',
    'Swimming Pool',
    'Gym',
    'Garden',
    'Club House',
    'CCTV',
    'Fire Safety',
    'Visitor Parking'
];

$selectedAmenities = $editProperty
    ? json_decode($editProperty['amenities'] ?? '[]', true) ?: []
    : [];

$baseUrl = rtrim(APP_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties | EstateAdmin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin-body">

<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="admin-main">

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="admin-content">

        <!-- <div class="admin-welcome">
            <div>
                <h2>Property Management</h2>
                <p>Manage your property listings, images and visibility.</p>
            </div>

            <a href="index.php#property-form" class="btn btn-primary">
                <i class="ri-add-line"></i> Add Property
            </a>
        </div> -->

        <?php if ($successMessage): ?>
            <div class="alert alert-success">
                <?= property_e($successMessage) ?>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="alert alert-danger">
                <?= property_e($errorMessage) ?>
            </div>
        <?php endif; ?>

        <!-- PROPERTY FORM -->
        <section class="admin-panel mb-4" id="property-form">

            <div class="admin-panel-heading">
                <div>
                    <h2><?= $editProperty ? 'Edit Property' : 'Add New Property' ?></h2>
                    <p>Enter property information and upload images.</p>
                </div>

                <?php if ($editProperty): ?>
                    <a href="index.php" class="btn btn-outline-primary btn-sm">
                        Cancel Edit
                    </a>
                <?php endif; ?>
            </div>

            <form method="POST" enctype="multipart/form-data" class="p-3">
                <input type="hidden" name="csrf_token" value="<?= property_e($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?= (int) ($editProperty['id'] ?? 0) ?>">

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">Property Title *</label>
                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            maxlength="255"
                            value="<?= property_e($editProperty['title'] ?? '') ?>"
                            placeholder="Luxury Family Home"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Location *</label>
                        <select name="location_id" class="form-select" required>
                            <option value="">Select Location</option>

                            <?php foreach ($locations as $location): ?>
                                <option
                                    value="<?= (int) $location['id'] ?>"
                                    <?= (int) ($editProperty['location_id'] ?? 0) === (int) $location['id'] ? 'selected' : '' ?>
                                >
                                    <?= property_e($location['name'] . ' (' . $location['district'] . ', ' . $location['state'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Property Type *</label>
                        <select name="property_type" class="form-select" required>
                            <?php
                            $types = [
                                'Apartment',
                                'Flat',
                                'Independent House',
                                'Villa',
                                'Builder Floor',
                                'Plot',
                                'Commercial',
                                'Office Space',
                                'Shop',
                                'Land'
                            ];
                            ?>

                            <option value="">Select Type</option>

                            <?php foreach ($types as $type): ?>
                                <option
                                    value="<?= property_e($type) ?>"
                                    <?= ($editProperty['property_type'] ?? '') === $type ? 'selected' : '' ?>
                                >
                                    <?= property_e($type) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Listing Type *</label>
                        <select name="listing_type" class="form-select" required>
                            <option value="sale" <?= ($editProperty['listing_type'] ?? 'sale') === 'sale' ? 'selected' : '' ?>>
                                For Sale
                            </option>
                            <option value="rent" <?= ($editProperty['listing_type'] ?? '') === 'rent' ? 'selected' : '' ?>>
                                For Rent
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Listing Status *</label>
                        <select name="listing_status" class="form-select" required>
                            <option value="available" <?= ($editProperty['listing_status'] ?? 'available') === 'available' ? 'selected' : '' ?>>
                                Available
                            </option>
                            <option value="sold" <?= ($editProperty['listing_status'] ?? '') === 'sold' ? 'selected' : '' ?>>
                                Sold
                            </option>
                            <option value="rented" <?= ($editProperty['listing_status'] ?? '') === 'rented' ? 'selected' : '' ?>>
                                Rented
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Price *</label>
                        <input
                            type="number"
                            name="price"
                            class="form-control"
                            min="0"
                            step="0.01"
                            value="<?= property_e($editProperty['price'] ?? '') ?>"
                            placeholder="18500000"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Price Display Label</label>
                        <input
                            type="text"
                            name="price_label"
                            class="form-control"
                            value="<?= property_e($editProperty['price_label'] ?? '') ?>"
                            placeholder="₹1.85 Cr onwards"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Bedrooms</label>
                        <input
                            type="number"
                            name="bedrooms"
                            class="form-control"
                            min="0"
                            value="<?= property_e($editProperty['bedrooms'] ?? 0) ?>"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Bathrooms</label>
                        <input
                            type="number"
                            name="bathrooms"
                            class="form-control"
                            min="0"
                            value="<?= property_e($editProperty['bathrooms'] ?? 0) ?>"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Built-up Area</label>
                        <input
                            type="number"
                            name="area"
                            class="form-control"
                            min="0"
                            step="0.01"
                            value="<?= property_e($editProperty['area'] ?? '') ?>"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Area Unit</label>
                        <select name="area_unit" class="form-select">
                            <?php foreach (['sq. ft.', 'sq. yd.', 'sq. m.', 'acres'] as $unit): ?>
                                <option
                                    value="<?= property_e($unit) ?>"
                                    <?= ($editProperty['area_unit'] ?? 'sq. ft.') === $unit ? 'selected' : '' ?>
                                >
                                    <?= property_e($unit) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Parking</label>
                        <input
                            type="text"
                            name="parking"
                            class="form-control"
                            value="<?= property_e($editProperty['parking'] ?? '') ?>"
                            placeholder="2 Cars"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Full Address</label>
                        <textarea
                            name="address"
                            class="form-control"
                            rows="2"
                            placeholder="Enter complete property address"
                        ><?= property_e($editProperty['address'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Property Description</label>
                        <textarea
                            name="description"
                            class="form-control"
                            rows="5"
                            placeholder="Enter property description..."
                        ><?= property_e($editProperty['description'] ?? '') ?></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Features & Amenities</label>

                        <div class="row g-2">
                            <?php foreach ($amenityOptions as $amenity): ?>
                                <div class="col-md-3 col-6">
                                    <label class="form-check">
                                        <input
                                            type="checkbox"
                                            name="amenities[]"
                                            class="form-check-input"
                                            value="<?= property_e($amenity) ?>"
                                            <?= in_array($amenity, $selectedAmenities, true) ? 'checked' : '' ?>
                                        >
                                        <span class="form-check-label">
                                            <?= property_e($amenity) ?>
                                        </span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Property Images</label>
                        <input
                            type="file"
                            name="images[]"
                            class="form-control"
                            accept="image/jpeg,image/png,image/webp"
                            multiple
                        >
                        <small class="text-muted">
                            JPG, PNG, WEBP. Maximum 5 MB per image.
                            The first uploaded image will be the main image if no image exists.
                        </small>
                    </div>

                    <?php if ($editImages): ?>
                        <div class="col-12">
                            <label class="form-label">Existing Images</label>

                            <div class="row g-3">
                                <?php foreach ($editImages as $image): ?>
                                    <div class="col-md-2 col-6">
                                        <div class="border rounded p-2">
                                            <img
                                                src="<?= property_e($baseUrl . '/' . $image['image_path']) ?>"
                                                alt="Property image"
                                                class="img-fluid rounded mb-2"
                                                style="width:100%;height:110px;object-fit:cover;"
                                            >

                                            <?php if ($image['is_primary']): ?>
                                                <span class="badge bg-success mb-2">Main Image</span>
                                            <?php endif; ?>

                                            <label class="form-check small">
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input"
                                                    name="delete_images[]"
                                                    value="<?= (int) $image['id'] ?>"
                                                >
                                                Delete Image
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <button
                                type="submit"
                                form="deleteImagesForm"
                                class="btn btn-outline-danger btn-sm mt-3"
                            >
                                Delete Selected Images
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line"></i>
                            <?= $editProperty ? 'Update Property' : 'Save Property' ?>
                        </button>

                        <?php if ($editProperty): ?>
                            <a href="index.php" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            </form>

            <?php if ($editImages): ?>
                <form method="POST" id="deleteImagesForm">
                    <input type="hidden" name="csrf_token" value="<?= property_e($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="action" value="delete_images">
                    <input type="hidden" name="id" value="<?= (int) $editProperty['id'] ?>">
                    <?php foreach ($editImages as $image): ?>
                        <input type="hidden" name="image_ids[]" value="<?= (int) $image['id'] ?>">
                    <?php endforeach; ?>
                </form>
            <?php endif; ?>

        </section>

        <!-- PROPERTY LIST -->
        <section class="admin-panel">

            <div class="admin-panel-heading">
                <div>
                    <h2>All Properties</h2>
                    <p>Total <?= $totalProperties ?> properties found.</p>
                </div>
            </div>

            <form method="GET" class="row g-2 p-3">
                <div class="col-md-5">
                    <input
                        type="search"
                        name="search"
                        class="form-control"
                        placeholder="Search title, property ID or location..."
                        value="<?= property_e($search) ?>"
                    >
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Listing Status</option>
                        <option value="available" <?= $filterStatus === 'available' ? 'selected' : '' ?>>Available</option>
                        <option value="sold" <?= $filterStatus === 'sold' ? 'selected' : '' ?>>Sold</option>
                        <option value="rented" <?= $filterStatus === 'rented' ? 'selected' : '' ?>>Rented</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="visibility" class="form-select">
                        <option value="">All Visibility</option>
                        <option value="1" <?= $filterVisibility === '1' ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= $filterVisibility === '0' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="index.php" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table admin-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Listing</th>
                            <th>Status</th>
                            <th>Visibility</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (!$properties): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                No properties found.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($properties as $property): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if ($property['main_image']): ?>
                                        <img
                                            src="<?= property_e($baseUrl . '/' . $property['main_image']) ?>"
                                            alt=""
                                            style="width:55px;height:48px;object-fit:cover;border-radius:8px;"
                                        >
                                    <?php endif; ?>

                                    <div>
                                        <strong><?= property_e($property['title']) ?></strong>
                                        <small class="d-block text-muted">
                                            <?= property_e($property['property_code']) ?>
                                        </small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <?= property_e($property['location_name']) ?>
                                <small class="d-block text-muted">
                                    <?= property_e($property['state']) ?>
                                </small>
                            </td>

                            <td>
                                ₹<?= number_format((float) $property['price']) ?>
                            </td>

                            <td>
                                <?= $property['listing_type'] === 'rent' ? 'For Rent' : 'For Sale' ?>
                            </td>

                            <td>
                                <?= ucfirst(property_e($property['listing_status'])) ?>
                            </td>

                            <td>
                                <span class="badge <?= $property['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $property['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>

                            <td>
                                <span class="badge <?= $property['is_featured'] ? 'bg-warning text-dark' : 'bg-light text-dark' ?>">
                                    <?= $property['is_featured'] ? 'Featured' : 'Normal' ?>
                                </span>
                            </td>

                            <td>
                                <div class="d-flex flex-wrap gap-1">

                                    <a
                                        href="index.php?edit=<?= (int) $property['id'] ?>#property-form"
                                        class="admin-action"
                                        title="Edit"
                                    >
                                        <i class="ri-edit-line"></i>
                                    </a>

                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= property_e($_SESSION['csrf_token']) ?>">
                                        <input type="hidden" name="action" value="toggle_active">
                                        <input type="hidden" name="id" value="<?= (int) $property['id'] ?>">

                                        <button
                                            type="submit"
                                            class="admin-action"
                                            title="<?= $property['is_active'] ? 'Disable' : 'Enable' ?>"
                                        >
                                            <i class="<?= $property['is_active'] ? 'ri-eye-off-line' : 'ri-eye-line' ?>"></i>
                                        </button>
                                    </form>

                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= property_e($_SESSION['csrf_token']) ?>">
                                        <input type="hidden" name="action" value="toggle_featured">
                                        <input type="hidden" name="id" value="<?= (int) $property['id'] ?>">

                                        <button type="submit" class="admin-action" title="Toggle Featured">
                                            <i class="<?= $property['is_featured'] ? 'ri-star-fill' : 'ri-star-line' ?>"></i>
                                        </button>
                                    </form>

                                    <form
                                        method="POST"
                                        class="d-inline delete-property-form"
                                    >
                                        <input type="hidden" name="csrf_token" value="<?= property_e($_SESSION['csrf_token']) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $property['id'] ?>">

                                        <button type="submit" class="admin-action text-danger" title="Delete">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="p-3 d-flex justify-content-end gap-2">

                    <?php
                    $queryParams = $_GET;
                    unset($queryParams['page']);
                    $queryString = http_build_query($queryParams);
                    ?>

                    <?php if ($currentPage > 1): ?>
                        <a
                            class="btn btn-outline-primary btn-sm"
                            href="?<?= $queryString ?>&page=<?= $currentPage - 1 ?>"
                        >
                            Previous
                        </a>
                    <?php endif; ?>

                    <span class="align-self-center">
                        Page <?= $currentPage ?> of <?= $totalPages ?>
                    </span>

                    <?php if ($currentPage < $totalPages): ?>
                        <a
                            class="btn btn-outline-primary btn-sm"
                            href="?<?= $queryString ?>&page=<?= $currentPage + 1 ?>"
                        >
                            Next
                        </a>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

        </section>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../js/admin.js"></script>

<script>
document.querySelectorAll('.delete-property-form').forEach(form => {
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        Swal.fire({
            title: 'Delete property?',
            text: 'This property and its gallery images will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33'
        }).then(result => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

</body>
</html>
