
<?php
require_once __DIR__ . '/config/database.php';

try {
    $stmt = $pdo->query('SELECT DATABASE() AS database_name');
    $database = $stmt->fetch();

    echo 'Database connected successfully!';
    echo '<br>Database: ' .
        htmlspecialchars($database['database_name'], ENT_QUOTES, 'UTF-8');
} catch (Throwable $e) {
    error_log('Database test failed: ' . $e->getMessage());
    http_response_code(500);
    echo 'Database test failed.';
}
