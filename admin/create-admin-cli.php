
<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only run from the command line.');
}

require_once __DIR__ . '/config/database.php';

function prompt(string $message): string
{
    echo $message;
    return trim((string) fgets(STDIN));
}

$name = prompt('Admin name: ');
$email = prompt('Admin email: ');

echo 'Admin password (input will be visible): ';
$password = trim((string) fgets(STDIN));

if ($name === '' || strlen($name) > 100) {
    exit("Invalid admin name.\n");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
    exit("Invalid email address.\n");
}

if (strlen($password) < 6 || strlen($password) > 4096) {
    exit("Password must be at least 6 characters.\n");
}

try {
    $check = $pdo->prepare(
        'SELECT id FROM admin_users WHERE email = :email LIMIT 1'
    );
    $check->execute(['email' => $email]);

    if ($check->fetch()) {
        exit("An admin with this email already exists.\n");
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
        'INSERT INTO admin_users (name, email, password_hash)
         VALUES (:name, :email, :password_hash)'
    );

    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'password_hash' => $hash,
    ]);

    echo "Admin account created successfully.\n";
} catch (PDOException $e) {
    error_log('Admin creation failed: ' . $e->getMessage());
    exit("Unable to create the admin account.\n");
}
