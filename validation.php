<?php
include 'session_init.php';
require_once 'db.php'; 

if (isset($_POST['user']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['user']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM customer WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        session_regenerate_id(true);

        $_SESSION['username'] = $row['username'];
        $_SESSION['customer_id'] = $row['customer_id'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['LAST_ACTIVITY'] = time(); 

        // ✅ Cookie nhớ đăng nhập 1 tiếng
        setcookie("remember_login", $row['customer_id'], time() + 3600, "/");
        setcookie("username", $row['username'], time() + 3600, "/");
        setcookie("role", $row['role'], time() + 3600, "/");
        setcookie("name", $row['name'], time() + 3600, "/");

        // ✅ Load giỏ hàng riêng theo user_id
        $cookie_name = "user_cart_" . $row['customer_id'];
        if (isset($_COOKIE[$cookie_name])) {
            $_SESSION['cart'] = json_decode($_COOKIE[$cookie_name], true);
        } else {
            $_SESSION['cart'] = [];
        }

        header('Location: base.php?page=home');
        exit();
    } else {
        header('Location: login.php?error=1');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
?>
