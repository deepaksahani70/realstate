
<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/config/database.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . APP_URL . '/admin/dashboard.php');
    exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Real Estate</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="../css/style.css">
  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css">
</head>
<body>

<main class="admin-login-page">
    <div class="admin-login-card">
        <div class="admin-login-heading">
            <div class="admin-login-icon">
                <i class="ri-building-2-line"></i>
            </div>
            <h1 class="testing">Admin Login</h1>
            <p>Sign in to manage your real estate website.</p>
        </div>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="authenticate.php" method="POST">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
            >

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="Enter your admin email"
                    autocomplete="username"
                    maxlength="190"
                    required
                    autofocus
                >
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="admin-password-field">
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >
                    <button
                        type="button"
                        class="admin-password-toggle"
                        id="togglePassword"
                        aria-label="Show password"
                    >
                        <i class="ri-eye-line"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Sign In <i class="ri-arrow-right-line ms-1"></i>
            </button>
        </form>

        <p class="admin-login-footer">
            &copy; <?= date('Y') ?> Real Estate. Admin access only.
        </p>
    </div>
</main>

<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
        const showPassword = passwordInput.type === 'password';
        passwordInput.type = showPassword ? 'text' : 'password';

        this.innerHTML = showPassword
            ? '<i class="ri-eye-off-line"></i>'
            : '<i class="ri-eye-line"></i>';

        this.setAttribute(
            'aria-label',
            showPassword ? 'Hide password' : 'Show password'
        );
    });
</script>

</body>
</html>
