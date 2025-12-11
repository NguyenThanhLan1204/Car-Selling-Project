<?php
include 'session_init.php';

// 1. Gọi file kết nối database
require_once 'db.php'; 

// 2. Lấy dữ liệu từ form login
if (isset($_POST['user']) && isset($_POST['password'])) {

    $username = $_POST['user'];
    $password = $_POST['password'];

    // 3. Xử lý chuỗi để tránh lỗi SQL Injection
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // 4. Kiểm tra trong bảng customer
    $sql = "SELECT * FROM customer WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        session_regenerate_id(true);

        $_SESSION['username'] = $username;
        $_SESSION['customer_id'] = $row['customer_id'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['role'] = $row['role']; // Lưu quyền admin/user
        $_SESSION['LAST_ACTIVITY'] = time(); 

        // ✅ Cookie nhớ đăng nhập 1 tiếng
            setcookie("remember_login", $row['customer_id'], time() + 3600, "/");
            setcookie("username", $row['username'], time() + 3600, "/");
            setcookie("role", $row['role'], time() + 3600, "/");
            setcookie("name", $row['name'], time() + 3600, "/");

        // --- BỔ SUNG: LOGIC HỢP NHẤT GIỎ HÀNG (CART MERGE) ---
        
        if (isset($_COOKIE['user_cart'])) {
    // 1. Lấy giỏ hàng cũ từ Cookie
    $cookie_cart = json_decode($_COOKIE['user_cart'], true);
    
    // 2. Lấy giỏ hàng hiện tại (Đã được nạp từ Cookie ở login.php hoặc là mảng rỗng)
    $session_cart = $_SESSION['cart'] ?? []; 
    
    if ($cookie_cart) {
        // 3. Hợp nhất: Chỉ thêm vào Session những sản phẩm nào CHỈ có trong Cookie
        // (Tránh cộng dồn số lượng 1+1)
        foreach ($cookie_cart as $vehicle_id => $cookie_item) {
            
            if (!isset($session_cart[$vehicle_id])) {
                // Nếu sản phẩm CHƯA có trong Session, thì thêm nó vào
                $session_cart[$vehicle_id] = $cookie_item;
            } 
            // KHÔNG CỘNG DỒN NỮA. Nếu đã có, chúng ta giữ nguyên giá trị đã có (là 1) 
            // vì nó đã được nạp ở bước trước.
        }
        
        // 4. Gán giỏ hàng đã hợp nhất vào Session
        $_SESSION['cart'] = $session_cart;
    }

    // 5. Xóa Cookie giỏ hàng (QUAN TRỌNG: Đã hợp nhất, cần phải xóa Cookie ngay lập tức)
    setcookie('user_cart', '', time() - 3600, '/'); 
}
        
        // Đăng nhập thành công -> Về trang chủ

        // SỬA: Chuyển hướng về base.php thay vì home.php
    header('Location: base.php?page=home');
    exit();
    } else {
        // Sai mật khẩu -> Quay lại login
        header('Location: login.php?error=1');
        exit();

    }
} else {
    // Nếu truy cập trực tiếp file này mà không qua form login
    header('Location: login.php');
    exit();
}
?>