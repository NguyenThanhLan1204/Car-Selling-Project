<?php
include("db.php");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT v.vehicle_id, v.model, v.category, v.year, v.price, v.image_url, v.description, m.name AS manufacturer
        FROM vehicle v
        JOIN manufacturer m ON v.manufacturer_id = m.manufacturer_id
        ORDER BY v.category, v.model";
$result = $conn->query($sql);

$cars = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cars[$row['category']][] = $row;
    }
}
?>

<body>
    <div class="slider position-relative w-100">
        <div class="text-content position-relative">
            <div class="slide">           
                <div class="img">
                    <img src="./assets/img/101-anh-sieu-xe-4k-tai-free-lam-hinh-nen-dt-may-tinh.jpg" alt="" class="d-block w-75 mx-auto p-3">
                </div>
                <div class="info-content position-absolute start-50 translate-middle-x text-center text-white z-1">
                    <h2 class="text-heading">Car World</h2>
                    <div class="text-description">Find your dream car at the best price today</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <h2 class="text-center fw-bold mb-4">DISCOVER OUR MODELS</h2>
      
        <ul class="nav nav-tabs justify-content-center mb-4" id="carTabs" role="tablist">
          <?php 
          $first = true;
          foreach ($cars as $category => $list) {
              echo '<li class="nav-item">';
              echo '<button class="nav-link '.($first?'active':'').'" data-bs-toggle="tab" data-bs-target="#'.$category.'" type="button">'.ucfirst($category).'</button>';
              echo '</li>';
              $first = false;
          }
          ?>
        </ul>
      
        <div class="tab-content" id="carTabsContent">
            
            <?php 
            $first = true;
            foreach ($cars as $category => $list) {
                echo '<div class="tab-pane fade '.($first?'show active':'').'" id="'.$category.'" role="tabpanel">';
                echo '<div class="row g-4 justify-content-center">';
                
                foreach ($list as $car) {
                    echo '<div class="col-md-4">';
                    echo '  <div class="card h-100 shadow-sm">';
                    echo '    <img src="'.$car['image_url'].'" class="card-img-top" style="height:220px;object-fit:cover;" alt="'.$car['model'].'">';
                    echo '    <div class="card-body d-flex flex-column">';
                    echo '      <h5 class="card-title fw-bold">'.$car['model'].'</h5>';
                    echo '      <ul class="list-unstyled mb-3 small flex-grow-1">';
                    echo '        <li><strong>Brand:</strong> '.$car['manufacturer'].'</li>';
                    echo '        <li><strong>Year:</strong> '.$car['year'].'</li>';
                    echo '      </ul>';
                    echo '      <div class="mt-auto">';
                    echo '        <p class="fw-bold text-danger fs-5 mb-2">'.number_format($car['price'],0,',','.').' $</p>';
                    echo '        <a href="base.php?page=information&id='.$car['vehicle_id'].'" class="btn btn-dark w-100">Order Now</a>';
                    echo '      </div>';
                    echo '    </div>';
                    echo '  </div>';
                    echo '</div>';
                }
                
                echo '</div>';
                echo '</div>';
                $first = false;
            }
            ?>
        </div>
    </div>
</body>