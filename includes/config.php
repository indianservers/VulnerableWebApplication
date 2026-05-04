<?php
// Database Configuration
// =====================

define('DB_HOST', 'localhost');
define('DB_USER', 'myuser');
define('DB_PASS', 'StrongPassword123');
define('DB_NAME', 'myapp_db');

$GLOBALS['db_bootstrap_status'] = null;

/**
 * Run the bundled schema if the core tables are missing.
 */
function initializeDatabaseIfNeeded(mysqli $conn): void {
    $schema_file = __DIR__ . '/../database_enhanced.sql';
    if (!file_exists($schema_file)) {
        throw new RuntimeException('Database schema file not found: ' . $schema_file);
    }

    $schema_sql = file_get_contents($schema_file);
    if ($schema_sql === false) {
        throw new RuntimeException('Unable to read database schema file.');
    }

    if (!$conn->multi_query($schema_sql)) {
        throw new RuntimeException('Database bootstrap failed: ' . $conn->error);
    }

    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());

    if ($conn->errno) {
        throw new RuntimeException('Database bootstrap failed: ' . $conn->error);
    }
}

function needsDatabaseBootstrap(mysqli $conn): bool {
    $table_check = $conn->query("SHOW TABLES LIKE 'users'");
    $has_users_table = $table_check->num_rows > 0;
    $table_check->free();

    if (!$has_users_table) {
        return true;
    }

    $grades_check = $conn->query("SHOW TABLES LIKE 'grades'");
    $has_grades_table = $grades_check->num_rows > 0;
    $grades_check->free();

    if (!$has_grades_table) {
        return true;
    }

    $column_result = $conn->query("SHOW COLUMNS FROM grades LIKE 'midterm'");
    $column = $column_result->fetch_assoc();
    $column_result->free();

    if (!$column) {
        return true;
    }

    return strtolower($column['Type']) !== 'decimal(5,2)';
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    $conn->set_charset('utf8mb4');
    $conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    $conn->select_db(DB_NAME);

    if (needsDatabaseBootstrap($conn)) {
        initializeDatabaseIfNeeded($conn);
        $conn->select_db(DB_NAME);
        $GLOBALS['db_bootstrap_status'] = 'initialized';
    }
} catch (Throwable $e) {
    die('Database setup error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

// For secure queries - prepared statements
function secureQuery($query, $params = array()) {
    global $conn;
    $stmt = $conn->prepare($query);
    if ($params) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $stmt->execute();
    return $stmt;
}

// For escaping user input (NOT SECURE - demonstration only)
function escapeInput($input) {
    global $conn;
    return $conn->real_escape_string($input);
}

// Session configuration
session_start();
