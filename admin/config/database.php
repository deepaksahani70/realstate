
<?php
use Dotenv\Dotenv;
$projectRoot = dirname(__DIR__, 2);
$autoloadPath = $projectRoot . '/vendor/autoload.php';

if (!file_exists($autoloadPath)) {
    error_log('Composer autoload file not found.');
    http_response_code(500);
    exit('Application configuration error.');
}

require_once $autoloadPath;

if (file_exists($projectRoot . '/.env')) {
    $dotenv = Dotenv::createImmutable($projectRoot);
    $dotenv->safeLoad();
}
require_once __DIR__ . '/app.php';
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$dbname = $_ENV['DB_NAME'] ?? '';
$username = $_ENV['DB_USER'] ?? '';
$password = $_ENV['DB_PASSWORD'] ?? '';
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

if ($dbname === '' || $username === '') {
    error_log('Database name or username is missing.');
    http_response_code(500);
    exit('Database configuration is incomplete.');
}

$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Unable to connect to the database.');
}
