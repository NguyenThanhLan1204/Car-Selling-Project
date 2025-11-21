<?php
session_start();

/* Kết nối database */
$con = mysqli_connect('localhost', 'root', '');
mysqli_select_db($con, "user_car_system");

/* Lấy dữ liệu từ form */
$username    = $_POST['user'];
$email       = $_POST['email'];
$password    = $_POST['password'];
$dob         = $_POST['dob'];
$nationality = $_POST['nationality'];
$phone       = $_POST['phonenumber'];

/* Kiểm tra username đã tồn tại chưa */
$s = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($con, $s);
$num = mysqli_num_rows($result);

if ($num == 1) {
    // Nếu username đã tồn tại
    echo "<script>
            alert('Username already exists! Please try another one.');
            window.location.href = 'registration.php';
          </script>";
} else {
    // Nếu username chưa tồn tại thì thêm người dùng mới
    $reg = "INSERT INTO users (username, email, password, dob, nationality, phonenumber)
            VALUES ('$username', '$email', '$password', '$dob', '$nationality', '$phone')";
    mysqli_query($con, $reg);

    echo "<script>
            alert('Registration successful! Please login.');
            window.location.href = 'login.php';
          </script>";
}

mysqli_close($con);
?>