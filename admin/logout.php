
<?php

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/config/database.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_URL . '/admin/dashboard.php');
    exit;
}

// Verify CSRF token
$submittedToken = $_POST['csrf_token'] ?? '';

if (
    !is_string($submittedToken) ||
    !hash_equals($_SESSION['csrf_token'], $submittedToken)
) {
    http_response_code(403);
    exit('Invalid request. Please return to the admin panel.');
}

// Clear session data
$_SESSION = [];

// Delete session cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $params['path'],
        'domain' => $params['domain'],
        'secure' => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => $params['samesite'] ?? 'Lax',
    ]);
}

// Destroy session
session_destroy();

// Redirect to login
header('Location: ' . APP_URL . '/admin/login.php');
exit;
