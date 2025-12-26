<?php
session_start();

if (isset($_SESSION['customer_id'])) {
    $cookie_name = "user_cart_" . $_SESSION['customer_id']; 
    $cookie_expiry = time() + (86400 * 30); // 30 ngày 
    if (empty($_SESSION['cart'])) { 
        // Nếu giỏ hàng rỗng thì xoá cookie 
        setcookie($cookie_name, '', time() - 3600, "/"); 
    } else { 
        // Nếu giỏ hàng còn thì lưu lại vào cookie 
        $cart_json = json_encode($_SESSION['cart']); 
        setcookie($cookie_name, $cart_json, $cookie_expiry, "/"); 
    } 
}

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
