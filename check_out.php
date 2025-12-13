<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// CONNECT DATABASE
if (file_exists("admin/dbconn.php")) {
    require_once("admin/dbconn.php");
} elseif (file_exists("dbconn.php")) {
    require_once("dbconn.php");
} elseif (file_exists("db.php")) {
    require_once("db.php");
    if (isset($conn) && !isset($link)) { $link = $conn; }
} else {
    die("Error: Database connection file not found.");
}

if (!isset($link) || !$link) {
    die("Error: The connection variable \$link has not been initialized..");
}
mysqli_set_charset($link, "utf8"); 


// --- RETRIEVE DATA FROM SHOPPING CART (For Form Display) ---
$sub_total_from_cart = isset($_POST['sub_total_input']) ? (float)$_POST['sub_total_input'] : 0.0;
$shipping_cost_from_cart = isset($_POST['shipping_cost']) ? (float)$_POST['shipping_cost'] : 0.0;
$payment_method_default = isset($_POST['payment_method_input']) ? $_POST['payment_method_input'] : 'Cash on Delivery';
$total_for_display = $sub_total_from_cart + $shipping_cost_from_cart;

//  PROCESS THE FORM WHEN THE ORDER BUTTON IS CLICKED

if (isset($_POST['btn_place_order'])) {

    $current_time = date('Y-m-d H:i:s');
    $status_code = 2; // Status = 2 (Booked)
    
    // Get customer_id from session
    $customer_id = (int)$_SESSION['customer_id'];
    
    //1. Retrieve data from the form and clean it up.
    $fullname = mysqli_real_escape_string($link, $_POST['fullname']);
    $phone = mysqli_real_escape_string($link, $_POST['phone']);
    $address = mysqli_real_escape_string($link, $_POST['address']);
    
    // Recover hidden values
    $payment_method = mysqli_real_escape_string($link, $_POST['payment_method']); 
    $final_total = (float)$_POST['total_amount_hidden'];
    $shipping_cost = (float)$_POST['shipping_cost_hidden'];
    
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

    if (!empty($cart)) {
        // --- CREATE ORDER ---
        $sql_order = "INSERT INTO orders 
              (customer_id, status, total_amount, created_at, shipping_fee,
               shipping_name, shipping_phone, shipping_address) 
              VALUES 
              ($customer_id, $status_code, $final_total, '$current_time', $shipping_cost,
               '$fullname', '$phone', '$address')";
        
        if (mysqli_query($link, $sql_order)) {
            $order_id = mysqli_insert_id($link);
            
            // --- CREATE ORDER DETAIL ---
            $success = true;
            $ids = implode(',', array_keys($cart));
            $res = mysqli_query($link, "SELECT vehicle_id, price FROM vehicle WHERE vehicle_id IN ($ids)");
            
            while($r = mysqli_fetch_assoc($res)){
                $vid = $r['vehicle_id'];
                $qty = $cart[$vid]['qty'];
                $price = $r['price'];
                $detail_status = 2; 
                
                $sql_detail = "INSERT INTO order_detail 
                (customer_id, vehicle_id, order_id, amount, quantity, payment_method, status) 
                VALUES 
                ($customer_id, $vid, $order_id, $price, $qty, '$payment_method', $detail_status)";
                
                if (!mysqli_query($link, $sql_detail)) {
                    $success = false;
                    error_log("Order details insertion error: " . mysqli_error($link));
                    break;
                }
            }
            
            if ($success) {
                unset($_SESSION['cart']);
                $_SESSION['last_order_id'] = $order_id; 
                
                echo "<script>
                    alert('Order placed successfully! Order code: #$order_id'); 
                    window.location.href='base.php?page=order'; 
                </script>";
                exit();
                
            } else {
                mysqli_query($link, "DELETE FROM orders WHERE order_id = $order_id");
                die("Error creating order details: " . mysqli_error($link));
            }
            
        } else {
            die("Main order creation error: " . mysqli_error($link));
        }
    } else {
        echo "<script>alert('Cart is empty!'); window.location.href='base.php';</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <h4 class="mb-3">Billing Address</h4>
            
            <form method="POST">
                
                <input type="hidden" name="total_amount_hidden" value="<?= $total_for_display ?>">
                <input type="hidden" name="shipping_cost_hidden" value="<?= $shipping_cost_from_cart ?>">
                <input type="hidden" name="payment_method" value="<?= htmlspecialchars($payment_method_default) ?>">
                
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="fullname" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="phone" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-control" name="address" required>
                </div>
                
                <hr class="my-4">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fw-bold h5">Total Amount:</span>
                    <span class="h4 fw-bold text-danger">$<?= number_format($total_for_display) ?></span>
                </div>

                <button type="submit" name="btn_place_order" class="btn btn-primary w-100 py-2 fw-bold text-uppercase">
                    CREATE ORDER
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>