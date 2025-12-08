<?php
// Gọi file kết nối database (dùng chung file db.php của Client)
require_once 'db.php';

// Kiểm tra giỏ hàng có trống không
$cart_content = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$list_ids = array_keys($cart_content); // Lấy danh sách ID các xe trong giỏ: [1, 5, ...]

$grand_total = 0; // Tổng tiền cả giỏ hàng

?>

<div class="cart container py-5">
    <div class="row g-4">
        <div class="col-lg-9">
            <h3 class="mb-4 fw-bold">Giỏ hàng của bạn</h3>
            
            <?php if (empty($list_ids)): ?>
                <div class="alert alert-warning text-center">
                    Giỏ hàng đang trống! <a href="index.php?page=home" class="fw-bold">Mua sắm ngay</a>
                </div>
            <?php else: ?>
                
                <?php
                // Chuyển mảng ID thành chuỗi để query: 1,5,7
                $ids_str = implode(',', $list_ids);
                
                // Lấy thông tin chi tiết các xe đang có trong giỏ
                $sql = "SELECT * FROM vehicle WHERE vehicle_id IN ($ids_str)";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()):
                    $curr_id = $row['vehicle_id'];
                    $qty = $cart_content[$curr_id]['qty']; // Lấy số lượng từ session
                    $color = $cart_content[$curr_id]['color']; // Lấy màu từ session
                    
                    $subtotal = $row['price'] * $qty;
                    $grand_total += $subtotal;
                ?>
                
                <div class="card mb-3 shadow-sm">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-3">
                            <img src="<?= !empty($row['image_url']) ? $row['image_url'] : 'https://via.placeholder.com/150' ?>" 
                                 class="img-fluid rounded-start h-100 object-fit-cover" 
                                 alt="<?= $row['model'] ?>"
                                 style="max-height: 150px; width: 100%;">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body position-relative">
                                <h5 class="card-title fw-bold"><?= $row['model'] ?></h5>
                                <p class="card-text mb-1 text-muted">Màu sắc: <strong><?= $color ?></strong></p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <span class="text-secondary">Đơn giá: $<?= number_format($row['price']) ?></span> <br>
                                        <span class="fw-bold">Số lượng: <?= $qty ?></span>
                                    </div>
                                    <div>
                                        <p class="card-text fw-bold fs-5 text-primary mb-0">
                                            $<?= number_format($subtotal) ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <a href="cart_delete.php?id=<?= $curr_id ?>" 
                                   class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-3"
                                   onclick="return confirm('Bạn có chắc muốn xóa xe này?')">
                                    <i class='bx bx-trash'></i> Xóa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>

            <?php endif; ?>

        </div>
    
        <?php if (!empty($list_ids)): ?>
        <div class="col-lg-3">
            <div class="p-4 shadow-sm bg-white rounded position-sticky" style="top: 100px;">
                <h5 class="fw-bold mb-3">Tóm tắt đơn hàng</h5>
        
                <div class="mb-3">
                    <label class="d-flex justify-content-between align-items-center mb-2">
                        <span>Tạm tính:</span>
                        <strong>$<?= number_format($grand_total) ?></strong>
                    </label>
                    <label class="d-flex justify-content-between align-items-center text-success">
                        <span>Vận chuyển:</span>
                        <strong>Miễn phí</strong>
                    </label>
                </div>
                
                <hr>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fs-5 fw-bold">Tổng cộng:</span>
                    <span class="fs-4 fw-bold text-danger">$<?= number_format($grand_total) ?></span>
                </div>
        
                <?php if (isset($_SESSION['username'])): ?>
                    <form action="checkout.php" method="POST">
                        <button class="btn btn-dark w-100 py-2 fw-bold">THANH TOÁN NGAY</button>
                    </form>
                <?php else: ?>
                    <a href="login.php" class="btn btn-warning w-100 py-2 fw-bold">Đăng nhập để thanh toán</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
