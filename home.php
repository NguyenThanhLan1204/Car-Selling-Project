<?php
// FILE: home.php
// Hãy xóa sạch nội dung cũ và paste đoạn này vào

include("db.php");

// --- CẤU HÌNH DEBUG (Khi nào chạy ngon thì sửa thành false) ---
$show_debug = false; 
// -------------------------------------------------------------

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Nhận từ khóa tìm kiếm
$keyword = '';
$search_condition = '';
$is_searching = false;

if (isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
    $is_searching = true;
    $raw_keyword = trim($_GET['keyword']);
    $keyword = $conn->real_escape_string($raw_keyword); // Xử lý bảo mật
    
    // Tìm trong Tên xe (model), Hãng (manufacturer.name), hoặc Mô tả
    $search_condition = " AND (v.model LIKE '%$keyword%' 
                          OR m.name LIKE '%$keyword%' 
                          OR v.description LIKE '%$keyword%'
                          OR v.year LIKE '%$keyword%') ";
}

// 2. Câu lệnh SQL (Kết hợp bảng Vehicle và Manufacturer)
// Lưu ý: Đã khớp với database bạn gửi (m.name, v.model...)
$sql = "SELECT v.vehicle_id, v.model, v.year, v.price, v.image_url, v.description, m.manufacturer_id, m.name AS manufacturer
        FROM vehicle v
        JOIN manufacturer m ON v.manufacturer_id = m.manufacturer_id
        WHERE 1=1 $search_condition 
        ORDER BY m.manufacturer_id, v.model";

// --- DEBUG: Kiểm tra xem SQL có chạy đúng không ---
if ($show_debug && $is_searching) {
    echo "<div style='background: #fff3cd; color: #856404; padding: 15px; margin: 10px; border: 1px solid #ffeeba;'>";
    echo "<strong>🔍 ĐANG TÌM KIẾM:</strong> " . htmlspecialchars($raw_keyword) . "<br>";
    echo "<strong>📜 SQL QUERY:</strong> " . $sql;
    echo "</div>";
}
// ------------------------------------------------

$result = $conn->query($sql);

$cars = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cars[$row['manufacturer']][] = $row;
    }
}
?>

<body>
    <?php if (!$is_searching): ?>
    <div class="slider position-relative w-100">
        <div class="text-content position-relative">
            <div class="slide">           
                <div class="img">
                    <video autoplay muted loop class="d-block w-75 mx-auto p-3">
                        <source src="./assets/video/Recording 2025-12-10 213401.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <div class="info-content position-absolute start-50 translate-middle-x text-center text-white z-1">
                    <h2 class="text-heading">Car World</h2>
                    <div class="text-description">Find your dream car at the best price today</div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="container py-5">
        
        <?php if ($is_searching): ?>
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                <h3 class="fw-bold">Search Result: <span class="text-primary">"<?= htmlspecialchars($raw_keyword) ?>"</span></h3>
                <a href="base.php?page=home" class="btn btn-outline-danger btn-sm">
                    <i class='bx bx-x'></i> Clear Search
                </a>
            </div>
        <?php else: ?>
            <h2 class="text-center fw-bold mb-4">DISCOVER OUR MODELS</h2>
        <?php endif; ?>
      
        <?php if (empty($cars)): ?>
            <div class="text-center py-5">
                <i class='bx bx-search fs-1 text-muted mb-3'></i>
                <h4 class="text-muted">No vehicles found matching your criteria.</h4>
                <p>Try searching for brand names like "Audi", "BMW" or model names.</p>
                <a href="base.php?page=home" class="btn btn-dark mt-3">View All Cars</a>
            </div>
        <?php else: ?>

            <ul class="nav nav-tabs justify-content-center mb-4" id="carTabs" role="tablist">
            <?php 
            $first = true;
            foreach ($cars as $brand => $list) {
                // Xử lý ID tab: Xóa khoảng trắng (ví dụ "Land Rover" -> "LandRover")
                $id = preg_replace('/\s+/', '', $brand);
                
                echo '<li class="nav-item">';
                echo '<button class="nav-link '.($first?'active':'').' fw-bold text-uppercase" data-bs-toggle="tab" data-bs-target="#'.$id.'" type="button">'
                        .$brand. ' <span class="badge bg-secondary rounded-pill ms-1">'.count($list).'</span>'.
                     '</button>';
                echo '</li>';
                $first = false;
            }
            ?>
            </ul>
        
            <div class="tab-content" id="carTabsContent">
                <?php 
                $first = true;
                foreach ($cars as $brand => $list) {
                    $id = preg_replace('/\s+/', '', $brand);

                    echo '<div class="tab-pane fade '.($first?'show active':'').'" id="'.$id.'" role="tabpanel">';
                    echo '<div class="row g-4 justify-content-center">';
                    
                    foreach ($list as $car) {
                        echo '<div class="col-md-4 col-sm-6">';
                        echo '  <div class="card h-100 shadow-sm border-0">';
                        
                        // Xử lý ảnh: nếu không có ảnh thì hiện ảnh placeholder (tùy chọn)
                        $img = !empty($car['image_url']) ? $car['image_url'] : 'https://via.placeholder.com/300x200?text=No+Image';
                        
                        echo '    <div class="position-relative">';
                        echo '      <img src="'.$img.'" class="card-img-top" style="height:220px; object-fit:cover;" alt="'.$car['model'].'">';
                        echo '      <div class="position-absolute top-0 end-0 m-2 badge bg-primary">'.$car['year'].'</div>';
                        echo '    </div>';

                        echo '    <div class="card-body d-flex flex-column">';
                        echo '      <h5 class="card-title fw-bold text-dark">'.$car['model'].'</h5>';
                        echo '      <p class="text-muted small mb-2">'.$brand.'</p>';
                        
                        // Hiển thị mô tả ngắn (cắt bớt nếu dài quá)
                        $short_desc = strlen($car['description']) > 50 ? substr($car['description'], 0, 50) . '...' : $car['description'];
                        echo '      <p class="card-text text-secondary small flex-grow-1">'.$short_desc.'</p>';
                        
                        echo '      <div class="mt-auto border-top pt-3">';
                        echo '        <div class="d-flex justify-content-between align-items-center mb-3">';
                        echo '           <span class="fw-bold text-danger fs-5">'.number_format($car['price'],0,',','.').' $</span>';
                        echo '        </div>';
                        echo '        <a href="base.php?page=information&id='.$car['vehicle_id'].'" class="btn btn-dark w-100 fw-bold">Order Now</a>';
                        echo '      </div>';
                        echo '    </div>';
                        echo '  </div>';
                        echo '</div>';
                    }
                    
                    echo '</div>';
                    echo '</div>';
                    $first = false;
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</body>