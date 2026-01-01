<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. XỬ LÝ LƯU TRẠNG THÁI (AJAX gọi đến chính nó)
// ===== AJAX SAVE CART STATE (SỬA LẠI) =====
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_GET['action']) &&
    $_GET['action'] === 'save_state'
) {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data && isset($data['quantities'])) {
        $_SESSION['selected_ids'] = $data['selected_ids'] ?? [];
        
        // Cập nhật TRỰC TIẾP vào giỏ hàng gốc
        foreach ($data['quantities'] as $id => $new_qty) {
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['qty'] = (int)$new_qty;
            }
        }
    }
    exit; 
}

// 2. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php?message=please_login");
    exit();
}

require_once 'db.php'; 
$db_connection = isset($conn) ? $conn : $link; 

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
                $ids_str = implode(',', $list_ids);
                $sql = "SELECT * FROM vehicle WHERE vehicle_id IN ($ids_str)";
                $result = $db_connection->query($sql);
                
                while ($row = $result->fetch_assoc()):
                    $curr_id = $row['vehicle_id'];
                    $qty = $cart_content[$curr_id]['qty'];
                    $subtotal = $row['price'] * $qty;
                    // Mặc định check nếu chưa có session, hoặc check theo session cũ
                    $is_checked = (!isset($_SESSION['selected_ids']) || in_array($curr_id, $_SESSION['selected_ids']));
                    if ($is_checked) $grand_total += $subtotal;
                ?>
                <div class="card mb-3 shadow-sm product-row" data-id="<?= $curr_id ?>" data-price="<?= $row['price'] ?>" data-stock="<?= $row['stock'] ?>">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-1 text-center">
                            <input type="checkbox" class="form-check-input item-checkbox" value="<?= $curr_id ?>" <?= $is_checked ? 'checked' : '' ?>>
                        </div>
                        <div class="col-md-2">
                            <img src="<?= !empty($row['image_url']) ? $row['image_url'] : 'https://via.placeholder.com/150' ?>" class="img-fluid rounded-start h-100 object-fit-cover" alt="<?= $row['model'] ?>" style="max-height: 150px; width: 100%;">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body position-relative">
                                <h5 class="card-title fw-bold"><?= $row['model'] ?></h5>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="d-flex flex-column">
                                        <label class="small text-muted mb-1">Quantity</label>
                                        <input type="number" class="form-control text-center qty-input" value="<?= $qty ?>" min="1"  max="<?= $row['stock'] ?>"data-id="<?= $curr_id ?>" style="width: 80px; text-align: center;">
                                    </div>
                                    <div class="text-end">
                                        <p class="small text-muted mb-0">Subtotal</p>
                                        <p class="card-text fw-bold fs-5 text-primary mb-0 subtotal-display">$<?= number_format($subtotal) ?></p>
                                    </div>
                                </div>
                                <a href="cart_delete.php?id=<?= $curr_id ?>" class="btn btn-sm text-danger position-absolute top-0 end-0 m-3" onclick="return confirm('Are you sure you want to remove this vehicle from your cart?')">
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
                <form action="base.php?page=checkout" method="POST" id="cart-form"> 
                    <input type="hidden" name="selected_ids_input" id="selected-ids-input" value="">
                    <input type="hidden" name="sub_total_input" id="sub-total-hidden" value="<?= $grand_total ?>">

                    <div class="mb-4">
                        <p class="text-muted small">
                            <i class='bx bx-info-circle'></i> 
                            Please proceed to checkout to select your test drive schedule and showroom.
                        </p>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 py-3 fw-bold rounded-pill text-uppercase">Book</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const qtyInputs = document.querySelectorAll('.qty-input');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const selectedIdsInput = document.getElementById('selected-ids-input');
    const subTotalHidden = document.getElementById('sub-total-hidden');

    function updateCartUI() {
        let grandSubtotal = 0;
        let selectedIds = [];
        let quantities = {};
        
        document.querySelectorAll('.product-row').forEach(row => {
            const checkbox = row.querySelector('.item-checkbox');
            const price = parseFloat(row.dataset.price);
            const input = row.querySelector('.qty-input');
           
            const stock = parseInt(row.dataset.stock);
            let qty = parseInt(input.value) || 1;

            // ÉP GIỚI HẠN SỐ LƯỢNG
            if (qty < 1) qty = 1;
            if (qty > stock) qty = stock;

            // CẬP NHẬT LẠI INPUT
            input.value = qty;
            
            const itemSubtotal = price * qty;
            const id = row.dataset.id;

            row.querySelector('.subtotal-display').innerText = '$' + itemSubtotal.toLocaleString();
            quantities[id] = qty;

            if (checkbox.checked) {
                grandSubtotal += itemSubtotal;
                selectedIds.push(id);
                row.style.opacity = "1";
            } else {
                row.style.opacity = "0.5"; 
            }
        });

        // Chỉ tính tổng tiền hàng, không có phí ship trong cart này
        const total = grandSubtotal;
        
        subTotalHidden.value = grandSubtotal; 
        selectedIdsInput.value = selectedIds.join(','); 
        
        // Cập nhật hiển thị (nếu có các element ID này ở layout cha, nếu không thì bỏ qua)
        if(document.getElementById('temp-total')) 
            document.getElementById('temp-total').innerText = '$' + grandSubtotal.toLocaleString();
        if(document.getElementById('grand-total-display'))
            document.getElementById('grand-total-display').innerText = '$' + total.toLocaleString();

        // GỬI DỮ LIỆU LƯU VÀO SESSION
        fetch('base.php?page=cart&action=save_state', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                selected_ids: selectedIds,
                quantities: quantities
            })
        });
    }

    itemCheckboxes.forEach(cb => cb.addEventListener('change', updateCartUI));
    qtyInputs.forEach(input => input.addEventListener('input', updateCartUI));

    updateCartUI(); 
});
</script>