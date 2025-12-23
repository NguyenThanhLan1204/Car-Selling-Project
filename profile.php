<?php
// profile.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

// 1. Check login status
if (!isset($_SESSION['customer_id'])) {
    // Redirect to login if not authenticated
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$customer_id = $_SESSION['customer_id'];
$message = "";
$msg_type = ""; // success or danger

// 2. Handle Profile Update Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $phone   = $_POST['phone_number'];
    $address = $_POST['address'];
    $age     = !empty($_POST['age']) ? $_POST['age'] : NULL;
    $dob     = !empty($_POST['dob']) ? $_POST['dob'] : NULL;

    // Update Database using Prepared Statement for security
    $sql_update = "UPDATE customer 
                   SET name = ?, email = ?, phone_number = ?, address = ?, age = ?, dob = ? 
                   WHERE customer_id = ?";
    
    $stmt = $conn->prepare($sql_update);
    
    // "ssssisi": s=string, i=integer. Parameter order must match SQL query
    $stmt->bind_param("ssssisi", $name, $email, $phone, $address, $age, $dob, $customer_id);

    if ($stmt->execute()) {
        $message = "Update profile successful!";
        $msg_type = "success";
        
        // Update Session Name to reflect changes immediately on Header
        $_SESSION['name'] = $name;
        // $_SESSION['username'] remains unchanged
    } else {
        $message = "Error updating information: " . $conn->error;
        $msg_type = "danger";
    }
    $stmt->close();
}

// 3. Fetch current User Info to display on the form
$sql_info = "SELECT * FROM customer WHERE customer_id = ?";
$stmt_info = $conn->prepare($sql_info);
$stmt_info->bind_param("i", $customer_id);
$stmt_info->execute();
$result = $stmt_info->get_result();
$user = $result->fetch_assoc();
$stmt_info->close();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="fw-bold mb-0"><i class='bx bx-user-circle'></i> My Profile</h4>
                </div>
                <div class="card-body p-4">
                    
                    <?php if ($message): ?>
                        <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show" role="alert">
                            <?= $message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted small text-uppercase fw-bold">Username</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Age</label>
                                <input type="number" name="age" class="form-control" value="<?= htmlspecialchars($user['age']) ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($user['phone_number']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date of Birth</label>
                                <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($user['dob']) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Address</label>
                            <textarea name="address" class="form-control" rows="2" required><?= htmlspecialchars($user['address']) ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="base.php?page=home" class="btn btn-outline-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary fw-bold px-4">Update Information</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>