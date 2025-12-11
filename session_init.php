<?php
session_start();

$timeout = 3600;

// 1) Kiểm tra timeout
if (isset($_SESSION['LAST_ACTIVITY'])) {
    if (time() - $_SESSION['LAST_ACTIVITY'] > $timeout) {

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        
        setcookie("username", "", time() - 3600, "/");
        setcookie("role", "", time() - 3600, "/");
        setcookie("name", "", time() - 3600, "/");

    } else {
        $_SESSION['LAST_ACTIVITY'] = time();
    }
}

// 2) Auto login bằng cookie
if (!isset($_SESSION['customer_id']) && isset($_COOKIE['remember_login'])) {

    $customer_id = intval($_COOKIE['remember_login']);

    if ($customer_id > 0) {
        $_SESSION['customer_id'] = $customer_id;

        // Khôi phục username nếu có
        if (isset($_COOKIE['username'])) {
            $_SESSION['username'] = $_COOKIE['username'];
        }

        // Khôi phục role nếu có
        if (isset($_COOKIE['role'])) {
            $_SESSION['role'] = $_COOKIE['role'];
        }

        // Khôi phục name nếu có
        if (isset($_COOKIE['name'])) {
            $_SESSION['name'] = $_COOKIE['name'];
        }

        $_SESSION['LAST_ACTIVITY'] = time();
    }
}
?>
