
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'User Reviews';

/* ---------------------------------
   Helper Function
--------------------------------- */
function review_e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/* ---------------------------------
   CSRF Token
--------------------------------- */
if (empty($_SESSION['review_csrf_token'])) {
    $_SESSION['review_csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['review_csrf_token'];
$flashMessage = '';
$flashType = 'success';

/* ---------------------------------
   Handle Add / Edit / Delete
--------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submittedToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals($csrfToken, $submittedToken)) {
        $flashMessage = 'Invalid request. Please refresh the page and try again.';
        $flashType = 'danger';

    } else {

        $action = $_POST['action'] ?? '';

        try {

            /* Add Review */
            if ($action === 'add' || $action === 'edit') {

                $customerName = trim($_POST['customer_name'] ?? '');
                $location = trim($_POST['location'] ?? '');
                $review = trim($_POST['review'] ?? '');
                $rating = (int)($_POST['rating'] ?? 5);
                $sortOrder = (int)($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;

                if ($customerName === '' || $location === '' || $review === '') {
                    throw new RuntimeException('Please fill in all required fields.');
                }

                if ($rating < 1 || $rating > 5) {
                    throw new RuntimeException('Rating must be between 1 and 5.');
                }

                if ($action === 'add') {

                    $stmt = $pdo->prepare("
                        INSERT INTO user_reviews
                        (customer_name, location, review, rating, is_active, sort_order)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");

                    $stmt->execute([
                        $customerName,
                        $location,
                        $review,
                        $rating,
                        $isActive,
                        $sortOrder
                    ]);

                    $flashMessage = 'Testimonial added successfully.';

                } else {

                    $reviewId = (int)($_POST['review_id'] ?? 0);

                    if ($reviewId <= 0) {
                        throw new RuntimeException('Invalid testimonial ID.');
                    }

                    $stmt = $pdo->prepare("
                        UPDATE user_reviews
                        SET customer_name = ?,
                            location = ?,
                            review = ?,
                            rating = ?,
                            is_active = ?,
                            sort_order = ?
                        WHERE id = ?
                    ");

                    $stmt->execute([
                        $customerName,
                        $location,
                        $review,
                        $rating,
                        $isActive,
                        $sortOrder,
                        $reviewId
                    ]);

                    $flashMessage = 'Testimonial updated successfully.';
                }

            /* Delete Review */
            } elseif ($action === 'delete') {

                $reviewId = (int)($_POST['review_id'] ?? 0);

                if ($reviewId <= 0) {
                    throw new RuntimeException('Invalid testimonial ID.');
                }

                $stmt = $pdo->prepare("
                    DELETE FROM user_reviews WHERE id = ?
                ");

                $stmt->execute([$reviewId]);

                $flashMessage = 'Testimonial deleted successfully.';

            } else {
                throw new RuntimeException('Invalid action.');
            }

        } catch (Throwable $e) {
            $flashMessage = $e->getMessage();
            $flashType = 'danger';
        }
    }

    $_SESSION['review_flash'] = [
        'message' => $flashMessage,
        'type' => $flashType
    ];

    header('Location: ' . APP_URL . '/admin/user-review/index.php');
    exit;
}

/* ---------------------------------
   Flash Message
--------------------------------- */
if (!empty($_SESSION['review_flash'])) {
    $flashMessage = $_SESSION['review_flash']['message'];
    $flashType = $_SESSION['review_flash']['type'];
    unset($_SESSION['review_flash']);
}

/* ---------------------------------
   Fetch Reviews
--------------------------------- */
$stmt = $pdo->query("
    SELECT *
    FROM user_reviews
    ORDER BY sort_order ASC, id DESC
");

$reviews = $stmt->fetchAll();

$adminPageTitle = 'User Reviews';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>User Reviews | EstateAdmin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body class="admin-body">

    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="admin-main">

        <?php include __DIR__ . '/../includes/header.php'; ?>

        <main class="admin-content">

            <!-- Page Heading -->
            <div class="admin-welcome">
                <div>
                    <h2>User Reviews</h2>
                    <p>Manage customer testimonials displayed on your website.</p>
                </div>

                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReviewModal">
                    <i class="ri-add-line me-1"></i> Add Testimonial
                </button>
            </div>

            <!-- Flash Message -->
            <?php if ($flashMessage !== ''): ?>
                <div class="alert alert-<?= review_e($flashType) ?> alert-dismissible fade show" role="alert">
                    <?= review_e($flashMessage) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Reviews Table -->
            <section class="admin-panel">

                <div class="admin-panel-heading">
                    <div>
                        <h2>All Testimonials</h2>
                        <p>Customer feedback and experiences.</p>
                    </div>
                </div>

                <div class="table-responsive">

                    <table class="table admin-table align-middle mb-0">

                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Review</th>
                                <th>Rating</th>
                                <th>Display Order</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (!empty($reviews)): ?>

                                <?php foreach ($reviews as $item): ?>

                                    <tr>

                                        <td>
                                            <strong><?= review_e($item['customer_name']) ?></strong>
                                            <small class="d-block text-muted">
                                                <i class="ri-map-pin-line"></i>
                                                <?= review_e($item['location']) ?>
                                            </small>
                                        </td>

                                        <td>
                                            <?= review_e(mb_strimwidth($item['review'], 0, 90, '...')) ?>
                                        </td>

                                        <td>
                                            <span class="text-warning">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="<?= $i <= (int)$item['rating'] ? 'ri-star-fill' : 'ri-star-line' ?>"></i>
                                                <?php endfor; ?>
                                            </span>
                                        </td>

                                        <td><?= (int)$item['sort_order'] ?></td>

                                        <td>
                                            <?php if ((int)$item['is_active'] === 1): ?>
                                                <span class="admin-status completed">Active</span>
                                            <?php else: ?>
                                                <span class="admin-status pending">Inactive</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-end">

                                            <button
                                                type="button"
                                                class="admin-action"
                                                title="Edit"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editReviewModal"
                                                data-id="<?= (int)$item['id'] ?>"
                                                data-name="<?= review_e($item['customer_name']) ?>"
                                                data-location="<?= review_e($item['location']) ?>"
                                                data-review="<?= review_e($item['review']) ?>"
                                                data-rating="<?= (int)$item['rating'] ?>"
                                                data-order="<?= (int)$item['sort_order'] ?>"
                                                data-active="<?= (int)$item['is_active'] ?>">
                                                <i class="ri-edit-line"></i>
                                            </button>

                                            <button
                                                type="button"
                                                class="admin-action text-danger"
                                                title="Delete"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteReviewModal"
                                                data-id="<?= (int)$item['id'] ?>"
                                                data-name="<?= review_e($item['customer_name']) ?>">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        No testimonials found.
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>

        </main>

    </div>

    <!-- Add Review Modal -->
    <div class="modal fade" id="addReviewModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">

                <form method="POST" action="">

                    <input type="hidden" name="csrf_token" value="<?= review_e($csrfToken) ?>">
                    <input type="hidden" name="action" value="add">

                    <div class="modal-header">
                        <h5 class="modal-title">Add Testimonial</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Customer Name *</label>
                                <input type="text" name="customer_name" class="form-control" maxlength="150" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Location *</label>
                                <input type="text" name="location" class="form-control" maxlength="150" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Rating *</label>
                                <select name="rating" class="form-select" required>
                                    <option value="5" selected>5 Stars</option>
                                    <option value="4">4 Stars</option>
                                    <option value="3">3 Stars</option>
                                    <option value="2">2 Stars</option>
                                    <option value="1">1 Star</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Display Order</label>
                                <input type="number" name="sort_order" class="form-control" value="0">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Customer Review *</label>
                                <textarea name="review" class="form-control" rows="4" required></textarea>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="addReviewActive" checked>
                                    <label class="form-check-label" for="addReviewActive">Show on website</label>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Testimonial</button>
                    </div>

                </form>

            </div>

        </div>

    </div>

    <!-- Edit Review Modal -->
    <div class="modal fade" id="editReviewModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">

                <form method="POST" action="">

                    <input type="hidden" name="csrf_token" value="<?= review_e($csrfToken) ?>">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="review_id" id="editReviewId">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Testimonial</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Customer Name *</label>
                                <input type="text" name="customer_name" id="editCustomerName" class="form-control" maxlength="150" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Location *</label>
                                <input type="text" name="location" id="editLocation" class="form-control" maxlength="150" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Rating *</label>
                                <select name="rating" id="editRating" class="form-select" required>
                                    <option value="5">5 Stars</option>
                                    <option value="4">4 Stars</option>
                                    <option value="3">3 Stars</option>
                                    <option value="2">2 Stars</option>
                                    <option value="1">1 Star</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Display Order</label>
                                <input type="number" name="sort_order" id="editSortOrder" class="form-control">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Customer Review *</label>
                                <textarea name="review" id="editReviewText" class="form-control" rows="4" required></textarea>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="editReviewActive">
                                    <label class="form-check-label" for="editReviewActive">Show on website</label>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Testimonial</button>
                    </div>

                </form>

            </div>

        </div>

    </div>

    <!-- Delete Review Modal -->
    <div class="modal fade" id="deleteReviewModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form method="POST" action="">

                    <input type="hidden" name="csrf_token" value="<?= review_e($csrfToken) ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="review_id" id="deleteReviewId">

                    <div class="modal-header">
                        <h5 class="modal-title">Delete Testimonial</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        Are you sure you want to delete the testimonial of
                        <strong id="deleteCustomerName"></strong>?
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>

                </form>

            </div>

        </div>

    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/admin.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const editModal = document.getElementById('editReviewModal');

            editModal.addEventListener('show.bs.modal', function (event) {

                const button = event.relatedTarget;

                document.getElementById('editReviewId').value = button.dataset.id;
                document.getElementById('editCustomerName').value = button.dataset.name;
                document.getElementById('editLocation').value = button.dataset.location;
                document.getElementById('editReviewText').value = button.dataset.review;
                document.getElementById('editRating').value = button.dataset.rating;
                document.getElementById('editSortOrder').value = button.dataset.order;
                document.getElementById('editReviewActive').checked = button.dataset.active === '1';

            });

            const deleteModal = document.getElementById('deleteReviewModal');

            deleteModal.addEventListener('show.bs.modal', function (event) {

                const button = event.relatedTarget;

                document.getElementById('deleteReviewId').value = button.dataset.id;
                document.getElementById('deleteCustomerName').textContent = button.dataset.name;

            });

        });
    </script>

</body>
</html>
