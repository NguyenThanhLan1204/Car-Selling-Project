<?php
session_start();
// Gọi file kết nối database (Đảm bảo bạn đã tạo file db.php cùng thư mục)
require_once 'db.php';

// Kiểm tra xem có ID xe trên đường dẫn không (ví dụ: information.php?id=1)
if (isset($_GET['id'])) {
    // Lấy ID và ép kiểu sang số nguyên để bảo mật
    $vehicle_id = intval($_GET['id']);

    // Viết câu lệnh SQL lấy thông tin Xe + Tên Hãng + Quốc gia
    // JOIN bảng vehicle với manufacturer để lấy tên hãng (manufacturer.name)
    $sql = "SELECT v.*, m.name AS manufacturer_name, m.country 
            FROM vehicle v 
            JOIN manufacturer m ON v.manufacturer_id = m.manufacturer_id 
            WHERE v.vehicle_id = $vehicle_id";
            
    $result = $conn->query($sql);

    // Kiểm tra xem có tìm thấy xe không
    if ($result->num_rows > 0) {
        $car = $result->fetch_assoc();
    } else {
        // Nếu ID không tồn tại trong DB
        echo "<div style='text-align:center; padding:50px;'>
                <h2>Không tìm thấy xe này!</h2>
                <a href='index.php'>Quay lại trang chủ</a>
              </div>";
        exit();
    }
} else {
    // Nếu không có tham số ?id=...
    header("Location: index.php"); // Chuyển hướng về trang chủ
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $car['model'] ?> - Chi tiết xe</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="./assets/css/information.css">
</head>
<body>

    <div class="product-wrapper">
        
        <div class="w-100 px-2 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted small">Home</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted small"><?= $car['manufacturer_name'] ?></a></li>
                    <li class="breadcrumb-item active text-dark fw-bold small"><?= $car['model'] ?></li>
                </ol>
            </nav>
        </div>

        <div class="col-img">
            <div class="main-image-container">
                <img src="<?= !empty($car['image_url']) ? $car['image_url'] : 'https://via.placeholder.com/600x400?text=No+Image' ?>" 
                     alt="<?= $car['model'] ?>"
                     onerror="this.src='https://via.placeholder.com/600x400?text=Image+Not+Found';">
            </div>
        </div>

        <div class="col-info">
            
            <div class="brand-header">
                <span><i class='bx bxs-check-circle'></i> <?= $car['manufacturer_name'] ?> Official</span>
                
                <?php if ($car['stock'] > 0): ?>
                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-normal">Available</span>
                <?php else: ?>
                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-normal">Out of Stock</span>
                <?php endif; ?>
            </div>

            <h1 class="product-title"><?= $car['model'] ?></h1>

            <div class="d-flex align-items-center mb-4">
                <i class='bx bxs-star text-warning'></i>
                <i class='bx bxs-star text-warning'></i>
                <i class='bx bxs-star text-warning'></i>
                <i class='bx bxs-star text-warning'></i>
                <i class='bx bxs-star-half text-warning'></i>
                <span class="text-muted ms-2 small">(Reviews)</span>
            </div>

            <div class="product-price">
                $<?= number_format($car['price'], 0, ',', '.') ?>
            </div>

            <form action="cart_add.php" method="POST">
                <input type="hidden" name="vehicle_id" value="<?= $car['vehicle_id'] ?>">

                <div class="mb-4">
                    <span class="color-label">Color Option: <span class="fw-normal text-muted" id="color-name">Standard</span></span>
                    <div class="color-options">
                        <label>
                            <input type="radio" name="color" value="Standard" class="color-radio" checked>
                            <span class="color-circle" style="background-color: #fff;" onclick="document.getElementById('color-name').innerText='White'"></span>
                        </label>
                        <label>
                            <input type="radio" name="color" value="Black" class="color-radio">
                            <span class="color-circle" style="background-color: #000;" onclick="document.getElementById('color-name').innerText='Black'"></span>
                        </label>
                        <label>
                            <input type="radio" name="color" value="Red" class="color-radio">
                            <span class="color-circle" style="background-color: #d63031;" onclick="document.getElementById('color-name').innerText='Red'"></span>
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <span class="color-label">Specifications</span>
                    <div class="specs-grid">
                        <div class="spec-box">
                            <span>Year</span>
                            <strong><?= $car['year'] ?></strong>
                        </div>
                        <div class="spec-box">
                            <span>Origin</span>
                            <strong><?= $car['country'] ?></strong>
                        </div>
                        <div class="spec-box">
                            <span>Stock</span>
                            <strong><?= $car['stock'] ?></strong>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <?php if ($car['stock'] > 0): ?>
                        <button type="submit" class="btn-black">
                            <i class='bx bx-shopping-bag'></i> Add to Cart
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn-black" style="background-color: #ccc; cursor: not-allowed;" disabled>
                            Sold Out
                        </button>
                    <?php endif; ?>
                    
                    <button type="button" class="btn-wishlist">
                        <i class='bx bx-heart'></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="w-100 mt-5 pt-4 border-top">
            <h5 class="fw-bold mb-3">Description</h5>
            <div class="text-secondary" style="line-height: 1.8; font-size: 15px;">
                <?= nl2br($car['description']) ?>
            </div>
        </div>

    </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>