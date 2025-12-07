<?php
// ==========================================
// PHẦN 1: KẾT NỐI DATABASE (Nằm chung ở đây)
// ==========================================
$servername = "localhost";
$username   = "root";
$password   = "";             // Mật khẩu XAMPP mặc định thường để trống
$dbname     = "car_showroom"; // TÊN DATABASE CỦA BẠN (Kiểm tra kỹ cái này!)

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// ==========================================
// PHẦN 2: LẤY DỮ LIỆU TỪ 3 BẢNG
// ==========================================

// Lấy ID xe từ thanh địa chỉ (ví dụ: information.php?id=1)
// Nếu không có ID thì mặc định là 0
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Câu lệnh SQL "thần thánh" nối 3 bảng dựa trên sơ đồ bạn gửi
$sql = "SELECT 
            v.vehicle_id, 
            v.sale_price, 
            v.quantity, 
            v.color,
            vm.name AS model_name, 
            vm.trim, 
            vm.body_type, 
            vm.year, 
            vm.base_price,
            -- Lưu ý: Bạn cần chắc chắn bảng vehicle_model có cột 'image' và 'description'
            -- Nếu chưa có thì vào phpMyAdmin thêm vào nhé, hoặc sửa tên cột dưới đây cho đúng
            vm.image,  
            vm.description,
            m.name AS brand_name, 
            m.country
        FROM vehicle v
        JOIN vehicle_model vm ON v.model_id = vm.model_id
        JOIN manufacturer m ON vm.manufacturer_id = m.manufacturer_id
        WHERE v.vehicle_id = $id";

$result = $conn->query($sql);

// Kiểm tra xem có tìm thấy xe không
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    // Nếu không tìm thấy xe, gán giá trị rỗng hoặc báo lỗi nhẹ
    // Để tránh web bị trắng trang, mình sẽ tạo dữ liệu giả để bạn test giao diện
    $row = [
        'brand_name' => 'Không tìm thấy',
        'model_name' => 'Sản phẩm',
        'trim' => '',
        'sale_price' => 0,
        'base_price' => 0,
        'quantity' => 0,
        'color' => 'N/A',
        'year' => 'N/A',
        'body_type' => 'N/A',
        'country' => 'N/A',
        'image' => 'default.png', // Ảnh mặc định
        'description' => 'Không tìm thấy dữ liệu xe này trong Database.'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $row['brand_name'] . ' ' . $row['model_name']; ?> - Chi tiết</title>
    
    <link rel="stylesheet" href="information.css"> 
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

    <div class="product-wrapper">
        
        <div class="col-img">
            <div class="main-image-container">
                <img src="img/<?php echo $row['image']; ?>" alt="Car Image" onerror="this.src='img/default.png'">
            </div>
        </div>

        <div class="col-info">
            <div class="brand-header">
                <span><?php echo $row['brand_name']; ?> Official</span>
                
                <?php if($row['quantity'] > 0): ?>
                    <span style="background:#e8f5e9; color:#2e7d32; padding:4px 12px; border-radius:20px; font-size:12px;">Available</span>
                <?php else: ?>
                    <span style="background:#ffebee; color:#c62828; padding:4px 12px; border-radius:20px; font-size:12px;">Out of Stock</span>
                <?php endif; ?>
            </div>

            <h1 class="product-title">
                <?php echo $row['brand_name'] . ' ' . $row['model_name']; ?> 
                <span style="font-weight:300; color:#888; font-size: 0.8em;"><?php echo $row['trim']; ?></span>
            </h1>

            <div class="review-stars">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                <span style="color:#888; margin-left:5px;">(New Arrival)</span>
            </div>

            <div class="product-price">
                $<?php echo number_format($row['sale_price']); ?>
                
                <?php if($row['base_price'] > $row['sale_price']): ?>
                    <span style="font-size:18px; color:#aaa; text-decoration:line-through; font-weight:400; margin-left:10px;">
                        $<?php echo number_format($row['base_price']); ?>
                    </span>
                <?php endif; ?>
            </div>

            <span class="color-label">Selected Color: <strong><?php echo $row['color']; ?></strong></span>
            <div class="color-options">
                <label>
                    <input type="radio" name="color" class="color-radio" checked>
                    <div class="color-circle" style="background-color: #333;"></div>
                </label>
            </div>

            <div class="specs-grid">
                <div class="spec-box">
                    <span>Year</span>
                    <strong><?php echo $row['year']; ?></strong>
                </div>
                <div class="spec-box">
                    <span>Body Type</span>
                    <strong><?php echo $row['body_type']; ?></strong>
                </div>
                <div class="spec-box">
                    <span>Origin</span>
                    <strong><?php echo $row['country']; ?></strong>
                </div>
            </div>

            <p style="font-size: 13px; color: #666; margin-bottom: 25px;">
                <i class="fas fa-box-open"></i> Stock Quantity: <strong><?php echo $row['quantity']; ?> vehicles</strong>
            </p>

            <div class="action-buttons">
                <button class="btn-black">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
                <button class="btn-wishlist">
                    <i class="far fa-heart"></i>
                </button>
            </div>
        </div>
    </div>

    <div style="max-width: 1200px; margin: 0 auto; padding: 0 40px;">
        <h3>Description</h3>
        <p style="color:#666; line-height:1.6;">
            <?php 
                // Nếu có mô tả thì hiện, không thì hiện chữ mặc định
                if (!empty($row['description'])) {
                    echo $row['description']; 
                } else {
                    echo "No description available for this vehicle.";
                }
            ?>
        </p>
    </div>

</body>
</html>