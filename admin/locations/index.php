<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$adminPageTitle = 'Locations Management';

$projectRoot = dirname(__DIR__, 2);
$uploadDirectory = $projectRoot . '/uploads/locations';
$uploadRelativeDirectory = 'uploads/locations';

function location_e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function deleteLocationImage(string $imagePath, string $projectRoot): void
{
    $imagePath = str_replace('\\', '/', $imagePath);

    // Only delete files inside the locations upload folder.
    if (!str_starts_with($imagePath, 'uploads/locations/')) {
        return;
    }

    $fullPath = $projectRoot . '/' . $imagePath;

    if (is_file($fullPath)) {
        unlink($fullPath);
    }
}

function redirectLocations(): void
{
    header('Location: ' . APP_URL . '/admin/locations/index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Handle Add, Edit and Delete
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($csrfToken) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $csrfToken)
    ) {
        $_SESSION['location_flash'] = [
            'type' => 'error',
            'message' => 'Invalid request. Please refresh the page and try again.'
        ];

        redirectLocations();
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Location
    |--------------------------------------------------------------------------
    */

    if ($action === 'delete') {

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            $_SESSION['location_flash'] = [
                'type' => 'error',
                'message' => 'Invalid location selected.'
            ];

            redirectLocations();
        }

        try {
            $stmt = $pdo->prepare(
                'SELECT image FROM locations WHERE id = :id LIMIT 1'
            );
            $stmt->execute(['id' => $id]);

            $location = $stmt->fetch();

            if (!$location) {
                $_SESSION['location_flash'] = [
                    'type' => 'error',
                    'message' => 'Location not found.'
                ];

                redirectLocations();
            }

            $deleteStmt = $pdo->prepare(
                'DELETE FROM locations WHERE id = :id'
            );
            $deleteStmt->execute(['id' => $id]);

            if (!empty($location['image'])) {
                deleteLocationImage($location['image'], $projectRoot);
            }

            $_SESSION['location_flash'] = [
                'type' => 'success',
                'message' => 'Location deleted successfully.'
            ];

        } catch (PDOException $e) {
            error_log('Location delete error: ' . $e->getMessage());

            $_SESSION['location_flash'] = [
                'type' => 'error',
                'message' => 'Unable to delete location. Please try again.'
            ];
        }

        redirectLocations();
    }


    /*
    |--------------------------------------------------------------------------
    | Add / Edit Location
    |--------------------------------------------------------------------------
    */

    if ($action === 'save') {

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        $name = trim((string) ($_POST['name'] ?? ''));
        $district = trim((string) ($_POST['district'] ?? ''));
        $state = trim((string) ($_POST['state'] ?? ''));
        $pincode = trim((string) ($_POST['pincode'] ?? ''));

        $errors = [];

        // Preserve text fields if validation fails.
        $_SESSION['location_old'] = [
            'id' => $id,
            'name' => $name,
            'district' => $district,
            'state' => $state,
            'pincode' => $pincode
        ];

        $_SESSION['location_open_modal'] = true;

        if ($name === '' || mb_strlen($name) > 150) {
            $errors[] = 'Location name is required and must be within 150 characters.';
        }

        if ($district === '' || mb_strlen($district) > 150) {
            $errors[] = 'District is required and must be within 150 characters.';
        }

        if ($state === '' || mb_strlen($state) > 150) {
            $errors[] = 'State is required and must be within 150 characters.';
        }

        if (!preg_match('/^[0-9]{6}$/', $pincode)) {
            $errors[] = 'Please enter a valid 6-digit pincode.';
        }

        // Check whether the location exists during edit.
        $currentImage = '';

        if ($id > 0) {
            $existingStmt = $pdo->prepare(
                'SELECT image FROM locations WHERE id = :id LIMIT 1'
            );
            $existingStmt->execute(['id' => $id]);

            $existingLocation = $existingStmt->fetch();

            if (!$existingLocation) {
                $errors[] = 'Location not found.';
            } else {
                $currentImage = (string) ($existingLocation['image'] ?? '');
            }
        }

        // Duplicate check.
        if (empty($errors)) {

            $duplicateStmt = $pdo->prepare(
                'SELECT id
                 FROM locations
                 WHERE name = :name
                   AND district = :district
                   AND state = :state
                   AND pincode = :pincode
                   AND id != :id
                 LIMIT 1'
            );

            $duplicateStmt->execute([
                'name' => $name,
                'district' => $district,
                'state' => $state,
                'pincode' => $pincode,
                'id' => $id
            ]);

            if ($duplicateStmt->fetch()) {
                $errors[] = 'This location already exists.';
            }
        }

        // Validate uploaded image before moving it.
        $uploadedImage = null;

        $hasImageUpload = isset($_FILES['image'])
            && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE;

        if ($hasImageUpload && empty($errors)) {

            $file = $_FILES['image'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Image upload failed. Please try again.';

            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $errors[] = 'Image size must not exceed 5 MB.';

            } else {

                $allowedTypes = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($file['tmp_name']);

                $imageInfo = @getimagesize($file['tmp_name']);

                if (
                    !isset($allowedTypes[$mimeType]) ||
                    $imageInfo === false
                ) {
                    $errors[] = 'Only valid JPG, PNG and WEBP images are allowed.';
                }
            }
        }

        // Image is required when adding a new location.
        if ($id === 0 && !$hasImageUpload) {
            $errors[] = 'Please upload a location image.';
        }

        if (!empty($errors)) {

            $_SESSION['location_flash'] = [
                'type' => 'error',
                'message' => implode(' ', $errors)
            ];

            redirectLocations();
        }

        /*
        |--------------------------------------------------------------------------
        | Move Uploaded Image
        |--------------------------------------------------------------------------
        */

        if ($hasImageUpload) {

            if (
                !is_dir($uploadDirectory) &&
                !mkdir($uploadDirectory, 0755, true) &&
                !is_dir($uploadDirectory)
            ) {
                $_SESSION['location_flash'] = [
                    'type' => 'error',
                    'message' => 'Unable to create image upload directory.'
                ];

                redirectLocations();
            }

            $extension = $allowedTypes[$mimeType];
            $fileName = bin2hex(random_bytes(16)) . '.' . $extension;

            $destination = $uploadDirectory . '/' . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {

                $_SESSION['location_flash'] = [
                    'type' => 'error',
                    'message' => 'Unable to save location image.'
                ];

                redirectLocations();
            }

            $uploadedImage = $uploadRelativeDirectory . '/' . $fileName;
        }

        $image = $uploadedImage ?? $currentImage;

        /*
        |--------------------------------------------------------------------------
        | Save Location
        |--------------------------------------------------------------------------
        */

        try {

            if ($id > 0) {

                $stmt = $pdo->prepare(
                    'UPDATE locations
                     SET name = :name,
                         district = :district,
                         state = :state,
                         pincode = :pincode,
                         image = :image
                     WHERE id = :id'
                );

                $stmt->execute([
                    'name' => $name,
                    'district' => $district,
                    'state' => $state,
                    'pincode' => $pincode,
                    'image' => $image,
                    'id' => $id
                ]);

                $successMessage = 'Location updated successfully.';

            } else {

                $stmt = $pdo->prepare(
                    'INSERT INTO locations
                        (name, district, state, pincode, image)
                     VALUES
                        (:name, :district, :state, :pincode, :image)'
                );

                $stmt->execute([
                    'name' => $name,
                    'district' => $district,
                    'state' => $state,
                    'pincode' => $pincode,
                    'image' => $image
                ]);

                $successMessage = 'Location added successfully.';
            }

            // Delete previous image only after successful database save.
            if (
                $uploadedImage !== null &&
                $currentImage !== '' &&
                $currentImage !== $uploadedImage
            ) {
                deleteLocationImage($currentImage, $projectRoot);
            }

            unset(
                $_SESSION['location_old'],
                $_SESSION['location_open_modal']
            );

            $_SESSION['location_flash'] = [
                'type' => 'success',
                'message' => $successMessage
            ];

        } catch (PDOException $e) {

            // Remove newly uploaded file if database save fails.
            if ($uploadedImage !== null) {
                deleteLocationImage($uploadedImage, $projectRoot);
            }

            error_log('Location save error: ' . $e->getMessage());

            $_SESSION['location_flash'] = [
                'type' => 'error',
                'message' => 'Unable to save location. Please try again.'
            ];
        }

        redirectLocations();
    }
}


