<?php
/**
 * Enterprise PHP/MySQL Database Configuration
 * Auto-initializes database and tables if they do not exist.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "";
$db_name = "fsia";

// Disable throwing exceptions on connection failure to allow session fallback
mysqli_report(MYSQLI_REPORT_OFF);

// Suppress connection warning to handle fallback gracefully
$connect = @mysqli_connect($db_host, $db_user, $db_pass);

if ($connect) {
    // 1. Initialize Database
    mysqli_query($connect, "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    mysqli_select_db($connect, $db_name);

    // 2. Initialize 'registration' Table
    $create_reg_table = "CREATE TABLE IF NOT EXISTS `registration` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `first_name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `mobile` VARCHAR(20) NOT NULL,
        `state` VARCHAR(100),
        `cityfsia` VARCHAR(100),
        `instagram` VARCHAR(100),
        `regtype` VARCHAR(50) DEFAULT '30',
        `age` INT NULL,
        `dob` DATE NULL,
        `qualification` VARCHAR(100) NULL,
        `skills` VARCHAR(255) NULL,
        `message` TEXT NULL,
        `trans_id` VARCHAR(100) NULL,
        `payment_status` VARCHAR(50) DEFAULT 'pending',
        `pdate` TIMESTAMP NULL DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    mysqli_query($connect, $create_reg_table);

    // Auto-alter table to add columns if they do not exist (e.g. for existing local mysql setup)
    $alterations = [
        'age' => "ALTER TABLE `registration` ADD COLUMN `age` INT NULL AFTER `regtype`",
        'dob' => "ALTER TABLE `registration` ADD COLUMN `dob` DATE NULL AFTER `age`",
        'qualification' => "ALTER TABLE `registration` ADD COLUMN `qualification` VARCHAR(100) NULL AFTER `dob`",
        'skills' => "ALTER TABLE `registration` ADD COLUMN `skills` VARCHAR(255) NULL AFTER `qualification`",
        'message' => "ALTER TABLE `registration` ADD COLUMN `message` TEXT NULL AFTER `skills`"
    ];
    foreach ($alterations as $col => $sql) {
        $res = mysqli_query($connect, "SHOW COLUMNS FROM `registration` LIKE '$col'");
        if ($res && mysqli_num_rows($res) === 0) {
            mysqli_query($connect, $sql);
        }
    }

    // 3. Initialize 'more_pages' Table
    $create_more_pages = "CREATE TABLE IF NOT EXISTS `more_pages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `page_name` VARCHAR(50) NOT NULL,
        `pay` VARCHAR(50) DEFAULT '₹2,999'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    mysqli_query($connect, $create_more_pages);

    // 4. Seed Page Meta (Page ID 145 = Miss Universe 2026 pricing)
    $check_meta = mysqli_query($connect, "SELECT * FROM `more_pages` WHERE `page_name` = '145'");
    if (mysqli_num_rows($check_meta) == 0) {
        mysqli_query($connect, "INSERT INTO `more_pages` (`page_name`, `pay`) VALUES ('145', '₹2,999')");
    }
} else {
    // Fallback: If MySQL is not running on the local system, we create a fallback mock
    // that uses PHP Session variables to simulate database records.
    // This allows the workflow to be tested completely in a mock mode.
    if (!isset($_SESSION['mock_db'])) {
        $_SESSION['mock_db'] = [];
        $_SESSION['mock_more_pages'] = [
            '145' => ['page_name' => '145', 'pay' => '₹2,999']
        ];
    }
}

/**
 * Helper query wrapper to support session-based mock database when MySQL is offline.
 */
function db_query($query) {
    global $connect;
    if ($connect) {
        return mysqli_query($connect, $query);
    }

    // Parse the query for simulated session logic
    $query = trim($query);
    if (stripos($query, "select * from registration") === 0) {
        // Find token
        preg_match("/md5\(id\)\s*=\s*'([^']+)'/", $query, $matches);
        $token = $matches[1] ?? '';
        foreach ($_SESSION['mock_db'] as $row) {
            if (md5($row['id']) === $token) {
                return [$row];
            }
        }
        return [];
    } elseif (stripos($query, "select * from more_pages") === 0) {
        preg_match("/page_name='([^']+)'/", $query, $matches);
        $page = $matches[1] ?? '';
        if (isset($_SESSION['mock_more_pages'][$page])) {
            return [$_SESSION['mock_more_pages'][$page]];
        }
        return [['page_name' => $page, 'pay' => '₹2,999']];
    }
    return false;
}

function db_fetch($result) {
    global $connect;
    if ($connect) {
        return mysqli_fetch_assoc($result);
    }
    if (is_array($result) && !empty($result)) {
        return array_shift($result);
    }
    return null;
}
?>
