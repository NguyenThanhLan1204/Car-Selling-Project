<?php 
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php?message=please_login");
    exit();
}
?>

<?php
require_once 'db.php';

$cart_content = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$list_ids = array_keys($cart_content);
$grand_total = 0;
?>

<div class="cart container py-5">
    <div class="row g-4">
        <div class="col-lg-9">
            <h3 class="mb-4 fw-bold">Your shopping cart</h3>
            <?php if (empty($list_ids)): ?>
                <div class="alert alert-warning text-center">
                    The shopping cart is empty! <a href="base.php?page=home" class="fw-bold">Shop now</a>
                </div>
            <?php else: ?>
                <?php
                // CHỈ THỰC HIỆN TRUY VẤN CSDL KHI GIỎ HÀNG KHÔNG RỖNG (Sửa lỗi Fatal Error SQL)
                $ids_str = implode(',', $list_ids);
                $sql = "SELECT * FROM vehicle WHERE vehicle_id IN ($ids_str)";
                $result = $conn->query($sql);
                
                while ($row = $result->fetch_assoc()):
                    $curr_id = $row['vehicle_id'];
                    $qty = $cart_content[$curr_id]['qty'];
                    $color = $cart_content[$curr_id]['color'];
                    $subtotal = $row['price'] * $qty;
                    $grand_total += $subtotal;
                ?>
                <div class="card mb-3 shadow-sm product-row" data-id="<?= $curr_id ?>" data-price="<?= $row['price'] ?>">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-3">
                            <img src="<?= !empty($row['image_url']) ? $row['image_url'] : 'https://via.placeholder.com/150' ?>" class="img-fluid rounded-start h-100 object-fit-cover" alt="<?= $row['model'] ?>" style="max-height: 150px; width: 100%;">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body position-relative">
                                <h5 class="card-title fw-bold"><?= $row['model'] ?></h5>
                                <p class="card-text mb-1 text-muted small">Color: <strong><?= $color ?></strong></p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="d-flex flex-column">
                                        <label class="small text-muted mb-1">Quantity</label>
                                        <input type="number" 
                                               class="form-control text-center qty-input" 
                                               value="<?= $qty ?>" 
                                               min="1" 
                                               data-id="<?= $curr_id ?>"
                                               style="width: 80px; text-align: center;">
                                    </div>
                                    <div class="text-end">
                                        <p class="small text-muted mb-0">Subtotal</p>
                                        <p class="card-text fw-bold fs-5 text-primary mb-0 subtotal-display">
                                            $<?= number_format($subtotal) ?>
                                        </p>
                                    </div>
                                </div>
                            <a href="cart_delete.php?id=<?= $curr_id ?>" 
                            class="btn btn-sm text-danger position-absolute top-0 end-0 m-3" 
                            title="Remove item"
                            onclick="return confirm('Are you sure you want to remove this vehicle from your cart?')">
                                <i class='bx bx-trash'></i> Remove
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
                <h5 class="fw-bold mb-4 border-bottom pb-2">Cart summary</h5>
                <div class="mb-4">
                    <p class="small fw-bold text-muted mb-2 text-uppercase">Shipping</p>
                    <div class="form-check border rounded p-2 mb-2">
                        <input class="form-check-input ms-0 me-2 shipping-radio" type="radio" name="shipping" id="ship-normal" value="0" checked>
                        <label class="form-check-label d-flex justify-content-between w-100" for="ship-normal">
                            <span>Free shipping</span>
                            <span class="fw-bold">$0</span>
                        </label>
                    </div>
                    <div class="form-check border rounded p-2">
                        <input class="form-check-input ms-0 me-2 shipping-radio" type="radio" name="shipping" id="ship-fast" value="15">
                        <label class="form-check-label d-flex justify-content-between w-100" for="ship-fast">
                            <span>Express shipping</span>
                            <span class="fw-bold">$15</span>
                        </label>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal:</span>
                    <strong id="temp-total">$<?= number_format($grand_total) ?></strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fw-bold h5">Total:</span>
                    <span class="h4 fw-bold text-danger" id="grand-total-display">$<?= number_format($grand_total) ?></span>
                </div>
                <form action="check_out.php" method="POST">
                    <input type="hidden" name="shipping_cost" id="shipping-input-value" value="0">
                    <button type="submit" class="btn btn-dark w-100 py-3 fw-bold rounded-pill text-uppercase">
                        Checkout
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const qtyInputs = document.querySelectorAll('.qty-input');
    const shippingRadios = document.querySelectorAll('.shipping-radio');
    const shippingHiddenInput = document.getElementById('shipping-input-value');

    // Hàm cập nhật tất cả giá tiền hiển thị
    function updateCartUI() {
        let grandSubtotal = 0;
        
        document.querySelectorAll('.product-row').forEach(row => {
            const price = parseFloat(row.dataset.price);
            const input = row.querySelector('.qty-input');
            
            let qty = parseInt(input.value);

            if (isNaN(qty) || qty < 1) {
                qty = 1;
                input.value = 1;
            }
            
            const itemSubtotal = price * qty;
            row.querySelector('.subtotal-display').innerText = '$' + itemSubtotal.toLocaleString();
            grandSubtotal += itemSubtotal;
            
            // TODO: Tại đây cần gọi AJAX để lưu số lượng (qty) mới vào $_SESSION['cart']
        });

        // Tính tổng tiền cuối cùng (bao gồm phí ship)
        const checkedRadio = document.querySelector('.shipping-radio:checked');
        const shippingCost = parseInt(checkedRadio ? checkedRadio.value : 0);
        
        // Cập nhật giá trị phí ship vào trường ẩn để gửi đi
        shippingHiddenInput.value = shippingCost; 

        const total = grandSubtotal + shippingCost;
        document.getElementById('temp-total').innerText = '$' + grandSubtotal.toLocaleString();
        document.getElementById('grand-total-display').innerText = '$' + total.toLocaleString();
    }
    
    // Xử lý khi nhập từ bàn phím trực tiếp hoặc nhấn mũi tên mặc định
    qtyInputs.forEach(input => {
        input.addEventListener('input', updateCartUI); // Cập nhật khi có thay đổi
        input.addEventListener('blur', function() { // Đảm bảo số lượng tối thiểu là 1
            if (this.value === "" || parseInt(this.value) < 1) {
                this.value = 1;
                updateCartUI();
            }
        });
    });

    // Xử lý khi đổi gói vận chuyển
    shippingRadios.forEach(radio => {
        radio.addEventListener('change', updateCartUI);
    });

    // Khởi tạo tổng tiền khi tải trang
    updateCartUI();
});
</script>