/*
|--------------------------------------------------------------------------
| Flash Messages and Old Form Data
|--------------------------------------------------------------------------
*/

$flash = $_SESSION['location_flash'] ?? null;
unset($_SESSION['location_flash']);

$old = $_SESSION['location_old'] ?? [];
$openModal = !empty($_SESSION['location_open_modal']);

unset(
    $_SESSION['location_old'],
    $_SESSION['location_open_modal']
);

$csrfToken = $_SESSION['csrf_token'];


/*
|--------------------------------------------------------------------------
| Search and State Filter
|--------------------------------------------------------------------------
*/

$search = isset($_GET['search']) && is_string($_GET['search'])
    ? trim($_GET['search'])
    : '';

$selectedState = isset($_GET['state']) && is_string($_GET['state'])
    ? trim($_GET['state'])
    : '';

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

$perPage = 10;
$offset = ($page - 1) * $perPage;


// Get states for dropdown.
$statesStmt = $pdo->query(
    'SELECT DISTINCT state
     FROM locations
     WHERE state <> ""
     ORDER BY state ASC'
);

$states = $statesStmt->fetchAll(PDO::FETCH_COLUMN);


// Build search conditions.
$where = [];
$params = [];

if ($search !== '') {

    $where[] = '(
        name LIKE :search_name
        OR district LIKE :search_district
        OR state LIKE :search_state
        OR pincode LIKE :search_pincode
    )';

    $searchValue = '%' . $search . '%';

    $params['search_name'] = $searchValue;
    $params['search_district'] = $searchValue;
    $params['search_state'] = $searchValue;
    $params['search_pincode'] = $searchValue;
}

