<?php
session_start();

// 1. Gọi file kết nối database
// Lệnh này bắt buộc phải có file db.php thì mới chạy được
require_once 'db.php'; 

// 2. Lấy dữ liệu từ form login
if (isset($_POST['user']) && isset($_POST['password'])) {
    $username = $_POST['user'];
    $password = $_POST['password'];

    // 3. Xử lý chuỗi để tránh lỗi SQL Injection
    // Nếu $conn chưa được định nghĩa ở bước 1, dòng này sẽ báo lỗi như bạn gặp
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // 4. Kiểm tra trong bảng customer
    $sql = "SELECT * FROM customer WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $username;
        $_SESSION['customer_id'] = $row['customer_id'];
        $_SESSION['role'] = $row['role']; // Lưu quyền admin/user

        // Đăng nhập thành công -> Về trang chủ
        header('Location: home.php');
    } else {
        // Sai mật khẩu -> Quay lại login
        header('Location: login.php?error=1');
    }
} else {
    // Nếu truy cập trực tiếp file này mà không qua form login
    header('Location: login.php');
}
?>