<?php
$link = mysqli_connect("localhost", "root", "") or die(mysqli_error($link));
mysqli_select_db($link, "car_selling") or die(mysqli_error($link));
function getAllCustomers() {
    global $link;

    $sql = "
        SELECT 
            c.customer_id,
            c.name,
            c.phone_number,
            c.email,
            c.age,
            c.dob,
            COUNT(o.order_id) AS total_orders
        FROM customer c
        LEFT JOIN orders o 
            ON c.customer_id = o.customer_id
        GROUP BY c.customer_id
    ";

    $query = mysqli_query($link, $sql);
    return mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

$users = getAllCustomers();
?>
