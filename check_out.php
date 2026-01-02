<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 1. DATABASE CONNECTION
require_once 'db.php';
if (!isset($conn) || !$conn) {
    die("Error: The connection variable \$conn has not been initialized.");
}
mysqli_set_charset($conn, "utf8");

// 2. RETRIEVE DATA FROM CART PAGE
$selected_ids_str = $_POST['selected_ids_input']
    ?? (!empty($_SESSION['selected_ids']) ? implode(',', $_SESSION['selected_ids']) : '');

$sub_total_from_cart = isset($_POST['sub_total_input']) ? (float)$_POST['sub_total_input'] : 0.0;

$total_for_display = $sub_total_from_cart; 
// ===== DEPOSIT CONFIG =====
$DEPOSIT_PERCENT = 10; // 10% đặt cọc

// ===== CALCULATE DEPOSIT =====
$deposit = round($sub_total_from_cart * $DEPOSIT_PERCENT / 100, 2);

// Tổng xe KHÔNG cộng tiền cọc
$total_for_display = $sub_total_from_cart;

// Check login status
if (!isset($_SESSION['customer_id'])) {
    die('<div class="container py-5 text-center"><h3>Please <a href="login.php">login</a> to continue checkout.</h3></div>');
}

// 3. RETRIEVE USER INFO FROM DATABASE (Auto-fill)
$prefill_name = "";
$prefill_phone = "";
$prefill_address = "";
$current_customer_id = (int)$_SESSION['customer_id'];
$sql_user_info = "SELECT * FROM customer WHERE customer_id = $current_customer_id LIMIT 1";
$result_user_info = mysqli_query($conn, $sql_user_info);
if ($result_user_info && mysqli_num_rows($result_user_info) > 0) {
    $row = mysqli_fetch_assoc($result_user_info);  
    $prefill_name    = isset($row['name']) ? $row['name'] : '';              
    $prefill_phone   = isset($row['phone_number']) ? $row['phone_number'] : ''; 
    $prefill_address = isset($row['address']) ? $row['address'] : '';           
}

// 4. HANDLE "CREATE ORDER" BUTTON CLICK
if (isset($_POST['btn_place_order'])) {
    
    // --- CẬP NHẬT LOGIC: Lấy ngày giờ trực tiếp từ form checkout ---
    $test_drive_date = $_POST['test_drive_date'] ?? null;
    $test_drive_time = $_POST['test_drive_time'] ?? null;
    // ---------------------------------------------------------------

    // Validate
    if (empty($test_drive_date) || empty($test_drive_time)) {
        echo "<script>alert('Please select Date and Time for test drive!'); window.history.back();</script>";
        exit();
    }

    $current_time = date('Y-m-d H:i:s');
    $status_code  = 2; 
    $customer_id = (int)$_SESSION['customer_id'];
    
    // Get data from Form
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname'] ?? '');
    $phone    = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $address  = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    $final_total       = isset($_POST['total_amount_hidden']) ? (float)$_POST['total_amount_hidden'] : 0.0;
    $deposit = isset($_POST['deposit_amount_hidden']) ? (float)$_POST['deposit_amount_hidden'] : 0.0;
    $final_selected_ids = mysqli_real_escape_string($conn, $_POST['final_selected_ids'] ?? '');

    if (!empty($final_selected_ids)) {
            $sql_order = "INSERT INTO orders 
                (customer_id, status, total_amount, deposit, booking_name, booking_phone, booking_address,
                test_drive_date, test_drive_time)
                VALUES (
                    $customer_id, $status_code, $final_total, $deposit, '$fullname', '$phone',
                    '$address', '$test_drive_date', '$test_drive_time')";

        if (mysqli_query($conn, $sql_order)) {
            $order_id = mysqli_insert_id($conn);
            $success  = true;
            $res = mysqli_query($conn, "SELECT vehicle_id, price FROM vehicle WHERE vehicle_id IN ($final_selected_ids)");
            while ($r = mysqli_fetch_assoc($res)) {
                $vid   = (int)$r['vehicle_id'];
                $price = (float)$r['price'];
                // Check if session cart exists to avoid errors
                $qty = isset($_SESSION['cart'][$vid]['qty']) ? (int)$_SESSION['cart'][$vid]['qty'] : 1;

                $sql_detail = "INSERT INTO order_detail (vehicle_id, order_id, amount, quantity) VALUES ($vid, $order_id, $price, $qty)";
                if (mysqli_query($conn, $sql_detail))  {
                    mysqli_query($conn, "UPDATE vehicle SET stock = stock - $qty WHERE vehicle_id = $vid");
                    unset($_SESSION['cart'][$vid]);
                } else { $success = false; break; }
            }

            if ($success) {
                if (isset($_COOKIE['user_cart_' . $customer_id])) {
                    setcookie('user_cart_' . $customer_id, '', time() - 3600, '/');
                }
                echo "<script>
                    // Clear storage after successful order
                    localStorage.removeItem('user_draft_fullname');
                    localStorage.removeItem('user_draft_phone');
                    localStorage.removeItem('user_draft_address');
                    localStorage.removeItem('last_selected_ids'); 
                    alert('Order placed successfully!');
                    window.location.href='base.php?page=order';
                </script>";
                exit();
            }
        } else {
             echo "Error: " . mysqli_error($conn);
        }
    }
}

