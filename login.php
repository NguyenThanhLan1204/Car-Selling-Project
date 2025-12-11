
<?php
session_start();

// --- BỔ SUNG: NẠP GIỎ HÀNG TỪ COOKIE VÀO SESSION (NẾU CHƯA ĐĂNG NHẬP) ---
if (!isset($_SESSION['username'])) {
    if (isset($_COOKIE['user_cart']) && !isset($_SESSION['cart'])) {
        // Nếu có Cookie giỏ hàng và Session giỏ hàng chưa tồn tại,
        // thì nạp giỏ hàng từ Cookie vào Session để hiển thị
        $cookie_cart = json_decode($_COOKIE['user_cart'], true);
        if ($cookie_cart) {
            $_SESSION['cart'] = $cookie_cart;
        }
    }
}
// --------------------------------------------------------------------------

if (isset($_SESSION['username'])) {
    header("Location: base.php?page=home");
    exit();
}

// Lấy username từ cookie để autofill (nếu có)
$savedUsername = isset($_COOKIE['username']) ? $_COOKIE['username'] : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/login.css"> 
</head>

<body>
    <div class="login-wrapper">
        <div class="container h-100">
            <div class="row h-100 justify-content-center align-items-center">
                <div class="col-10 col-md-8 col-lg-5 col-xl-4">
                    <div class="login-card">
                        <h2 class="text-center mb-4">Login</h2>
                        <form action="validation.php" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>                        
                                <input type="text" id="username" name="user" class="form-control custom-input" required>                            
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password" class="form-control custom-input" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 custom-btn">Login</button>
                            
                            <div class="text-center mt-4 registration-link">
                                <p>Don't have an account? <a href="registration.php">Registration</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>   
    </div>
</body>
</html>