
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$adminPageTitle = 'Team Management';
$baseUrl = rtrim(APP_URL, '/');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function team_admin_e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function team_admin_slug($value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
    return trim($value, '-');
}

function team_admin_upload(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Image upload failed. Please try again.');
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Image size must be less than 5 MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG and WEBP images are allowed.');
    }

    $uploadDirectory = dirname(__DIR__, 2) . '/uploads/team/';

    if (!is_dir($uploadDirectory) &&
        !mkdir($uploadDirectory, 0755, true) &&
        !is_dir($uploadDirectory)) {
        throw new RuntimeException('Unable to create team upload directory.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $destination = $uploadDirectory . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Unable to save uploaded image.');
    }

    return '/uploads/team/' . $filename;
}

function team_admin_delete_image(?string $image): void
{
    if (!$image || !str_starts_with($image, '/uploads/team/')) {
        return;
    }

    $projectRoot = dirname(__DIR__, 2);
    $imagePath = $projectRoot . $image;

    if (is_file($imagePath)) {
        unlink($imagePath);
    }
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $errorMessage = 'Invalid request. Please refresh the page and try again.';
    } else {
        $action = $_POST['action'] ?? '';

        try {
            if ($action === 'save') {
                $id = (int) ($_POST['id'] ?? 0);

                $name = trim($_POST['name'] ?? '');
                $slug = team_admin_slug($_POST['slug'] ?? '');
                $designation = trim($_POST['designation'] ?? '');
                $shortDescription = trim($_POST['short_description'] ?? '');

                $experience = trim($_POST['experience'] ?? '');
                $projects = trim($_POST['projects'] ?? '');
                $specialization = trim($_POST['specialization'] ?? '');
                $location = trim($_POST['location'] ?? '');

                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['phone'] ?? '');

                $linkedin = trim($_POST['linkedin'] ?? '');
                $instagram = trim($_POST['instagram'] ?? '');
                $facebook = trim($_POST['facebook'] ?? '');

                $intro = trim($_POST['intro'] ?? '');
                $bio = trim($_POST['bio'] ?? '');
                $skills = trim($_POST['skills'] ?? '');
                $achievements = trim($_POST['achievements'] ?? '');

                $sortOrder = (int) ($_POST['sort_order'] ?? 0);
                $isActive = (int) ($_POST['is_active'] ?? 1);

                if ($name === '' || $designation === '') {
                    throw new RuntimeException('Name and designation are required.');
                }

                if ($slug === '') {
                    $slug = team_admin_slug($name);
                }

                if ($slug === '') {
                    throw new RuntimeException('Please enter a valid member name or slug.');
                }

                if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new RuntimeException('Please enter a valid email address.');
                }

                if (!in_array($isActive, [0, 1], true)) {
                    $isActive = 1;
                }

                // Check duplicate slug.
                $slugQuery = $pdo->prepare("
                    SELECT id FROM team_members
                    WHERE slug = ? AND id != ?
                    LIMIT 1
                ");
                $slugQuery->execute([$slug, $id]);

                if ($slugQuery->fetch()) {
                    throw new RuntimeException('This profile URL already exists. Please use another slug.');
                }

                // Get existing member when editing.
                $existingMember = null;

                if ($id > 0) {
                    $existingQuery = $pdo->prepare("
                        SELECT * FROM team_members WHERE id = ? LIMIT 1
                    ");
                    $existingQuery->execute([$id]);
                    $existingMember = $existingQuery->fetch();

                    if (!$existingMember) {
                        throw new RuntimeException('Team member not found.');
                    }
                }

                $newImage = team_admin_upload($_FILES['image'] ?? []);
                $image = $newImage ?: ($existingMember['image'] ?? null);

                $data = [
                    $name,
                    $slug,
                    $designation,
                    $shortDescription,
                    $image,
                    $experience,
                    $projects,
                    $specialization,
                    $location,
                    $email,
                    $phone,
                    $linkedin,
                    $instagram,
                    $facebook,
                    $intro,
                    $bio,
                    $skills,
                    $achievements,
                    $sortOrder,
                    $isActive
                ];

                if ($id > 0) {
                    $stmt = $pdo->prepare("
                        UPDATE team_members SET
                            name = ?,
                            slug = ?,
                            designation = ?,
                            short_description = ?,
                            image = ?,
                            experience = ?,
                            projects = ?,
                            specialization = ?,
                            location = ?,
                            email = ?,
                            phone = ?,
                            linkedin = ?,
                            instagram = ?,
                            facebook = ?,
                            intro = ?,
                            bio = ?,
                            skills = ?,
                            achievements = ?,
                            sort_order = ?,
                            is_active = ?
                        WHERE id = ?
                    ");

                    $data[] = $id;
                    $stmt->execute($data);

                    if ($newImage && !empty($existingMember['image'])) {
                        team_admin_delete_image($existingMember['image']);
                    }

                    $successMessage = 'Team member updated successfully.';
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO team_members (
                            name, slug, designation, short_description, image,
                            experience, projects, specialization, location,
                            email, phone, linkedin, instagram, facebook,
                            intro, bio, skills, achievements,
                            sort_order, is_active
                        ) VALUES (
                            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                        )
                    ");

                    $stmt->execute($data);
                    $successMessage = 'Team member added successfully.';
                }
            } elseif ($action === 'delete') {
                $id = (int) ($_POST['id'] ?? 0);

                $stmt = $pdo->prepare("
                    SELECT image FROM team_members WHERE id = ? LIMIT 1
                ");
                $stmt->execute([$id]);
                $member = $stmt->fetch();

                if (!$member) {
                    throw new RuntimeException('Team member not found.');
                }

                $deleteStmt = $pdo->prepare("
                    DELETE FROM team_members WHERE id = ?
                ");
                $deleteStmt->execute([$id]);

                team_admin_delete_image($member['image']);

                $successMessage = 'Team member deleted successfully.';
            }

        } catch (Throwable $e) {
            error_log('Team management error: ' . $e->getMessage());
            $errorMessage = $e instanceof RuntimeException
                ? $e->getMessage()
                : 'Something went wrong. Please check the database and try again.';
        }
    }
}

// Fetch all team members.
$teamMembers = [];

try {
    $stmt = $pdo->query("
        SELECT *
        FROM team_members
        ORDER BY sort_order ASC, id DESC
    ");
    $teamMembers = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Team listing error: ' . $e->getMessage());
    $errorMessage = $errorMessage ?: 'Unable to load team members.';
}

$totalMembers = count($teamMembers);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management | EstateAdmin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= team_admin_e($baseUrl) ?>/css/style.css">
    <link rel="stylesheet" href="<?= team_admin_e($baseUrl) ?>/admin/css/admin.css">
</head>

<body class="admin-body">

<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="admin-main">

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="admin-content">

        <div class="admin-welcome">
            <div>
                <h2>Team Management</h2>
                <p>Manage your real estate professionals and their profile information.</p>
            </div>

            <button type="button" class="btn btn-primary"
                data-bs-toggle="modal" data-bs-target="#teamModal"
                onclick="openAddTeamModal()">
                <i class="ri-add-line me-1"></i> Add Team Member
            </button>
        </div>

        <?php if ($successMessage): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= team_admin_e($successMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= team_admin_e($errorMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="admin-panel">

            <div class="admin-panel-heading">
                <div>
                    <h5>All Team Members</h5>
                    <p>Manage team profiles displayed on your website.</p>
                </div>

                <span class="text-muted">
                    Total Members: <strong><?= number_format($totalMembers) ?></strong>
                </span>
            </div>

            <div class="table-responsive">
                <table class="table admin-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Designation</th>
                            <th>Experience</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if ($teamMembers): ?>

                        <?php foreach ($teamMembers as $member): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if (!empty($member['image'])): ?>
                                            <img
                                                src="<?= team_admin_e($baseUrl . $member['image']) ?>"
                                                alt="<?= team_admin_e($member['name']) ?>"
                                                width="48"
                                                height="48"
                                                class="rounded"
                                                style="object-fit:cover;"
                                            >
                                        <?php else: ?>
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                                style="width:48px;height:48px;">
                                                <i class="ri-user-line fs-4 text-muted"></i>
                                            </div>
                                        <?php endif; ?>

                                        <div>
                                            <strong><?= team_admin_e($member['name']) ?></strong>
                                            <div class="small text-muted">
                                                <?= team_admin_e($member['slug']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td><?= team_admin_e($member['designation']) ?></td>
                                <td><?= team_admin_e($member['experience']) ?: '—' ?></td>
                                <td><?= (int) $member['sort_order'] ?></td>

                                <td>
                                    <?php if ((int) $member['is_active'] === 1): ?>
                                        <span class="admin-status completed">Active</span>
                                    <?php else: ?>
                                        <span class="admin-status pending">Inactive</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">

                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#teamModal"
                                            onclick='openEditTeamModal(<?= json_encode($member, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)'>
                                            <i class="ri-edit-line"></i>
                                        </button>

                                        <form method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this team member?');">
                                            <input type="hidden" name="csrf_token"
                                                value="<?= team_admin_e($_SESSION['csrf_token']) ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id"
                                                value="<?= (int) $member['id'] ?>">

                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="ri-team-line fs-1 text-muted"></i>
                                <h6 class="mt-2">No team members found</h6>
                                <p class="text-muted mb-3">Add your first team member to get started.</p>
                                <button type="button" class="btn btn-primary"
                                    data-bs-toggle="modal" data-bs-target="#teamModal"
                                    onclick="openAddTeamModal()">
                                    <i class="ri-add-line me-1"></i> Add Team Member
                                </button>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

</div>


<!-- Add / Edit Team Member Modal -->
<div class="modal fade" id="teamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <form method="POST" enctype="multipart/form-data" id="teamForm">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="teamModalTitle">Add Team Member</h5>
                        <p class="text-muted small mb-0">Enter team member and profile details.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="csrf_token"
                        value="<?= team_admin_e($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" id="teamId" value="0">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" id="teamName" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Designation *</label>
                            <input type="text" name="designation" id="teamDesignation" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Profile Slug</label>
                            <input type="text" name="slug" id="teamSlug" class="form-control"
                                placeholder="rahul-sharma">
                            <small class="text-muted">Used in the profile URL. Generated from name if empty.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Short Description</label>
                            <input type="text" name="short_description" id="teamShortDescription" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Profile Image</label>
                            <input type="file" name="image" id="teamImage" class="form-control"
                                accept=".jpg,.jpeg,.png,.webp">
                            <small class="text-muted">JPG, PNG or WEBP. Maximum 5 MB.</small>
                            <div id="teamCurrentImage" class="mt-2"></div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Experience</label>
                            <input type="text" name="experience" id="teamExperience"
                                class="form-control" placeholder="12+ Years">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Successful Transactions</label>
                            <input type="text" name="projects" id="teamProjects"
                                class="form-control" placeholder="250+">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Specialization</label>
                            <input type="text" name="specialization" id="teamSpecialization" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" id="teamLocation" class="form-control"
                                placeholder="New Delhi, India">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" id="teamEmail" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" id="teamPhone" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">LinkedIn URL</label>
                            <input type="url" name="linkedin" id="teamLinkedin" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Instagram URL</label>
                            <input type="url" name="instagram" id="teamInstagram" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Facebook URL</label>
                            <input type="url" name="facebook" id="teamFacebook" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Profile Introduction</label>
                            <textarea name="intro" id="teamIntro" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Biography</label>
                            <textarea name="bio" id="teamBio" class="form-control" rows="5"
                                placeholder="Write one paragraph per line."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Skills & Specializations</label>
                            <textarea name="skills" id="teamSkills" class="form-control" rows="5"
                                placeholder="One skill per line."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Achievements</label>
                            <textarea name="achievements" id="teamAchievements" class="form-control" rows="5"
                                placeholder="One achievement per line."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="sort_order" id="teamSortOrder"
                                class="form-control" value="0">
                            <small class="text-muted">Lower numbers appear first.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="is_active" id="teamStatus" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>
                        <span id="teamSubmitText">Save Team Member</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= team_admin_e($baseUrl) ?>/admin/js/admin.js"></script>

<script>
const teamBaseUrl = <?= json_encode($baseUrl, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

function setTeamValue(id, value) {
    const element = document.getElementById(id);
    if (element) element.value = value ?? '';
}

function openAddTeamModal() {
    document.getElementById('teamForm').reset();

    setTeamValue('teamId', '0');
    setTeamValue('teamSortOrder', '0');
    setTeamValue('teamStatus', '1');

    document.getElementById('teamModalTitle').textContent = 'Add Team Member';
    document.getElementById('teamSubmitText').textContent = 'Save Team Member';
    document.getElementById('teamCurrentImage').innerHTML = '';
}

function openEditTeamModal(member) {
    document.getElementById('teamForm').reset();

    setTeamValue('teamId', member.id);
    setTeamValue('teamName', member.name);
    setTeamValue('teamDesignation', member.designation);
    setTeamValue('teamSlug', member.slug);
    setTeamValue('teamShortDescription', member.short_description);
    setTeamValue('teamExperience', member.experience);
    setTeamValue('teamProjects', member.projects);
    setTeamValue('teamSpecialization', member.specialization);
    setTeamValue('teamLocation', member.location);
    setTeamValue('teamEmail', member.email);
    setTeamValue('teamPhone', member.phone);
    setTeamValue('teamLinkedin', member.linkedin);
    setTeamValue('teamInstagram', member.instagram);
    setTeamValue('teamFacebook', member.facebook);
    setTeamValue('teamIntro', member.intro);
    setTeamValue('teamBio', member.bio);
    setTeamValue('teamSkills', member.skills);
    setTeamValue('teamAchievements', member.achievements);
    setTeamValue('teamSortOrder', member.sort_order);
    setTeamValue('teamStatus', member.is_active);

    document.getElementById('teamModalTitle').textContent = 'Edit Team Member';
    document.getElementById('teamSubmitText').textContent = 'Update Team Member';

    const imageContainer = document.getElementById('teamCurrentImage');

    if (member.image) {
        const imageUrl = teamBaseUrl + member.image;
        imageContainer.innerHTML = '';

        const image = document.createElement('img');
        image.src = imageUrl;
        image.alt = 'Current team image';
        image.width = 75;
        image.height = 75;
        image.style.objectFit = 'cover';
        image.style.borderRadius = '8px';

        imageContainer.appendChild(image);
    } else {
        imageContainer.innerHTML = '';
    }
}

// Generate a slug automatically from the name when creating a profile.
document.getElementById('teamName').addEventListener('input', function () {
    const slugInput = document.getElementById('teamSlug');

    if (document.getElementById('teamId').value === '0') {
        slugInput.value = this.value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
    }
});
</script>

</body>
</html>
