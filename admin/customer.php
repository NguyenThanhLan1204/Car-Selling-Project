<?php
include "dbconn.php";
$users = getAllCustomers();
?>

<body>
    <body>
    <head>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="./css/customer.css"> 
</head>
<div class="layout">

    <!-- SIDEBAR GỌI TỪ header.php -->
    <?php include ("header.php"); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Customer table</h6>
                        </div>
                    </div>

                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th class="text-center">Age</th>
                                        <th class="text-center">Date of Birth</th>
                                        <th class="text-center">Total order</th>
                                        <th class="text-center">Day come</th>
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

                                            <!-- Chưa có dữ liệu nên để trống -->
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
