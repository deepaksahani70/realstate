
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
$adminPageTitle = 'Blogs';

/* ---------------------------------------
   Helper Functions
--------------------------------------- */

function blog_e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function blog_slug($value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
    return trim($value, '-');
}

/* ---------------------------------------
   Sanitize Rich Text HTML
--------------------------------------- */

function blog_clean_html(string $html): string
{
    if (!class_exists('DOMDocument')) {
        throw new RuntimeException('PHP DOM extension is required for the rich text editor.');
    }

    $allowedTags = [
        'p', 'h2', 'h3', 'h4',
        'ul', 'ol', 'li',
        'strong', 'b', 'em', 'i', 'u',
        'a', 'blockquote', 'br', 'hr'
    ];

    $dom = new DOMDocument('1.0', 'UTF-8');

    libxml_use_internal_errors(true);

    $dom->loadHTML(
        '<?xml encoding="UTF-8"><div id="blog-content-root">' . $html . '</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );

    libxml_clear_errors();

    $root = $dom->getElementById('blog-content-root');

    if (!$root) {
        return '';
    }

    $cleanNodes = function ($parent) use (&$cleanNodes, $dom, $allowedTags) {

        foreach (iterator_to_array($parent->childNodes) as $node) {

            if ($node instanceof DOMComment) {
                $parent->removeChild($node);
                continue;
            }

            if (!($node instanceof DOMElement)) {
                continue;
            }

            $cleanNodes($node);

            $tag = strtolower($node->tagName);

            if (!in_array($tag, $allowedTags, true)) {

                while ($node->firstChild) {
                    $parent->insertBefore($node->firstChild, $node);
                }

                $parent->removeChild($node);
                continue;
            }

            $allowedAttributes = ($tag === 'a') ? ['href', 'target'] : [];

            foreach (iterator_to_array($node->attributes) as $attribute) {

                if (!in_array(strtolower($attribute->name), $allowedAttributes, true)) {
                    $node->removeAttribute($attribute->name);
                }
            }

            if ($tag === 'a') {

                $href = trim($node->getAttribute('href'));

                if (
                    $href !== '' &&
                    !preg_match('/^(https?:\/\/|mailto:|\/|#)/i', $href)
                ) {
                    $node->removeAttribute('href');
                }

                if ($node->getAttribute('target') === '_blank') {
                    $node->setAttribute('rel', 'noopener noreferrer');
                } else {
                    $node->removeAttribute('target');
                }
            }
        }
    };

    $cleanNodes($root);

    $output = '';

    foreach ($root->childNodes as $child) {
        $output .= $dom->saveHTML($child);
    }

    return $output;
}

/* ---------------------------------------
   Generate Unique Slug
--------------------------------------- */

function blog_unique_slug(PDO $pdo, string $slug, int $excludeId = 0): string
{
    $baseSlug = blog_slug($slug);

    if ($baseSlug === '') {
        $baseSlug = 'blog-' . time();
    }

    $uniqueSlug = $baseSlug;
    $counter = 1;

    while (true) {

        $sql = "SELECT id FROM blogs WHERE slug = :slug";

        if ($excludeId > 0) {
            $sql .= " AND id != :exclude_id";
        }

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':slug', $uniqueSlug);

        if ($excludeId > 0) {
            $stmt->bindValue(':exclude_id', $excludeId, PDO::PARAM_INT);
        }

        $stmt->execute();

        if (!$stmt->fetch()) {
            return $uniqueSlug;
        }

        $counter++;
        $uniqueSlug = $baseSlug . '-' . $counter;
    }
}

/* ---------------------------------------
   Image Upload
--------------------------------------- */

function blog_upload_image(array $file): ?string
{
    if (
        !isset($file['error']) ||
        $file['error'] === UPLOAD_ERR_NO_FILE
    ) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Image upload failed. Please try again.');
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Image size must not exceed 5 MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($allowedTypes[$mime])) {
        throw new RuntimeException('Only JPG, PNG and WEBP images are allowed.');
    }

    $projectRoot = dirname(__DIR__, 2);
    $uploadDirectory = $projectRoot . '/uploads/blogs';

    if (!is_dir($uploadDirectory)) {
        if (!mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
            throw new RuntimeException('Unable to create blog upload directory.');
        }
    }

    $fileName = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mime];

    if (!move_uploaded_file($file['tmp_name'], $uploadDirectory . '/' . $fileName)) {
        throw new RuntimeException('Unable to save uploaded image.');
    }

    return '/uploads/blogs/' . $fileName;
}

