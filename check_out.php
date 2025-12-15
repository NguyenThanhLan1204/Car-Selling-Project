<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// CONNECT DATABASE
require_once 'db.php'; 

if (!isset($conn) || !$conn) {
    die("Error: The connection variable \$conn has not been initialized.");
}
mysqli_set_charset($conn, "utf8");

// --- RETRIEVE DATA FROM SHOPPING CART ---
$sub_total_from_cart = isset($_POST['sub_total_input']) ? (float)$_POST['sub_total_input'] : 0.0;
$shipping_cost_from_cart = isset($_POST['shipping_cost']) ? (float)$_POST['shipping_cost'] : 0.0;
$payment_default = isset($_POST['payment_input']) ? (int)$_POST['payment_input'] : 2;
$total_for_display = $sub_total_from_cart + $shipping_cost_from_cart;

// PROCESS THE FORM WHEN THE ORDER BUTTON IS CLICKED
if (isset($_POST['btn_place_order'])) {

    $current_time = date('Y-m-d H:i:s');
    $status_code = 2; // Booked

    $customer_id = (int)$_SESSION['customer_id'];
    
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    
    // ✅ FIX: đúng name từ form
    $payment_method_id = (int)$_POST['payment'];

    // Recover hidden values
    $final_total  = (float)$_POST['total_amount_hidden'];
    $shipping_cost = (float)$_POST['shipping_cost_hidden'];

    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

    if (!empty($cart)) {

        // --- 1. CREATE ORDER ---
        $sql_order = "INSERT INTO orders 
            (customer_id, payment_method_id, status, total_amount, created_at, shipping_fee,
             shipping_name, shipping_phone, shipping_address) 
            VALUES 
            ($customer_id, $payment_method_id, $status_code, $final_total, '$current_time', $shipping_cost,
             '$fullname', '$phone', '$address')";

        if (mysqli_query($conn, $sql_order)) {

            $order_id = mysqli_insert_id($conn);
            $success = true;

            $ids = implode(',', array_keys($cart));
            $res = mysqli_query($conn, "SELECT vehicle_id, price FROM vehicle WHERE vehicle_id IN ($ids)");

            while ($r = mysqli_fetch_assoc($res)) {

                $vid   = $r['vehicle_id'];
                $qty   = $cart[$vid]['qty'];
                $price = $r['price'];

                // ✅ FIX: xóa status khỏi order_detail
                $sql_detail = "INSERT INTO order_detail 
                    (vehicle_id, order_id, amount, quantity) 
                    VALUES 
                    ($vid, $order_id, $price, $qty)";

                if (!mysqli_query($conn, $sql_detail)) {
                    $success = false;
                    break;
                }

                // Trừ kho
                mysqli_query($conn, "UPDATE vehicle SET stock = stock - $qty WHERE vehicle_id = $vid");
            }

            if ($success) {
                unset($_SESSION['cart']);
                $_SESSION['last_order_id'] = $order_id;

                if (isset($_COOKIE["user_cart_" . $customer_id])) {
                    setcookie("user_cart_" . $customer_id, "", time() - 3600, "/");
                }

                echo "<script>
                    alert('Order placed successfully! Order code: #$order_id');
                    window.location.href='base.php?page=order';
                </script>";
                exit();

            } else {
                mysqli_query($conn, "DELETE FROM orders WHERE order_id = $order_id");
                die("Error creating order details.");
            }

        } else {
            die("Main order creation error: " . mysqli_error($conn));
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

                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select name="payment" class="form-select" required>
                        <?php
                        $pm_query = mysqli_query($conn, "SELECT payment_method_id, name FROM payment_methods ORDER BY name");
                        while ($pm = mysqli_fetch_assoc($pm_query)) {
                            $selected = ($pm['payment_method_id'] == $payment_default) ? 'selected' : '';
                            echo "<option value='{$pm['payment_method_id']}' $selected>{$pm['name']}</option>";
                        }
                        ?>
                    </select>
                </div>

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

                <hr>

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
