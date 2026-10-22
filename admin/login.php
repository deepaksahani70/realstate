
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | EstateAdmin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>

<main class="admin-login">
    <div class="login-wrapper">

        <a href="../index.php" class="admin-brand">
            <span class="brand-icon"><i class="ri-building-2-line"></i></span>
            <span>
                <strong>Estate<span>Admin</span></strong>
                <small>PROPERTY MANAGEMENT</small>
            </span>
        </a>

        <div class="login-card">
            <div class="login-heading">
                <span class="login-shield"><i class="ri-shield-check-line"></i></span>
                <h1>Welcome back</h1>
                <p>Sign in to access your admin dashboard.</p>
            </div>

            <form action="authenticate.php" method="POST">
                <div class="login-field">
                    <label for="email">Email Address</label>
                    <div class="login-input">
                        <i class="ri-mail-line"></i>
                        <input type="email" id="email" name="email"
                            placeholder="Enter your email" autocomplete="username" required>
                    </div>
                </div>

                <div class="login-field">
                    <label for="password">Password</label>
                    <div class="login-input">
                        <i class="ri-lock-password-line"></i>
                        <input type="password" id="password" name="password"
                            placeholder="Enter your password"
                            autocomplete="current-password" required>
                        <button type="button" class="password-toggle"
                            id="togglePassword" aria-label="Show password"
                            aria-pressed="false">
                            <i class="ri-eye-line"></i>
                        </button>
                    </div>
                </div>

                <div class="login-options">
                    <label class="remember-option">
                        <input type="checkbox" name="remember" value="1">
                        <span>Remember me</span>
                    </label>
                    <a href="forgot-password.php">Forgot password?</a>
                </div>

                <button type="submit" class="login-submit">
                    Sign In <i class="ri-arrow-right-line"></i>
                </button>
            </form>

            <div class="login-security">
                <i class="ri-shield-check-line"></i>
                Secure administrator access
            </div>
        </div>

        <p class="login-copyright">
            &copy; <?= date('Y') ?> EstateAdmin. All rights reserved.
        </p>

    </div>
</main>

<script>
const toggle = document.getElementById('togglePassword');
const password = document.getElementById('password');

toggle.addEventListener('click', function () {
    const visible = password.type === 'password';
    password.type = visible ? 'text' : 'password';
    toggle.innerHTML = visible
        ? '<i class="ri-eye-off-line"></i>'
        : '<i class="ri-eye-line"></i>';
    toggle.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
    toggle.setAttribute('aria-pressed', String(visible));
});
</script>

</body>
</html>
