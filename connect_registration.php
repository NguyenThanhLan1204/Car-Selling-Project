<?php
/* Kết nối database */
include("db.php");

/* Lấy dữ liệu từ form */
$name       = $_POST['name'];
$age        = $_POST['age'];
$username   = $_POST['user'];
$email      = $_POST['email'];
$password   = $_POST['password'];
$dob        = $_POST['dob'];
$phone      = $_POST['phonenumber'];
$address    = $_POST['address'];

/* Kiểm tra username đã tồn tại chưa */
$s = "SELECT * FROM customer WHERE username = '$username'";
$result = mysqli_query($conn, $s);
$num = mysqli_num_rows($result);

if ($num == 1) {
    // Nếu username đã tồn tại
    echo "<script>
            alert('Username already exists! Please try another one.');
            window.location.href = 'registration.php';
          </script>";
} else {
    // Thêm user mới vào bảng customer
    $reg = "INSERT INTO customer (name, age, phone_number, email, dob, username, password, address, role)
            VALUES ('$name', '$age', '$phone', '$email', '$dob', '$username', '$password', '$address', 'user')";
    
    mysqli_query($conn, $reg);

    echo "<script>
            alert('Registration successful! Please login.');
            window.location.href = 'login.php';
          </script>";
}

mysqli_close($conn);
?>
