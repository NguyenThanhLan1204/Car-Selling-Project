<?php
session_start();

// Xóa cookie giỏ hàng theo user (QUAN TRỌNG NHẤT)
if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
    setcookie('user_cart_' . $customer_id, '', time() - 3600, '/');
}

// Xóa giỏ hàng trong session
unset($_SESSION['cart']);

// Xóa toàn bộ session
$_SESSION = [];

// Xóa cookie PHPSESSID
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hủy session
session_destroy();

// Xóa các cookie khác
setcookie("remember_login", "", time() - 3600, "/");
setcookie("username", "", time() - 3600, "/");
setcookie("role", "", time() - 3600, "/");
setcookie("name", "", time() - 3600, "/");

header("Location: base.php?page=home");
exit();