// 5. FETCH SELECTED VEHICLE DETAILS FOR DISPLAY
$vehicles = [];
if (!empty($selected_ids_str)) {
    $sql_vehicles = "
        SELECT vehicle_id, model, price, image_url
        FROM vehicle
        WHERE vehicle_id IN ($selected_ids_str)
    ";
    $res_vehicles = mysqli_query($conn, $sql_vehicles);
    while ($row = mysqli_fetch_assoc($res_vehicles)) {
        $vehicles[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-return-cart { display: inline-flex; align-items: center; padding: 8px 15px; color: #4a5568; background-color: #fff; border: 1px solid #cbd5e0; border-radius: 6px; text-decoration: none; margin-bottom: 20px; }
        .btn-return-cart:hover { background-color: #f7fafc; color: #2d3748; }
        .btn-return-cart span { margin-right: 10px; font-size: 1.3rem; font-weight: bold; }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="mb-3">
        <a href="base.php?page=cart" class="btn-return-cart"><span>&larr;</span> Return to Cart</a>
    </div>
   <div class="row justify-content-center">
        <div class="col-md-7">
        <h4 class="mb-3">Booking Information</h4>
        <form method="POST" id="checkoutForm">
            <input type="hidden" id="current_ids" name="final_selected_ids" value="<?= htmlspecialchars($selected_ids_str) ?>">
            <input type="hidden" name="total_amount_hidden" value="<?= $total_for_display ?>">
            <input type="hidden" name="deposit_amount_hidden" value="<?= $deposit ?>">

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="fullname" id="fullname" value="<?= htmlspecialchars($prefill_name) ?>" required>
           </div>
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" id="phone" value="<?= htmlspecialchars($prefill_phone) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <input type="text" class="form-control" name="address" id="address" value="<?= htmlspecialchars($prefill_address) ?>" required>
            </div>

            <hr class="my-4">
            
            <h5 class="mb-3">Schedule Info</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="test_drive_date" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Time <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" name="test_drive_time" required>
                </div>
            </div>
            <hr class="my-4">

            <?php if (!empty($vehicles)): ?>
            <div class="mb-3">
                <label class="form-label">My Selected Vehicles</label>
                <?php foreach ($vehicles as $v): ?>
                    <div class="card mb-3 shadow-sm">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-3">
                                <img
                                    src="<?= !empty($v['image_url']) ? $v['image_url'] : 'https://via.placeholder.com/300' ?>"
                                    class="img-fluid rounded-start"
                                    alt="<?= htmlspecialchars($v['model']) ?>"
                                >
                            </div>
                            <div class="col-md-9">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold">
                                        <?= htmlspecialchars($v['model']) ?>
                                    </h5>
                                    <p class="mb-1">
                                        Price: <strong class="text-primary">$<?= number_format($v['price']) ?></strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between mb-2">
                <span class="fw-semibold">Total Amount:</span>
                <span class="fw-bold text-warning">
                    $<?= number_format($total_for_display) ?>
                </span>
            </div>

            <div class="d-flex justify-content-between mb-4">
                <span class="fw-bold h5">Deposit (<?= $DEPOSIT_PERCENT ?>%):</span>
                <span class="h4 fw-bold text-danger">$<?= number_format($deposit) ?></span>
            </div>
            <button type="submit" name="btn_place_order" class="btn btn-primary w-100 fw-bold py-2">Book Now</button>
        </form>
    </div>
</div>
</div>

<script>
(function() {
    const fields = ['fullname', 'phone', 'address'];
    const currentIds = document.getElementById('current_ids').value;
    const lastIds = localStorage.getItem('last_selected_ids');
    // CHECK: If the vehicle list changes -> Clear old draft
    if (lastIds !== currentIds) {
        fields.forEach(id => localStorage.removeItem('user_draft_' + id));
        localStorage.setItem('last_selected_ids', currentIds);
    }
    function applyDrafts() {
        fields.forEach(id => {
            const saved = localStorage.getItem('user_draft_' + id);
            const input = document.getElementById(id);
            if (input && saved) {
                input.value = saved;
            }
        });
    }
    applyDrafts();
    // Save draft when user types
    fields.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', () => {
                localStorage.setItem('user_draft_' + id, input.value);
            });
        }
    });
})();
</script>
</body>
</html>