if ($selectedState !== '') {
    $where[] = 'state = :selected_state';
    $params['selected_state'] = $selectedState;
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';


// Total rows.
$countStmt = $pdo->prepare(
    "SELECT COUNT(*) FROM locations {$whereSql}"
);

$countStmt->execute($params);

$totalLocations = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalLocations / $perPage));

if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}


// Fetch locations.
$listStmt = $pdo->prepare(
    "SELECT id, name, district, state, pincode, image, created_at
     FROM locations
     {$whereSql}
     ORDER BY id DESC
     LIMIT :limit OFFSET :offset"
);

foreach ($params as $key => $value) {
    $listStmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
}

$listStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$listStmt->execute();

$locations = $listStmt->fetchAll();

$adminBaseUrl = rtrim(APP_URL, '/') . '/admin/locations/index.php';
$siteBaseUrl = rtrim(APP_URL, '/');
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= location_e($adminPageTitle) ?></title>

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

            <!-- Page Welcome -->
            <div class="admin-welcome">
                <div>
                    <h2>Manage Locations</h2>
                    <p>Add, update and manage property locations.</p>
                </div>

                <button type="button"
                    class="btn btn-primary"
                    id="addLocationBtn"
                    data-bs-toggle="modal"
                    data-bs-target="#locationModal">
                    <i class="ri-add-line"></i>
                    Add Location
                </button>
            </div>

            <!-- Search and Filter -->
            <section class="admin-panel mb-4 p-4">

                <!-- <div class="admin-panel-heading">
                    <div>
                        <h2>Search Locations</h2>
                        <p>Find locations by name, district, state or pincode.</p>
                    </div>
                </div> -->

                <form method="GET" action="<?= location_e($adminBaseUrl) ?>">
                    <div class="row g-3 align-items-end">

                        <div class="col-12 col-md-6"> 
                            <input
                                type="search"
                                class="form-control"
                                id="locationSearch"
                                name="search"
                                value="<?= location_e($search) ?>"
                                placeholder="Search location, district or pincode">
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="stateFilter" class="form-label">
                                Select State
                            </label>
                            <select class="form-select" name="state" id="stateFilter">
                                <option value="">All States</option>

                                <?php foreach ($states as $state): ?>
                                    <option
                                        value="<?= location_e($state) ?>"
                                        <?= $selectedState === $state ? 'selected' : '' ?>>
                                        <?= location_e($state) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="col-12 col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="ri-search-line"></i>
                                Search
                            </button>

                            <a href="<?= location_e($adminBaseUrl) ?>"
                                class="btn btn-outline-primary">
                                <i class="ri-refresh-line"></i>
                            </a>
                        </div>

                    </div>
                </form>

            </section>

            <!-- Locations Table -->
            <section class="admin-panel">

                <div class="admin-panel-heading">
                    <div>
                        <h2>All Locations</h2>
                        <p>
                            <?= number_format($totalLocations) ?>
                            locations available.
                        </p>
                    </div>
                </div>

                <div class="table-responsive">

                    <table class="table admin-table align-middle mb-0">

                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Location</th>
                                <th>District</th>
                                <th>State</th>
                                <th>Pincode</th>
                                <th>Date Added</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (!empty($locations)): ?>

                                <?php foreach ($locations as $location): ?>

                                    <?php
                                    $imageUrl = !empty($location['image'])
                                        ? $siteBaseUrl . '/' . ltrim($location['image'], '/')
                                        : '';
                                    ?>

                                    <tr>

                                        <td>
                                            <?php if ($imageUrl): ?>
                                                <img
                                                    src="<?= location_e($imageUrl) ?>"
                                                    alt="<?= location_e($location['name']) ?>"
                                                    width="55"
                                                    height="45"
                                                    style="object-fit:cover;border-radius:8px;">
                                            <?php else: ?>
                                                <i class="ri-image-line fs-3 text-muted"></i>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <strong><?= location_e($location['name']) ?></strong>
                                        </td>

                                        <td><?= location_e($location['district']) ?></td>

                                        <td><?= location_e($location['state']) ?></td>

                                        <td><?= location_e($location['pincode']) ?></td>

                                        <td>
                                            <?= !empty($location['created_at'])
                                                ? date('d M Y', strtotime($location['created_at']))
                                                : '-' ?>
                                        </td>

                                        <td>
                                            <div class="d-flex align-items-center gap-2">

                                                <button
                                                    type="button"
                                                    class="admin-action editLocationBtn"
                                                    data-id="<?= (int) $location['id'] ?>"
                                                    data-name="<?= location_e($location['name']) ?>"
                                                    data-district="<?= location_e($location['district']) ?>"
                                                    data-state="<?= location_e($location['state']) ?>"
                                                    data-pincode="<?= location_e($location['pincode']) ?>"
                                                    data-image="<?= location_e($location['image'] ?? '') ?>"
                                                    data-image-url="<?= location_e($imageUrl) ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#locationModal"
                                                    aria-label="Edit location">
                                                    <i class="ri-edit-line"></i>
                                                </button>

                                                <button
                                                    type="button"
                                                    class="admin-action deleteLocationBtn"
                                                    data-id="<?= (int) $location['id'] ?>"
                                                    data-name="<?= location_e($location['name']) ?>"
                                                    aria-label="Delete location">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>

                                            </div>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="ri-map-pin-line fs-1 text-muted"></i>
                                        <h6 class="mt-2">No Locations Found</h6>
                                        <p class="text-muted mb-0">
                                            Add a new location or change your filters.
                                        </p>
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>

                    <nav class="mt-4">
                        <ul class="pagination justify-content-end mb-0">

                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link"
                                    href="?<?= location_e(http_build_query([
                                        'search' => $search,
                                        'state' => $selectedState,
                                        'page' => max(1, $page - 1)
                                    ])) ?>">
                                    Previous
                                </a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                                <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="?<?= location_e(http_build_query([
                                            'search' => $search,
                                            'state' => $selectedState,
                                            'page' => $i
                                        ])) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>

                            <?php endfor; ?>

                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link"
                                    href="?<?= location_e(http_build_query([
                                        'search' => $search,
                                        'state' => $selectedState,
                                        'page' => min($totalPages, $page + 1)
                                    ])) ?>">
                                    Next
                                </a>
                            </li>

                        </ul>
                    </nav>

                <?php endif; ?>

            </section>

        </main>


        <?php include __DIR__ . '/../includes/footer.php'; ?>

    </div>


    <!-- Add / Edit Location Modal -->
    <div class="modal fade" id="locationModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form
                    method="POST"
                    action="<?= location_e($adminBaseUrl) ?>"
                    enctype="multipart/form-data"
                    id="locationForm">

                    <input type="hidden" name="csrf_token" value="<?= location_e($csrfToken) ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" id="locationId" value="0">

                    <div class="modal-header">

                        <h5 class="modal-title" id="locationModalTitle">
                            Add Location
                        </h5>

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        </button>

                    </div>


                    <div class="modal-body">

                        <div class="mb-3">

                            <label for="locationName" class="form-label">
                                Location Name <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="locationName"
                                name="name"
                                maxlength="150"
                                placeholder="Enter city or location name"
                                required>

                        </div>


                        <div class="mb-3">

                            <label for="locationDistrict" class="form-label">
                                District <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="locationDistrict"
                                name="district"
                                maxlength="150"
                                placeholder="Enter district"
                                required>

                        </div>


                        <div class="mb-3">

                            <label for="locationState" class="form-label">
                                State <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="locationState"
                                name="state"
                                maxlength="150"
                                placeholder="Enter state"
                                required>

                        </div>


                        <div class="mb-3">

                            <label for="locationPincode" class="form-label">
                                Pincode <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="locationPincode"
                                name="pincode"
                                inputmode="numeric"
                                pattern="[0-9]{6}"
                                maxlength="6"
                                placeholder="Enter 6-digit pincode"
                                required>

                        </div>


                        <div class="mb-3">

                            <label for="locationImage" class="form-label">
                                Location Image <span class="text-danger" id="imageRequiredMark">*</span>
                            </label>

                            <input
                                type="file"
                                class="form-control"
                                id="locationImage"
                                name="image"
                                accept="image/jpeg,image/png,image/webp"
                                required>

                            <small class="text-muted">
                                JPG, PNG or WEBP. Maximum size: 5 MB.
                            </small>

                            <div id="locationImagePreview" class="mt-3 d-none">

                                <img
                                    id="previewImage"
                                    src=""
                                    alt="Location preview"
                                    style="width:120px;height:85px;object-fit:cover;border-radius:10px;">

                            </div>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">

                            Cancel

                        </button>

                        <button type="submit"
                            class="btn btn-primary"
                            id="saveLocationBtn">

                            <i class="ri-save-line me-1"></i>
                            Save Location

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    <!-- Delete Form -->
    <form method="POST"
        action="<?= location_e($adminBaseUrl) ?>"
        id="deleteLocationForm"
        class="d-none">

        <input type="hidden" name="csrf_token" value="<?= location_e($csrfToken) ?>">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteLocationId">

    </form>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="../js/admin.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const locationModal = document.getElementById('locationModal');
            const locationForm = document.getElementById('locationForm');

            const locationId = document.getElementById('locationId');
            const locationName = document.getElementById('locationName');
            const locationDistrict = document.getElementById('locationDistrict');
            const locationState = document.getElementById('locationState');
            const locationPincode = document.getElementById('locationPincode');

            const locationImage = document.getElementById('locationImage');
            const imagePreview = document.getElementById('locationImagePreview');
            const previewImage = document.getElementById('previewImage');
            const imageRequiredMark = document.getElementById('imageRequiredMark');

            const modalTitle = document.getElementById('locationModalTitle');
            const saveButton = document.getElementById('saveLocationBtn');

            const addLocationBtn = document.getElementById('addLocationBtn');


            function resetLocationForm() {

                locationForm.reset();

                locationId.value = 0;

                modalTitle.textContent = 'Add Location';
                saveButton.innerHTML = '<i class="ri-save-line me-1"></i> Save Location';

                locationImage.required = true;
                imageRequiredMark.classList.remove('d-none');

                imagePreview.classList.add('d-none');
                previewImage.src = '';
            }


            // Add button.
            addLocationBtn.addEventListener('click', function () {
                resetLocationForm();
            });


            // Edit button.
            document.querySelectorAll('.editLocationBtn').forEach(function (button) {

                button.addEventListener('click', function () {

                    resetLocationForm();

                    locationId.value = this.dataset.id;
                    locationName.value = this.dataset.name;
                    locationDistrict.value = this.dataset.district;
                    locationState.value = this.dataset.state;
                    locationPincode.value = this.dataset.pincode;

                    modalTitle.textContent = 'Edit Location';
                    saveButton.innerHTML = '<i class="ri-save-line me-1"></i> Update Location';

                    // Image is optional while editing.
                    locationImage.required = false;
                    imageRequiredMark.classList.add('d-none');

                    if (this.dataset.imageUrl) {
                        previewImage.src = this.dataset.imageUrl;
                        imagePreview.classList.remove('d-none');
                    }

                });

            });


            // New image preview.
            locationImage.addEventListener('change', function () {

                const file = this.files[0];

                if (!file) {
                    return;
                }

                const allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];

                if (!allowedTypes.includes(file.type)) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Image',
                        text: 'Please select a JPG, PNG or WEBP image.'
                    });

                    this.value = '';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Image Too Large',
                        text: 'Image size must not exceed 5 MB.'
                    });

                    this.value = '';
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (event) {
                    previewImage.src = event.target.result;
                    imagePreview.classList.remove('d-none');
                };

                reader.readAsDataURL(file);

            });


            // Clear modal on close.
            locationModal.addEventListener('hidden.bs.modal', function () {
                resetLocationForm();
            });


            // Delete location confirmation.
            document.querySelectorAll('.deleteLocationBtn').forEach(function (button) {

                button.addEventListener('click', function () {

                    const id = this.dataset.id;
                    const name = this.dataset.name;

                    Swal.fire({
                        title: 'Delete Location?',
                        text: 'Are you sure you want to delete "' + name + '"?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Delete',
                        cancelButtonText: 'Cancel'
                    }).then(function (result) {

                        if (result.isConfirmed) {

                            document.getElementById('deleteLocationId').value = id;

                            document.getElementById('deleteLocationForm').submit();

                        }

                    });

                });

            });


            // Show PHP flash messages.
            const flash = <?= json_encode(
                $flash,
                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
            ) ?>;

            if (flash && flash.message) {

                Swal.fire({
                    icon: flash.type === 'success' ? 'success' : 'error',
                    title: flash.type === 'success' ? 'Success' : 'Error',
                    text: flash.message,
                    confirmButtonColor: '#149A83'
                });

            }


            // Reopen modal if server-side validation failed.
            const oldData = <?= json_encode(
                $old,
                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
            ) ?>;

            const openModal = <?= $openModal ? 'true' : 'false' ?>;

            if (openModal) {

                locationId.value = oldData.id || 0;
                locationName.value = oldData.name || '';
                locationDistrict.value = oldData.district || '';
                locationState.value = oldData.state || '';
                locationPincode.value = oldData.pincode || '';

                if (Number(oldData.id) > 0) {

                    modalTitle.textContent = 'Edit Location';
                    saveButton.innerHTML = '<i class="ri-save-line me-1"></i> Update Location';

                    locationImage.required = false;
                    imageRequiredMark.classList.add('d-none');

                } else {
                    modalTitle.textContent = 'Add Location';
                    locationImage.required = true;
                    imageRequiredMark.classList.remove('d-none');
                }
                bootstrap.Modal.getOrCreateInstance(locationModal).show();

            }
        });
    </script>

</body>
</html>