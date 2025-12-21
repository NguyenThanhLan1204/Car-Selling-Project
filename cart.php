<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php?message=please_login");
    exit();
}

?>

<?php
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
                    $grand_total += $subtotal;
                ?>
                <div class="card mb-3 shadow-sm product-row" data-id="<?= $curr_id ?>" data-price="<?= $row['price'] ?>">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-1 text-center">
                            <input type="checkbox" class="form-check-input item-checkbox" value="<?= $curr_id ?>" checked>
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
                
                <form action="base.php?page=checkout" method="POST" id="cart-form"> 
                    
                    <input type="hidden" name="selected_ids_input" id="selected-ids-input" value="">
                    <input type="hidden" name="sub_total_input" id="sub-total-hidden" value="<?= $grand_total ?>">

                    <div class="mb-4">
                        <p class="small fw-bold text-muted mb-2 text-uppercase">Shipping</p>
                        <div class="form-check border rounded p-2 mb-2">
                            <input class="form-check-input ms-0 me-2 shipping-radio" type="radio" 
                                   name="shipping_cost" id="ship-normal" value="0" checked>
                            <label class="form-check-label d-flex justify-content-between w-100" for="ship-normal">
                                <span>Free shipping</span><span class="fw-bold">$0</span>
                            </label>
                        </div>
                        <div class="form-check border rounded p-2">
                            <input class="form-check-input ms-0 me-2 shipping-radio" type="radio" 
                                   name="shipping_cost" id="ship-fast" value="15">
                            <label class="form-check-label d-flex justify-content-between w-100" for="ship-fast">
                                <span>Express shipping</span><span class="fw-bold">$15</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="small fw-bold text-muted mb-2 text-uppercase">Payment Method</p>
                        <div class="form-check border rounded p-2 mb-2">
                            <input class="form-check-input ms-0 me-2 payment-radio" type="radio" 
                                   name="checkout_payment_method" id="payment-cash" value="2" checked> 
                            <label class="form-check-label w-100" for="payment-cash">Cash on Delivery</label>
                        </div>
                        <div class="form-check border rounded p-2 mb-2">
                            <input class="form-check-input ms-0 me-2 payment-radio" type="radio" 
                                   name="checkout_payment_method" id="payment-bank" value="1"> 
                            <label class="form-check-label w-100" for="payment-bank">Bank Transfer</label>
                        </div>
                        <div class="form-check border rounded p-2">
                            <input class="form-check-input ms-0 me-2 payment-radio" type="radio" 
                                   name="checkout_payment_method" id="payment-credit" value="3"> 
                            <label class="form-check-label w-100" for="payment-credit">Credit Card</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Selected Subtotal:</span>
                        <strong id="temp-total">$0</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold h5">Total:</span>
                        <span class="h4 fw-bold text-danger" id="grand-total-display">$0</span>
                    </div>
                    
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
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const selectAllCheck = document.getElementById('select-all-cart');
    const selectedIdsInput = document.getElementById('selected-ids-input');
    const subTotalHidden = document.getElementById('sub-total-hidden');

    function updateCartUI() {
        let grandSubtotal = 0;
        let selectedIds = [];
        
        document.querySelectorAll('.product-row').forEach(row => {
            const checkbox = row.querySelector('.item-checkbox');
            const price = parseFloat(row.dataset.price);
            const input = row.querySelector('.qty-input');
            const qty = parseInt(input.value) || 1;
            const itemSubtotal = price * qty;

            // Update subtotal display for each line
            row.querySelector('.subtotal-display').innerText = '$' + itemSubtotal.toLocaleString();

            // ONLY PAY IF SELECTED
            if (checkbox.checked) {
                grandSubtotal += itemSubtotal;
                selectedIds.push(row.dataset.id);
                row.style.opacity = "1";
            } else {
                row.style.opacity = "0.5"; 
            }
        });

        const checkedShip = document.querySelector('.shipping-radio:checked');
        const shippingCost = parseInt(checkedShip ? checkedShip.value : 0);
        const total = grandSubtotal + shippingCost;
        
// Update hidden inputs to send to checkout
        subTotalHidden.value = grandSubtotal; 
        selectedIdsInput.value = selectedIds.join(','); 
        
        document.getElementById('temp-total').innerText = '$' + grandSubtotal.toLocaleString();
        document.getElementById('grand-total-display').innerText = '$' + total.toLocaleString();
    }
    

    itemCheckboxes.forEach(cb => cb.addEventListener('change', updateCartUI));
    qtyInputs.forEach(input => input.addEventListener('input', updateCartUI));
    shippingRadios.forEach(radio => radio.addEventListener('change', updateCartUI));

    updateCartUI(); 
});
</script>