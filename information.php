<?php
// Giả định kết nối database (bạn thay bằng file kết nối thực tế của bạn)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_selling"; // Tên DB của bạn

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}

// Lấy ID xe từ URL (ví dụ: index.php?page=information&id=1)
// Kiểm tra tham số 'id' thay vì 'page'
if (isset($_GET['id'])) {
    $vehicle_id = intval($_GET['id']);

    // Truy vấn thông tin xe kèm tên hãng (JOIN bảng manufacturer)
    $sql = "SELECT v.*, m.name AS manufacturer_name, m.country 
            FROM vehicle v 
            JOIN manufacturer m ON v.manufacturer_id = m.manufacturer_id 
            WHERE v.vehicle_id = $vehicle_id";
            
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $car = $result->fetch_assoc();
    } else {
        echo "<div class='container py-5'><h3>Không tìm thấy thông tin xe này.</h3></div>";
        exit();
    }
} else {
    // Nếu không có ID thì chuyển về trang chủ hoặc báo lỗi
    echo "<script>window.location.href='?page=home';</script>";
    exit();
}
?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=home" class="text-decoration-none text-dark">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Xe</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $car['model'] ?></li>
        </ol>
    </nav>

    <div class="row g-5 mt-2">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <img src="<?= !empty($car['image_url']) ? $car['image_url'] : './assets/img/placeholder.jpg' ?>" 
                     class="img-fluid rounded" 
                     alt="<?= $car['model'] ?>"
                     style="width: 100%; object-fit: cover;">
            </div>
        </div>

        <div class="col-lg-6">
            <h2 class="fw-bold text-uppercase"><?= $car['model'] ?></h2>
            <div class="mb-3">
                <span class="badge bg-secondary me-2"><?= $car['manufacturer_name'] ?></span>
                <span class="text-muted">Xuất xứ: <?= $car['country'] ?></span>
            </div>
            
            <h3 class="fw-bold text-danger mb-3">
                $<?= number_format($car['price'], 0, ',', '.') ?>
            </h3>

            <p class="mb-4 text-secondary">
                Năm sản xuất: <strong><?= $car['year'] ?></strong> <br>
                Tình trạng: 
                <?php if ($car['stock'] > 0): ?>
                    <span class="text-success fw-bold">Còn hàng (<?= $car['stock'] ?> xe)</span>
                <?php else: ?>
                    <span class="text-danger fw-bold">Hết hàng</span>
                <?php endif; ?>
            </p>

            <hr class="my-4">

            <form action="cart_add.php" method="POST">
                <input type="hidden" name="vehicle_id" value="<?= $car['vehicle_id'] ?>">
                
                <div class="mb-3">
                    <label for="color" class="form-label fw-bold">Chọn màu sắc:</label>
                    <select class="form-select" name="color" id="color">
                        <option value="Trắng">Trắng (White)</option>
                        <option value="Đen">Đen (Black)</option>
                        <option value="Đỏ">Đỏ (Red)</option>
                        <option value="Xanh">Xanh (Blue)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="quantity" class="form-label fw-bold">Số lượng:</label>
                    <div class="d-flex align-items-center">
                        <input type="number" name="quantity" class="form-control w-25 text-center me-3" value="1" min="1" max="<?= $car['stock'] ?>">
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <?php if ($car['stock'] > 0): ?>
                        <button type="submit" class="btn btn-dark btn-lg">
                            <i class='bx bx-cart-add me-2'></i> THÊM VÀO GIỎ HÀNG
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-secondary btn-lg" disabled>TẠM HẾT HÀNG</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold m-0">Thông tin chi tiết & Thông số kỹ thuật</h5>
                </div>
                <div class="card-body">
                    <div class="card-text text-secondary" style="line-height: 1.8;">
                        <?= nl2br($car['description']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>