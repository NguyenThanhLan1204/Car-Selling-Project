<?php
// Khởi tạo session an toàn (chỉ khi chưa có)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Kết nối database
require_once 'db.php';

// Kiểm tra tham số ID trên URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Truy vấn thông tin xe + hãng sản xuất
    $sql = "SELECT v.*, m.name AS manufacturer_name, m.country 
            FROM vehicle v 
            JOIN manufacturer m ON v.manufacturer_id = m.manufacturer_id 
            WHERE v.vehicle_id = $id";
            
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $car = $result->fetch_assoc();
    } else {
        // Không tìm thấy xe
        echo "<div class='container py-5 text-center'>
                <h2>Không tìm thấy xe yêu cầu!</h2>
                <a href='index.php' class='btn btn-primary mt-3'>Về trang chủ</a>
              </div>";
        exit();
    }
} else {
    // Không có ID -> Về trang chủ
    header("Location: index.php");
    exit();
}
?>
<body>

    <div class="container mt-5">
        <div class="product-wrapper row bg-white rounded shadow-sm p-4">
            
            <div class="col-12 mb-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="base.php?page=home" class="text-decoration-none">Trang chủ</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $car['manufacturer_name'] ?></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $car['model'] ?></li>
                    </ol>
                </nav>
            </div>

            <div class="col-md-7 mb-4">
                <div class="main-image-container d-flex align-items-center justify-content-center bg-light rounded" style="min-height: 400px;">
                    <img src="<?= !empty($car['image_url']) ? $car['image_url'] : 'https://via.placeholder.com/600x400' ?>" 
                         class="img-fluid rounded shadow-sm"
                         alt="<?= $car['model'] ?>"
                         style="max-height: 400px;">
                </div>
            </div>

            <div class="col-md-5">
                <form action="cart_add.php" method="POST">
                    
                    <input type="hidden" name="vehicle_id" value="<?= $car['vehicle_id'] ?>">

                    <div class="brand-header d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted"><i class='bx bxs-check-circle text-primary'></i> <?= $car['manufacturer_name'] ?> Chính hãng</span>
                        <?php if ($car['stock'] > 0): ?>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Còn hàng</span>
                        <?php else: ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Hết hàng</span>
                        <?php endif; ?>
                    </div>

                    <h2 class="fw-bold mb-3"><?= $car['model'] ?></h2>

                    <div class="mb-3">
                        <i class='bx bxs-star text-warning'></i>
                        <i class='bx bxs-star text-warning'></i>
                        <i class='bx bxs-star text-warning'></i>
                        <i class='bx bxs-star text-warning'></i>
                        <i class='bx bxs-star-half text-warning'></i>
                        <small class="text-muted">(Đánh giá khách hàng)</small>
                    </div>

                    <h3 class="text-danger fw-bold mb-4">
                        $<?= number_format($car['price'], 0, ',', '.') ?>
                    </h3>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Màu sắc: <span id="color-name" class="fw-normal">Tiêu chuẩn</span></label>
                        <div class="d-flex gap-3">
                            <label>
                                <input type="radio" name="color" value="Trắng" class="color-radio" checked onclick="document.getElementById('color-name').innerText='Trắng'">
                                <span class="color-circle shadow-sm" style="background-color: #fff;" title="Trắng"></span>
                            </label>
                            <label>
                                <input type="radio" name="color" value="Đen" class="color-radio" onclick="document.getElementById('color-name').innerText='Đen'">
                                <span class="color-circle shadow-sm" style="background-color: #000;" title="Đen"></span>
                            </label>
                            <label>
                                <input type="radio" name="color" value="Đỏ" class="color-radio" onclick="document.getElementById('color-name').innerText='Đỏ'">
                                <span class="color-circle shadow-sm" style="background-color: #d63031;" title="Đỏ"></span>
                            </label>
                            <label>
                                <input type="radio" name="color" value="Xanh" class="color-radio" onclick="document.getElementById('color-name').innerText='Xanh'">
                                <span class="color-circle shadow-sm" style="background-color: #0984e3;" title="Xanh"></span>
                            </label>
                        </div>
                    </div>

                    <div class="row mb-4 text-center">
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <small class="text-muted d-block">Năm SX</small>
                                <strong><?= $car['year'] ?></strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <small class="text-muted d-block">Xuất xứ</small>
                                <strong><?= $car['country'] ?></strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <small class="text-muted d-block">Kho</small>
                                <strong><?= $car['stock'] ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <!-- NOTE: sửa chỗ thêm vào giỏ -->
                        <?php if (isset($_SESSION['username'])): ?>
                            <?php if ($car['stock'] > 0): ?>
                                <button type="submit" class="btn btn-dark flex-grow-1 py-3 fw-bold rounded-pill">
                                    <i class='bx bx-cart-add fs-4 align-middle'></i> Thêm vào giỏ
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-secondary flex-grow-1 py-3 fw-bold rounded-pill" disabled>
                                    Tạm hết hàng
                                </button>
                            <?php endif; ?>
                            
                            <button type="button" class="btn btn-outline-danger px-4 rounded-pill">
                                <i class='bx bx-heart fs-4'></i>
                            </button>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-dark flex-grow-1 py-3 fw-bold rounded-pill">
                                Đăng nhập để thêm vào giỏ
                            </a>
                        <?php endif; ?>
                    </div>

                </form> 
                </div>

            <div class="col-12 mt-5 pt-4 border-top">
                <h4 class="fw-bold mb-3">Mô tả sản phẩm</h4>
                <div class="text-secondary" style="line-height: 1.8; white-space: pre-line;">
                    <?= $car['description'] ? $car['description'] : "Đang cập nhật mô tả..." ?>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>