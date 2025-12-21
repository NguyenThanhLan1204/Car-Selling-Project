<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. CONNECT DATABASE
require_once 'db.php';

if (!isset($conn) || !$conn) {
    die("Error: The connection variable \$conn has not been initialized.");
}
mysqli_set_charset($conn, "utf8");

// 2. RETRIEVE DATA FROM THE CART PAGE AND SEND IT TO
$selected_ids_str = isset($_POST['selected_ids_input']) ? $_POST['selected_ids_input'] : '';
$sub_total_from_cart = isset($_POST['sub_total_input']) ? (float)$_POST['sub_total_input'] : 0.0;
$shipping_cost_from_cart = isset($_POST['shipping_cost']) ? (float)$_POST['shipping_cost'] : 0.0;
// Get payment method ID (1, 2, or 3)
$payment_id_from_cart = isset($_POST['checkout_payment_method']) ? (int)$_POST['checkout_payment_method'] : 2;

$total_for_display = $sub_total_from_cart + $shipping_cost_from_cart;

// 3. AUTOMATIC PRE-FILL LOGIC
$prefill_name = "";
$prefill_phone = "";
$prefill_address = "";

if (isset($_SESSION['customer_id'])) {
    $current_customer_id = (int)$_SESSION['customer_id'];
    
    $sql_user_info = "SELECT name, phone_number, address FROM customer WHERE customer_id = '$current_customer_id'";
    $res_user = mysqli_query($conn, $sql_user_info);
    
    if ($res_user && mysqli_num_rows($res_user) > 0) {
        $user_info = mysqli_fetch_assoc($res_user);
        $prefill_name = $user_info['name'];
        $prefill_phone = $user_info['phone_number'];
        $prefill_address = $user_info['address'];
    }
} else {
    die('<div class="container py-5 text-center"><h3>Please <a href="login.php">login</a> to continue checkout.</h3></div>');
}

// 4. HANDLING WHEN PRESSING THE "CREATE ORDER" BUTTON
if (isset($_POST['btn_place_order'])) {

    $current_time = date('Y-m-d H:i:s');
    $status_code  = 2; // Booked

    $customer_id = (int)$_SESSION['customer_id'];

    // Get information from the Form (the user may have edited it)
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname'] ?? '');
    $phone    = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $address  = mysqli_real_escape_string($conn, $_POST['address'] ?? '');

    // Retrieve hidden values ​​passed through the form
    $payment_method_id = isset($_POST['payment_hidden']) ? (int)$_POST['payment_hidden'] : 2;
    $final_total       = isset($_POST['total_amount_hidden']) ? (float)$_POST['total_amount_hidden'] : 0.0;
    $shipping_cost     = isset($_POST['shipping_cost_hidden']) ? (float)$_POST['shipping_cost_hidden'] : 0.0;
    $final_selected_ids = mysqli_real_escape_string($conn, $_POST['final_selected_ids'] ?? '');

    if (!empty($final_selected_ids)) {

        // ---  CREATE MAIN ORDER ---
        $sql_order = "INSERT INTO orders 
            (customer_id, payment_method_id, status, total_amount, created_at, shipping_fee,
             shipping_name, shipping_phone, shipping_address)
            VALUES 
            ($customer_id, $payment_method_id, $status_code, $final_total, '$current_time', $shipping_cost,
             '$fullname', '$phone', '$address')";

        if (mysqli_query($conn, $sql_order)) {

            $order_id = mysqli_insert_id($conn);
            $success  = true;

            // --- ONLY THE PRICE OF THE SELECTED VEHICLES IS VALID. ---
            $res = mysqli_query($conn, "SELECT vehicle_id, price FROM vehicle WHERE vehicle_id IN ($final_selected_ids)");

            while ($r = mysqli_fetch_assoc($res)) {
                $vid   = (int)$r['vehicle_id'];
                $price = (float)$r['price'];
                // Get the number from the session cart
                $qty   = (int)$_SESSION['cart'][$vid]['qty'];

                // --- CREATE ORDER DETAIL ---
                $sql_detail = "INSERT INTO order_detail 
                    (vehicle_id, order_id, amount, quantity) 
                    VALUES 
                    ($vid, $order_id, $price, $qty)";

                if (mysqli_query($conn, $sql_detail))  {
                    // Inventory Update
                    mysqli_query($conn, "UPDATE vehicle SET stock = stock - $qty WHERE vehicle_id = $vid");
                    
                    // REMOVE THIS CAR FROM YOUR SHOPPING CART
                    unset($_SESSION['cart'][$vid]);
                } else {
                    $success = false;
                    break;
                }
            }

            if ($success) {
                // Delete cookies if any
                if (isset($_COOKIE['user_cart_' . $customer_id])) {
                    setcookie('user_cart_' . $customer_id, '', time() - 3600, '/');
                }

                echo "<script>
                    alert('Order placed successfully! Order code: #$order_id');
                    window.location.href='base.php?page=order';
                </script>";
                exit();
            } else {
                mysqli_query($conn, "DELETE FROM orders WHERE order_id = $order_id");
                die('Error creating order details.');
            }
        } else {
            die('Main order creation error: ' . mysqli_error($conn));
        }
    } else {
        echo "<script>alert('No items selected!'); window.location.href='base.php?page=cart';</script>";
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
            <input type="hidden" name="final_selected_ids" value="<?= $selected_ids_str ?>">
            <input type="hidden" name="total_amount_hidden" value="<?= $total_for_display ?>">
            <input type="hidden" name="shipping_cost_hidden" value="<?= $shipping_cost_from_cart ?>">
            <input type="hidden" name="payment_hidden" value="<?= $payment_id_from_cart ?>">

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="fullname" 
                       value="<?= htmlspecialchars($prefill_name) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" 
                       value="<?= htmlspecialchars($prefill_phone) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <input type="text" class="form-control" name="address" 
                       value="<?= htmlspecialchars($prefill_address) ?>" required>
                <div class="form-text">You can edit this address for this specific order.</div>
            </div>

            <hr class="my-4">

            <div class="p-3 bg-white border rounded mb-4">
                <div class="d-flex justify-content-between">
                    <span>Selected Payment:</span>
                    <span class="fw-bold">
                        <?php 
                            if($payment_id_from_cart == 1) echo "Bank Transfer";
                            elseif($payment_id_from_cart == 3) echo "Credit Card";
                            else echo "Cash on Delivery";
                        ?>
                    </span>
                </div>
            </div>

            <div class="d-flex justify-content-between mb-4">
                <span class="fw-bold h5">Total Amount:</span>
                <span class="h4 fw-bold text-danger">$<?= number_format($total_for_display) ?></span>
            </div>

            <button type="submit" name="btn_place_order" class="btn btn-primary w-100 fw-bold">
                CREATE ORDER
            </button>
        </form>

    </div>
</div>
</div>

</body>
</html>