
<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/config/database.php';

$appUrl = APP_URL;
function loginFailed(string $message = 'Invalid email or password.'): never
{
    $_SESSION['login_error'] = $message;
     header('Location: ' . APP_URL . '/admin/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: ' . APP_URL . '/admin/login.php');
    exit;
}

$submittedToken = $_POST['csrf_token'] ?? '';

if (
    !is_string($submittedToken) ||
    !hash_equals($_SESSION['csrf_token'], $submittedToken)
) {
    loginFailed('Your session expired. Please try again.');
}

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if (
    $email === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($email) > 190 ||
    $password === '' ||
    strlen($password) > 4096
) {
    loginFailed();
}

try {
    $stmt = $pdo->prepare(
        'SELECT id, name, email, password_hash
         FROM admin_users
         WHERE email = :email AND is_active = 1
         LIMIT 1'
    );

    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch();

    if (
        !$admin ||
        !password_verify($password, $admin['password_hash'])
    ) {
        loginFailed();
    }

    session_regenerate_id(true);

    $_SESSION['admin_id'] = (int) $admin['id'];
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

header('Location: ' . APP_URL . '/admin/dashboard.php');
    exit;
} catch (PDOException $e) {
    error_log('Admin authentication error: ' . $e->getMessage());
    http_response_code(500);
    exit('Unable to sign in right now. Please try again later.');
}
