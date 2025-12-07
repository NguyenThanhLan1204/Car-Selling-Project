<body>
    <div class="slider position-relative w-100">
        <div class="text-content position-relative">
            <div class="slide">           
                <div class="img">
                    <img 
                    src="./assets/img/101-anh-sieu-xe-4k-tai-free-lam-hinh-nen-dt-may-tinh.jpg" 
                    alt=""
                    class="d-block w-75 mx-auto p-3"
                    >
                </div>

                <div class="info-content position-absolute start-50 translate-middle-x text-center text-white z-1">
                    <h2 class="text-heading">Car World</h2>
                    <div class="text-description">Find your dream car at the best price today</div>
                </div>
            </div>
            <button class="slide-control btn btn-dark bg-opacity-50 border-0 position-absolute top-50 start-0 translate-middle-y z-2 ms-2 p-2">
                <i class="bx bxs-chevron-left text-white fs-3"></i>
            </button>
            <button class="slide-control btn btn-dark bg-opacity-50 border-0 position-absolute top-50 end-0 translate-middle-y z-2 me-2 p-2">
                <i class="bx bxs-chevron-right text-white fs-3"></i>
            </button>
        </div>
    </div>

    <div class="container py-5">
        <h2 class="text-center fw-bold mb-4">DISCOVER OUR MODELS</h2>
      
        <ul class="nav nav-tabs justify-content-center mb-4" id="carTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#sedan" type="button" role="tab">Sedan</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#hatchback" type="button" role="tab">Hatchback</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#suv" type="button" role="tab">SUV</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#mpv" type="button" role="tab">MPV</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pickup" type="button" role="tab">Pickup</button>
          </li>
        </ul>
      
        <div class="tab-content" id="carTabsContent">
          <div class="tab-pane fade show active" id="sedan" role="tabpanel">
            <div class="row g-4 justify-content-center">
              
              <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                  <img src="./assets/img/vios.jpg" class="card-img-top" alt="Toyota Vios">
                  <div class="card-body">
                    <h5 class="card-title fw-bold">Toyota Vios</h5>
                    <ul class="list-unstyled mb-3">
                      <li>Brand: Toyota (Japan)</li>
                      <li>Engine: 1.5L</li>
                      <li>Seats: 5</li>
                      <li>Description: The national sedan, durable.</li>
                    </ul>
                    <p class="fw-bold text-danger">458,000,000 VND</p>
                    <button class="btn btn-primary w-100">Order Now</button>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                  <img src="./assets/img/city.jpg" class="card-img-top" alt="Honda City">
                  <div class="card-body">
                    <h5 class="card-title fw-bold">Honda City</h5>
                    <ul class="list-unstyled mb-3">
                      <li>Brand: Honda (Japan)</li>
                      <li>Engine: 1.5L DOHC</li>
                      <li>Seats: 5</li>
                      <li>Description: Sporty, best driving experience.</li>
                    </ul>
                    <p class="fw-bold text-danger">559,000,000 VND</p>
                    <button class="btn btn-primary w-100">Order Now</button>
                  </div>
                </div>
              </div>
      
            </div>
          </div>
      
          <div class="tab-pane fade" id="hatchback" role="tabpanel"><p class="text-center mt-3">No data available</p></div>
          <div class="tab-pane fade" id="suv" role="tabpanel"><p class="text-center mt-3">Coming soon (SantaFe, VF8...)</p></div>
          <div class="tab-pane fade" id="mpv" role="tabpanel"><p class="text-center mt-3">Updating...</p></div>
          <div class="tab-pane fade" id="pickup" role="tabpanel"><p class="text-center mt-3">Updating...</p></div>
        </div>
    </div>

    <div class="container">
        <h2 class="text-center fw-bold mb-5">Best Selling Cars</h2>
    </div>
</body>