/* ---------------------------------------
   Delete Uploaded Image
--------------------------------------- */

function blog_delete_image(?string $imagePath): void
{
    if (!$imagePath || strpos($imagePath, '/uploads/blogs/') !== 0) {
        return;
    }

    $projectRoot = dirname(__DIR__, 2);
    $filePath = $projectRoot . $imagePath;

    if (is_file($filePath)) {
        unlink($filePath);
    }
}

/* ---------------------------------------
   CSRF Token
--------------------------------------- */

if (empty($_SESSION['blog_csrf_token'])) {
    $_SESSION['blog_csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['blog_csrf_token'];

/* ---------------------------------------
   Handle Form Submission
--------------------------------------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $flashMessage = '';
    $flashType = 'success';

    try {

        $submittedToken = $_POST['csrf_token'] ?? '';

        if (!hash_equals($csrfToken, $submittedToken)) {
            throw new RuntimeException('Invalid request. Please refresh the page.');
        }

        $action = $_POST['action'] ?? '';

        /* ADD / EDIT BLOG */
        if ($action === 'save') {

            $blogId = (int)($_POST['blog_id'] ?? 0);

            $title = trim($_POST['title'] ?? '');
            $slugInput = trim($_POST['slug'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $excerpt = trim($_POST['excerpt'] ?? '');
            $intro = trim($_POST['intro'] ?? '');
            $content = blog_clean_html($_POST['content'] ?? '');
            $imageAlt = trim($_POST['image_alt'] ?? '');
            $authorName = trim($_POST['author_name'] ?? 'Real Estate Editorial Team');
            $authorRole = trim($_POST['author_role'] ?? 'Property Insights');
            $readingTime = max(1, (int)($_POST['reading_time'] ?? 5));
            $tags = trim($_POST['tags'] ?? '');
            $seoDescription = trim($_POST['seo_description'] ?? '');
            $status = $_POST['status'] ?? 'draft';

            if ($title === '' || $category === '' || $excerpt === '' || $content === '') {
                throw new RuntimeException('Please fill in all required fields.');
            }

            if (!in_array($status, ['draft', 'published'], true)) {
                throw new RuntimeException('Invalid blog status.');
            }

            $slug = blog_unique_slug(
                $pdo,
                $slugInput !== '' ? $slugInput : $title,
                $blogId
            );

            $oldImage = null;

            if ($blogId > 0) {

                $existingStmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
                $existingStmt->execute([$blogId]);
                $existingBlog = $existingStmt->fetch();

                if (!$existingBlog) {
                    throw new RuntimeException('Blog not found.');
                }

                $oldImage = $existingBlog['featured_image'];
            }

            $uploadedImage = blog_upload_image($_FILES['featured_image'] ?? []);

            $featuredImage = $uploadedImage ?: $oldImage;

            if ($blogId === 0 && !$featuredImage) {
                throw new RuntimeException('Please upload a featured image.');
            }

            if ($blogId > 0) {

                $stmt = $pdo->prepare("
                    UPDATE blogs SET
                        title = ?,
                        slug = ?,
                        category = ?,
                        excerpt = ?,
                        intro = ?,
                        content = ?,
                        featured_image = ?,
                        image_alt = ?,
                        author_name = ?,
                        author_role = ?,
                        reading_time = ?,
                        tags = ?,
                        seo_description = ?,
                        status = ?,
                        published_at = CASE
                            WHEN ? = 'published' THEN COALESCE(published_at, NOW())
                            ELSE NULL
                        END
                    WHERE id = ?
                ");

                $stmt->execute([
                    $title,
                    $slug,
                    $category,
                    $excerpt,
                    $intro,
                    $content,
                    $featuredImage,
                    $imageAlt,
                    $authorName,
                    $authorRole,
                    $readingTime,
                    $tags,
                    $seoDescription,
                    $status,
                    $status,
                    $blogId
                ]);

                if ($uploadedImage && $oldImage) {
                    blog_delete_image($oldImage);
                }

                $flashMessage = 'Blog updated successfully.';

            } else {

                $stmt = $pdo->prepare("
                    INSERT INTO blogs (
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
                        status,
                        published_at
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                        IF(? = 'published', NOW(), NULL)
                    )
                ");

                $stmt->execute([
                    $title,
                    $slug,
                    $category,
                    $excerpt,
                    $intro,
                    $content,
                    $featuredImage,
                    $imageAlt,
                    $authorName,
                    $authorRole,
                    $readingTime,
                    $tags,
                    $seoDescription,
                    $status,
                    $status
                ]);

                $flashMessage = 'Blog added successfully.';
            }

        /* DELETE BLOG */
        } elseif ($action === 'delete') {

            $blogId = (int)($_POST['blog_id'] ?? 0);

            if ($blogId <= 0) {
                throw new RuntimeException('Invalid blog ID.');
            }

            $stmt = $pdo->prepare("SELECT featured_image FROM blogs WHERE id = ?");
            $stmt->execute([$blogId]);
            $blog = $stmt->fetch();

            if (!$blog) {
                throw new RuntimeException('Blog not found.');
            }

            $deleteStmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
            $deleteStmt->execute([$blogId]);

            blog_delete_image($blog['featured_image']);

            $flashMessage = 'Blog deleted successfully.';

        } else {
            throw new RuntimeException('Invalid action.');
        }

    } catch (Throwable $e) {

        $flashMessage = $e->getMessage();
        $flashType = 'danger';
    }

    $_SESSION['blog_flash'] = [
        'message' => $flashMessage,
        'type' => $flashType
    ];

    header('Location: ' . APP_URL . '/admin/blog/index.php');
    exit;
}

/* ---------------------------------------
   Flash Message
--------------------------------------- */

$flashMessage = '';
$flashType = 'success';

if (!empty($_SESSION['blog_flash'])) {
    $flashMessage = $_SESSION['blog_flash']['message'];
    $flashType = $_SESSION['blog_flash']['type'];
    unset($_SESSION['blog_flash']);
}

/* ---------------------------------------
   Fetch Blogs
--------------------------------------- */

$stmt = $pdo->query("
    SELECT *
    FROM blogs
    ORDER BY created_at DESC, id DESC
");

$blogs = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Blogs | EstateAdmin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Quill Rich Text Editor -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

</head>

<body class="admin-body">

    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="admin-main">

        <?php include __DIR__ . '/../includes/header.php'; ?>

        <main class="admin-content">

            <!-- Page Heading -->
            <div class="admin-welcome">

                <div>
                    <h2>Blogs</h2>
                    <p>Manage your real estate articles and property insights.</p>
                </div>

                <button
                    type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#blogModal"
                    data-mode="add">

                    <i class="ri-add-line me-1"></i>
                    Add Blog

                </button>

            </div>

            <!-- Flash Message -->
            <?php if ($flashMessage !== ''): ?>

                <div class="alert alert-<?= blog_e($flashType) ?> alert-dismissible fade show" role="alert">

                    <?= blog_e($flashMessage) ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                </div>

            <?php endif; ?>

            <!-- Blog Table -->
            <section class="admin-panel">

                <div class="admin-panel-heading">

                    <div>
                        <h2>All Blogs</h2>
                        <p>Manage articles displayed on your website.</p>
                    </div>

                </div>

                <div class="table-responsive">

                    <table class="table admin-table align-middle mb-0">

                        <thead>
                            <tr>
                                <th>Blog</th>
                                <th>Category</th>
                                <th>Reading Time</th>
                                <th>Published Date</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (!empty($blogs)): ?>

                                <?php foreach ($blogs as $blog): ?>

                                    <tr>

                                        <td>

                                            <div class="d-flex align-items-center gap-3">

                                                <?php if (!empty($blog['featured_image'])): ?>

                                                    <img
                                                        src="<?= blog_e(APP_URL . $blog['featured_image']) ?>"
                                                        alt="<?= blog_e($blog['image_alt']) ?>"
                                                        width="65"
                                                        height="55"
                                                        style="object-fit:cover;border-radius:8px;">

                                                <?php endif; ?>

                                                <div>
                                                    <strong><?= blog_e($blog['title']) ?></strong>

                                                    <small class="d-block text-muted">
                                                        /<?= blog_e($blog['slug']) ?>
                                                    </small>
                                                </div>

                                            </div>

                                        </td>

                                        <td><?= blog_e($blog['category']) ?></td>

                                        <td>
                                            <?= (int)$blog['reading_time'] ?> min read
                                        </td>

                                        <td>
                                            <?= !empty($blog['published_at'])
                                                ? date('d M Y', strtotime($blog['published_at']))
                                                : 'Not published' ?>
                                        </td>

                                        <td>

                                            <?php if ($blog['status'] === 'published'): ?>

                                                <span class="admin-status completed">
                                                    Published
                                                </span>

                                            <?php else: ?>

                                                <span class="admin-status pending">
                                                    Draft
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td class="text-end">

                                            <button
                                                type="button"
                                                class="admin-action"
                                                title="Edit Blog"
                                                data-bs-toggle="modal"
                                                data-bs-target="#blogModal"
                                                data-mode="edit"
                                                data-blog="<?= blog_e(json_encode($blog, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP)) ?>">

                                                <i class="ri-edit-line"></i>

                                            </button>

                                            <button
                                                type="button"
                                                class="admin-action text-danger"
                                                title="Delete Blog"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteBlogModal"
                                                data-id="<?= (int)$blog['id'] ?>"
                                                data-title="<?= blog_e($blog['title']) ?>">

                                                <i class="ri-delete-bin-line"></i>

                                            </button>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        No blogs found. Click Add Blog to create your first article.
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>

        </main>

    </div>

    <!-- Add / Edit Blog Modal -->
    <div class="modal fade" id="blogModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

            <div class="modal-content">

                <form method="POST" enctype="multipart/form-data" id="blogForm">

                    <input type="hidden" name="csrf_token" value="<?= blog_e($csrfToken) ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="blog_id" id="blogId" value="">

                    <div class="modal-header">

                        <h5 class="modal-title" id="blogModalTitle">
                            Add Blog
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body">

                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label">Blog Title *</label>
                                <input
                                    type="text"
                                    name="title"
                                    id="blogTitle"
                                    class="form-control"
                                    maxlength="255"
                                    required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Category *</label>
                                <input
                                    type="text"
                                    name="category"
                                    id="blogCategory"
                                    class="form-control"
                                    placeholder="e.g. Buying Guide"
                                    maxlength="150"
                                    required>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label">URL Slug</label>
                                <input
                                    type="text"
                                    name="slug"
                                    id="blogSlug"
                                    class="form-control"
                                    placeholder="first-time-home-buying-guide">
                                <small class="text-muted">
                                    Automatically generated from the title. You can edit it.
                                </small>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Status *</label>
                                <select name="status" id="blogStatus" class="form-select" required>
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Short Description / Excerpt *</label>
                                <textarea
                                    name="excerpt"
                                    id="blogExcerpt"
                                    class="form-control"
                                    rows="3"
                                    required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Article Introduction</label>
                                <textarea
                                    name="intro"
                                    id="blogIntro"
                                    class="form-control"
                                    rows="2"></textarea>
                            </div>

                            <div class="col-md-7">
                                <label class="form-label">Featured Image *</label>
                                <input
                                    type="file"
                                    name="featured_image"
                                    id="blogImage"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png,.webp">

                                <small class="text-muted">
                                    JPG, PNG or WEBP. Maximum 5 MB.
                                    Required when adding a new blog.
                                </small>
                                <div id="currentBlogImage" class="mt-2"></div>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label">Image Alt Text</label>
                                <input
                                    type="text"
                                    name="image_alt"
                                    id="blogImageAlt"
                                    class="form-control"
                                    maxlength="255">
                            </div>

                            <div class="col-12">
                                <div> 
                                    <label class="form-label">Article Content *</label>
                                    <div id="blogEditor" style="min-height:200px;background:#fff;"></div>
                                    <textarea
                                        name="content"
                                        id="blogContent"
                                        class="d-none"
                                         ></textarea>
                                     
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Author Name</label>
                                <input
                                    type="text"
                                    name="author_name"
                                    id="blogAuthor"
                                    class="form-control"
                                    value="Real Estate Editorial Team"
                                    maxlength="150">

                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Author Role</label>
                                <input
                                    type="text"
                                    name="author_role"
                                    id="blogAuthorRole"
                                    class="form-control"
                                    value="Property Insights"
                                    maxlength="150">

                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Reading Time (Minutes)</label>
                                <input
                                    type="number"
                                    name="reading_time"
                                    id="blogReadingTime"
                                    class="form-control"
                                    min="1"
                                    value="5">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Tags</label>
                                <input
                                    type="text"
                                    name="tags"
                                    id="blogTags"
                                    class="form-control"
                                    placeholder="Buying Guide, Real Estate, Home Buying">
                                <small class="text-muted">
                                    Separate tags with commas.
                                </small>
                            </div>

                            <div class="col-12">
                                <label class="form-label">SEO Meta Description</label>
                                <textarea
                                    name="seo_description"
                                    id="blogSeo"
                                    class="form-control"
                                    rows="2"
                                    maxlength="320"></textarea>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Save Blog
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <!-- Delete Blog Modal -->
    <div class="modal fade" id="deleteBlogModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form method="POST">

                    <input type="hidden" name="csrf_token" value="<?= blog_e($csrfToken) ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="blog_id" id="deleteBlogId">

                    <div class="modal-header">

                        <h5 class="modal-title">Delete Blog</h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body">

                        Are you sure you want to delete
                        <strong id="deleteBlogTitle"></strong>?

                        <p class="text-muted mb-0 mt-2">
                            This action cannot be undone.
                        </p>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-danger">
                            Delete Blog
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <script src="../js/admin.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const blogModal = document.getElementById('blogModal');
            const blogForm = document.getElementById('blogForm');

            const quill = new Quill('#blogEditor', {
                theme: 'snow',
                placeholder: 'Write your blog article here...',
                modules: {
                    toolbar: [
                        [{ header: [2, 3, 4, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['blockquote', 'link'],
                        ['clean']
                    ]
                }
            });

            const fields = {
                id: document.getElementById('blogId'),
                title: document.getElementById('blogTitle'),
                slug: document.getElementById('blogSlug'),
                category: document.getElementById('blogCategory'),
                excerpt: document.getElementById('blogExcerpt'),
                intro: document.getElementById('blogIntro'),
                imageAlt: document.getElementById('blogImageAlt'),
                author: document.getElementById('blogAuthor'),
                authorRole: document.getElementById('blogAuthorRole'),
                readingTime: document.getElementById('blogReadingTime'),
                tags: document.getElementById('blogTags'),
                seo: document.getElementById('blogSeo'),
                status: document.getElementById('blogStatus')
            };

            let slugManuallyChanged = false;

            function makeSlug(value) {
                return value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-|-$/g, '');
            }

            fields.slug.addEventListener('input', function () {
                slugManuallyChanged = true;
            });

            fields.title.addEventListener('input', function () {
                if (!slugManuallyChanged) {
                    fields.slug.value = makeSlug(fields.title.value);
                }
            });

            function resetBlogForm() {

                blogForm.reset();

                fields.id.value = '';
                fields.author.value = 'Real Estate Editorial Team';
                fields.authorRole.value = 'Property Insights';
                fields.readingTime.value = 5;
                fields.status.value = 'draft';

                quill.setText('');

                document.getElementById('blogContent').value = '';
                document.getElementById('currentBlogImage').innerHTML = '';

                slugManuallyChanged = false;
            }

            blogModal.addEventListener('show.bs.modal', function (event) {

                const button = event.relatedTarget;
                const mode = button.dataset.mode;

                resetBlogForm();

                if (mode === 'add') {

                    document.getElementById('blogModalTitle').textContent = 'Add Blog';

                    return;
                }

                document.getElementById('blogModalTitle').textContent = 'Edit Blog';

                const blog = JSON.parse(button.dataset.blog);

                fields.id.value = blog.id;
                fields.title.value = blog.title;
                fields.slug.value = blog.slug;
                fields.category.value = blog.category;
                fields.excerpt.value = blog.excerpt;
                fields.intro.value = blog.intro || '';
                fields.imageAlt.value = blog.image_alt || '';
                fields.author.value = blog.author_name || '';
                fields.authorRole.value = blog.author_role || '';
                fields.readingTime.value = blog.reading_time || 5;
                fields.tags.value = blog.tags || '';
                fields.seo.value = blog.seo_description || '';
                fields.status.value = blog.status;

                quill.root.innerHTML = blog.content || '';

                if (blog.featured_image) {

                    document.getElementById('currentBlogImage').innerHTML =
                        '<img src="' + <?= json_encode(APP_URL) ?> +
                        blog.featured_image +
                        '" alt="Current blog image" width="120" height="80" style="object-fit:cover;border-radius:8px;">';
                }

                slugManuallyChanged = true;
            });

            blogForm.addEventListener('submit', function (event) {
                const content = quill.root.innerHTML;
                if (quill.getText().trim() === '') {
                    event.preventDefault();
                    alert('Please write your blog content.');
                    return;
                }
                document.getElementById('blogContent').value = content;
            });
            const deleteModal = document.getElementById('deleteBlogModal');
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                document.getElementById('deleteBlogId').value = button.dataset.id;
                document.getElementById('deleteBlogTitle').textContent =
                    button.dataset.title;

            });
        });
    </script>
</body>
</html>
