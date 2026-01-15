<?php
// Kiểm tra xem session đã bắt đầu chưa. Nếu chưa thì start session.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kết nối database
require_once 'db.php';

// --- 1. KIỂM TRA QUYỀN TRUY CẬP ---
// Nếu trong session chưa có 'customer_id' 
if (!isset($_SESSION['customer_id'])) {
    // Chuyển hướng người dùng về trang đăng nhập
    echo "<script>window.location.href='login.php';</script>";
    exit(); 
}

// Lấy ID người dùng từ session hiện tại
$customer_id = $_SESSION['customer_id'];
$message = "";
$msg_type = ""; 

// --- 2. XỬ LÝ YÊU CẦU CẬP NHẬT  ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form gửi lên
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $phone   = $_POST['phone_number'];
    $address = $_POST['address'];
    
    // Kiểm tra dữ liệu rỗng: Nếu có nhập thì lấy giá trị, nếu không thì set là NULL
    $age     = !empty($_POST['age']) ? $_POST['age'] : NULL;
    $dob     = !empty($_POST['dob']) ? $_POST['dob'] : NULL;

    // Sử dụng dấu ? (Prepared Statement) để chống hack SQL Injection
    $sql_update = "UPDATE customer 
                   SET name = ?, email = ?, phone_number = ?, address = ?, age = ?, dob = ? 
                   WHERE customer_id = ?";
    
    // Chuẩn bị statement
    $stmt = $conn->prepare($sql_update);
    
    // Gán giá trị vào các dấu ? theo thứ tự
    $stmt->bind_param("ssssisi", $name, $email, $phone, $address, $age, $dob, $customer_id);

    // Thực thi câu lệnh
    if ($stmt->execute()) {
        $message = "Update profile successful!";
        $msg_type = "success"; // Màu xanh
        
        // --- CẬP NHẬT LẠI SESSION ---
      
        $_SESSION['name'] = $name;
        // $_SESSION['username'] giữ nguyên vì thường username không cho đổi
    } else {
        $message = "Error updating information: " . $conn->error;
        $msg_type = "danger"; 
    }
    // Đóng statement để giải phóng tài nguyên
    $stmt->close();
}

// --- 3. LẤY THÔNG TIN HIỆN TẠI ĐỂ HIỂN THỊ LÊN FORM ---
$sql_info = "SELECT * FROM customer WHERE customer_id = ?";
$stmt_info = $conn->prepare($sql_info);
$stmt_info->bind_param("i", $customer_id);
$stmt_info->execute();

// Lấy kết quả trả về
$result = $stmt_info->get_result();
// Chuyển dòng dữ liệu thành mảng kết hợp 
$user = $result->fetch_assoc();
$stmt_info->close();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                
                <div class="card-header bg-primary text-white">
                    <h4 class="fw-bold mb-0"><i class='bx bx-user-circle'></i> My Profile</h4>
                </div>
                
                <div class="card-body p-4">
                    
                    <?php if ($message): ?>
                        <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show" role="alert">
                            <?= $message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted small text-uppercase fw-bold">Username</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Age</label>
                                <input type="number" name="age" class="form-control" value="<?= htmlspecialchars($user['age']) ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($user['phone_number']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date of Birth</label>
                                <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($user['dob']) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Address</label>
                            <textarea name="address" class="form-control" rows="2" required><?= htmlspecialchars($user['address']) ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="base.php?page=home" class="btn btn-outline-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary fw-bold px-4">Update Information</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>