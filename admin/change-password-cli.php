
<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI access only.');
}

require_once __DIR__ . '/config/database.php';

$email = 'deepakdeveloper0@gmail.com'; // Your existing admin email

echo 'Enter new password: ';
$password = trim((string) fgets(STDIN));

if (strlen($password) < 6 || strlen($password) > 4096) {
    exit("Password must be at least 6 characters.\n");
}

try {
    $check = $pdo->prepare(
        'SELECT id FROM admin_users WHERE email = :email LIMIT 1'
    );
    $check->execute(['email' => $email]);

    if (!$check->fetch()) {
        exit("Admin account not found. Check the email address.\n");
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
        'UPDATE admin_users
         SET password_hash = :password_hash
         WHERE email = :email'
    );

    $stmt->execute([
        'password_hash' => $hash,
        'email' => $email,
    ]);

    echo "Admin password updated successfully.\n";
} catch (PDOException $e) {
    error_log('Password update failed: ' . $e->getMessage());
    exit("Unable to update the password.\n");
}
