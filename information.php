<?php
// Kết nối database 
require_once 'db.php';

// Kiểm tra xem trên thanh địa chỉ (URL) có tham số 'id' hay không 
if (isset($_GET['id'])) {
    // Lấy giá trị id và ép kiểu về số nguyên (intval) để tránh lỗi SQL Injection cơ bản
    $id = intval($_GET['id']);

    // Chuẩn bị SQL
    $sql = "SELECT v.*, m.name AS manufacturer_name, m.country 
            FROM vehicle v 
            JOIN manufacturer m ON v.manufacturer_id = m.manufacturer_id 
            WHERE v.vehicle_id = $id";
            
    // Thực thi câu lệnh truy vấn
    $result = $conn->query($sql);

    // Nếu tìm thấy dữ liệu (số dòng > 0)
    if ($result->num_rows > 0) {
        // Lấy dữ liệu dòng đầu tiên gán vào biến $car dưới dạng mảng kết hợp
        $car = $result->fetch_assoc();
    } else {
        // Nếu không tìm thấy xe với ID đó, hiển thị thông báo lỗi và nút quay về
        echo "<div class='container py-5 text-center'>
                <h2>Vehicle not found!</h2>
                <a href='base.php?page=home' class='btn btn-primary mt-3'>Back to Home</a>
              </div>";
        exit(); // Dừng chạy code phía sau
    }
} else {
    // Nếu không có tham số ID trên URL, chuyển hướng người dùng về trang chủ
    header("Location: base.php?page=home");
    exit();
}
?>

<body>
    <div class="container mt-5">
        <div class="product-wrapper row bg-white rounded shadow-sm p-4">
            
            <div class="col-12 mb-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="base.php?page=home" class="text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($car['manufacturer_name']) ?></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($car['model']) ?></li>
                    </ol>
                </nav>
            </div>

            <div class="col-md-7 mb-4">
                <div id="mediaViewer"
                    class="main-media-container position-relative d-flex align-items-center justify-content-center bg-light rounded"
                    style="width: 500px; height: 500px; margin: auto; overflow:hidden;">

                    <?php 
                        // Tạo mảng $media để chứa danh sách ảnh/video
                        $media = []; 
                        
                        // Nếu trong database có link video, thêm vào đầu mảng
                        if (!empty($car['video_url'])) {
                            $media[] = ['type' => 'video', 'url' => $car['video_url']];
                        }
                        
                        // Luôn thêm ảnh vào mảng (dùng ảnh placeholder nếu không có ảnh thật)
                        $media[] = [
                            'type' => 'image',
                            'url' => !empty($car['image_url']) ? $car['image_url'] : 'https://via.placeholder.com/600x400'
                        ];
                    ?>

                    <div id="mediaContent" 
                        style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                        
                        <?php if ($media[0]['type'] === 'video'): ?>
                            <video autoplay muted loop style="height:100%; width:auto; object-fit:cover;">
                                <source src="<?= $media[0]['url'] ?>" type="video/mp4">
                            </video>
                        <?php else: ?>
                            <img src="<?= $media[0]['url'] ?>" 
                                style="max-width:100%; max-height:100%; object-fit:contain;">
                        <?php endif; ?>

                    </div>

                    <div class="position-absolute start-0 top-50 translate-middle-y">
                        <i class="bx bx-chevron-left fs-1 text-dark" 
                           role="button" 
                           onclick="prevMedia()"></i>
                    </div>
                    
                    <div class="position-absolute end-0 top-50 translate-middle-y">
                        <i class="bx bx-chevron-right fs-1 text-dark" 
                           role="button" 
                           onclick="nextMedia()"></i>
                    </div>

                </div>
            </div>

            <script>
                // Chuyển mảng PHP $media thành mảng JSON để JavaScript sử dụng được
                const mediaList = <?= json_encode($media) ?>;
                
                // Biến theo dõi vị trí media đang hiển thị
                let currentIndex = 0;

                // Hàm vẽ lại nội dung bên trong div #mediaContent
                function renderMedia() {
                    const container = document.getElementById('mediaContent');
                    const item = mediaList[currentIndex];
                    
                    // Xóa nội dung cũ
                    container.innerHTML = "";
                    
                    // Nếu là VIDEO: Chèn thẻ <video>
                    if (item.type === "video") {
                        container.innerHTML = `
                             <video autoplay muted loop style="height:100%; width:auto; object-fit:cover;">
                                <source src="<?= $media[0]['url'] ?>" type="video/mp4">
                            </video>
                        `;
                    } 
                    // Nếu là ẢNH: Chèn thẻ <img>
                    else {
                        container.innerHTML = `
                            <img src="${item.url}"
                                style="max-width:100%; max-height:100%; object-fit:contain;">
                        `;
                    }
                }

                // Hàm xử lý khi bấm nút Next (tăng index, quay về 0 nếu hết mảng)
                function nextMedia() {
                    currentIndex = (currentIndex + 1) % mediaList.length;
                    renderMedia();
                }

                // Hàm xử lý khi bấm nút Prev (giảm index, quay về cuối mảng nếu < 0)
                function prevMedia() {
                    currentIndex = (currentIndex - 1 + mediaList.length) % mediaList.length;
                    renderMedia();
                }
            </script>

            <div class="col-md-5">
                <form action="cart_add.php" method="POST"> 
                    <input type="hidden" name="vehicle_id" value="<?= $car['vehicle_id'] ?>">
                    
                    <div class="brand-header d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted"><i class='bx bxs-check-circle text-primary'></i> <?= htmlspecialchars($car['manufacturer_name']) ?> Official</span>
                        
                        <?php if ($car['stock'] > 0): ?>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">In Stock</span>
                        <?php else: ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Out of Stock</span>
                        <?php endif; ?>
                    </div>

                    <h2 class="fw-bold mb-3"><?= htmlspecialchars($car['model']) ?></h2>

                    <h3 class="text-danger fw-bold mb-4">
                        $<?= number_format($car['price'], 0, '.', ',') ?>
                    </h3>

                    <div class="row mb-4 text-center">
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <small class="text-muted d-block">Year</small>
                                <strong><?= $car['year'] ?></strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <small class="text-muted d-block">Origin</small>
                                <strong><?= htmlspecialchars($car['country']) ?></strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <small class="text-muted d-block">Stock</small>
                                <strong><?= $car['stock'] ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-5 pt-4 border-top">
                        <h4 class="fw-bold mb-3">Product Description</h4>
                        <div class="text-secondary" style="line-height: 1.8; white-space: pre-line;">
                            <?= $car['description'] ? htmlspecialchars($car['description']) : "Description updating..." ?>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="d-flex gap-2">
                            <?php if (isset($_SESSION['customer_id'])): ?>
                                
                                <?php if ($car['stock'] > 0): ?>
                                    <button type="submit" class="btn btn-warning flex-grow-1 py-3 fw-bold rounded-pill">
                                        <i class='bx bx-cart-add fs-4 align-middle'></i> Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-secondary flex-grow-1 py-3 fw-bold rounded-pill text-uppercase" disabled>
                                        SOLD OUT
                                    </button>
                                <?php endif; ?>
                                
                            <?php else: ?>
                                <a href="login.php" class="btn btn-warning flex-grow-1 py-3 fw-bold rounded-pill">
                                    Login to Add to Cart
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form> 
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>