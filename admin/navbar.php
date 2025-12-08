<?php 
$page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-5">

                <li class="breadcrumb-item text-sm">
                    <a class="text-dark opacity-5" href="index.php">Dashboard</a>
                </li>

                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
                    <?= ucwords(str_replace(".php", "", $page)) ?>
                </li>

            </ol>
        </nav>
    </div>
</nav>
