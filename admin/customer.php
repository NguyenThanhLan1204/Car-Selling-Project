<?php
include "dbconn.php";
$users = getAllCustomers();
?>
<head>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="./css/ver_manuf.css"> 
</head>

<div class="layout">

    <?php include("header.php"); ?>

    <!-- Dùng .container đúng như CSS -->
    <div class="container">

        <div class="card">

            <!-- Giữ nguyên .card-header để CSS custom hoạt động -->
            <div class="card-header">
                <h2>Customer table</h2>
            </div>

            <div class="card-body">
                <div class="table-responsive">

                    <!-- Bỏ class bootstrap dư gây lệch CSS -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th class="text-center">Age</th>
                                <th class="text-center">Date of Birth</th>
                                <th class="text-center">Total order</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach($users as $user) { ?>
                                <tr>
                                    <td><?= $user['name'] ?></td>

                                    <td><?= $user['phone_number'] ?></td>

                                    <td><?= $user['email'] ?></td>

                                    <td class="text-center"><?= $user['age'] ?></td>

                                    <td class="text-center">
                                        <?= date('d-m-Y', strtotime($user['dob'])); ?>
                                    </td>

                                    <td class="text-center">
                                        <?= $user['total_orders'] ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>

                    </table>

                </div>
            </div>

        </div>
    </div>

</div>
