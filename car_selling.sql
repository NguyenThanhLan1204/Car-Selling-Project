DROP DATABASE IF EXISTS car_selling;
CREATE DATABASE car_selling;
USE car_selling;

-- 1. Bảng Manufacturer (Nhà sản xuất)
CREATE TABLE manufacturer (
    manufacturer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    country VARCHAR(100),
    description TEXT
);

-- 2. Bảng Vehicle (Xe)
CREATE TABLE vehicle (
    vehicle_id INT AUTO_INCREMENT PRIMARY KEY,
    manufacturer_id INT, 
    model VARCHAR(255) NOT NULL,
    year INT,
    price DECIMAL(15, 2), 
    image_url VARCHAR(500),
    video_url VARCHAR(500), 
    stock INT DEFAULT 0, 
    description TEXT,
    CONSTRAINT fk_vehicle_manufacturer FOREIGN KEY (manufacturer_id) REFERENCES manufacturer(manufacturer_id)
);

-- 3. Bảng Customer (Khách hàng )
CREATE TABLE customer (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    age INT, 
    phone_number VARCHAR(20),
    email VARCHAR(255),
    dob DATE, 
    username VARCHAR(100) UNIQUE,
    address varchar(191) DEFAULT NULL, 
    password VARCHAR(255),
    role VARCHAR(20) DEFAULT 'user' 
);

-- 4. Bảng Order (Đơn hàng)
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL, 
    status INT(11) NOT NULL DEFAULT 2, 
    created_at timestamp NOT NULL DEFAULT current_timestamp(), 
    CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES customer(customer_id)
);

CREATE TABLE order_detail (
  order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  vehicle_id INT NOT NULL,
  order_id INT DEFAULT NULL,
  amount DECIMAL(15, 2) NOT NULL, 
  quantity INT(11) NOT NULL,
  payment_method VARCHAR(50) NOT NULL,
  status INT(11) NOT NULL DEFAULT 1,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  CONSTRAINT fk_orderdetail_customer FOREIGN KEY (customer_id) REFERENCES customer(customer_id),
  CONSTRAINT fk_orderdetail_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicle(vehicle_id),
  CONSTRAINT fk_orderdetail_orders FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- 1.Tạo Manufacturers
INSERT INTO manufacturer (name, country, description) VALUES
('Mercedes-Benz', 'Germany', 'Luxury vehicles, vans, trucks, buses, coaches and ambulances.'), 
('BMW', 'Germany', 'German multinational manufacturer of luxury vehicles and motorcycles.'),                          
('Ferrari', 'Italy', 'Italian luxury sports car manufacturer based in Maranello.'),              
('Audi', 'Germany', 'German automotive manufacturer of luxury vehicles.'),
('Volvo', 'Sweden', 'Swedish premium automotive manufacturer focusing on safety.'),
('Porsche', 'Germany', 'German high-performance sports car manufacturer.'),
('McLaren', 'United Kingdom', 'British supercar manufacturer known for hypercars.');
-- 2. Tạo Vehicles
INSERT INTO vehicle (manufacturer_id, model, year, price, image_url,video_url, stock, description) VALUES

-- ==========================
-- 1. AUDI (ID = 4)
-- ==========================
(4, 'Audi RS6', 2023, 138888, './assets/img/audi_rs6.jpg', 'assets/video/audi_rs6.mp4', 5, 'High-performance wagon with aggressive styling.'),
(4, 'Audi Q3 SUV', 2024, 38595, './assets/img/audi_q3.webp', 'assets/video/Audi_Q3.mp4', 7, 'Fuel 9.0–5.3 l/100km, CO₂ 205–137 g/km, Class G–E. Optional equipment available.'),
(4, 'Audi RS7', 2024, 127800, './assets/img/audi_rs7.jpg', 'assets/video/Audi_RS7.mp4', 3, 'High-performance luxury fastback.'),
(4, 'Audi A7 Sportback', 2024, 75000, './assets/img/audi_a7.jpg', 'assets/video/Audi_A7_Sportback.mp4', 6, 'Premium 4-door coupe-style sedan.'),

-- ==========================
-- 2. BMW (ID = 2)
-- ==========================
(2, 'BMW X5M', 2024, 519000, './assets/img/bmw_x5m.png', 'assets/video/BMW_X5M.mp4', 2, '616 BHP V8 • Black Sapphire Metallic • 13,500 KM • GCC Spec • Warranty until 2029.'),
(2, 'BMW X5 xDrive40i M Sport', 2025, 359000, './assets/img/bmw_x5_40i.jpg', 'assets/video/BMW_X5.mp4', 3, 'Loaded with premium options • 9,771 KM • Warranty until 2030.'),
(2, 'BMW M3 G80', 2024, 76995, './assets/img/bmw_m3_g80.png', 'assets/video/bmw_m3_g80.mp4', 4, 'High-performance sports sedan, twin-turbo inline-6.'),

-- ==========================
-- 3. VOLVO (ID = 5)
-- ==========================
(5, 'Volvo EM90', 2024, 80000, './assets/img/volvo_em90.avif', 'assets/video/VolvoEM90.mp4', 3, 'Luxury electric MPV with Scandinavian design.'),
(5, 'Volvo S90', 2024, 55000, './assets/img/volvo_s90.png', 'assets/video/Volvo_S90.mp4', 5, 'Premium sedan known for safety and comfort.'),
(5, 'Volvo XC70', 2025, 45000, './assets/img/volvo_xc70.png', 'assets/video/Volvo_XC70.mp4', 4, 'All-road wagon built for versatility.'),

-- ==========================
-- 4. PORSCHE (ID = 6)
-- ==========================
(6, 'Porsche Taycan Turbo GT', 2025, 739000, './assets/img/taycan_gt.jpg', 'assets/video/Porsche_Taycan_Turbo_GT.mp4', 1, '1,019 BHP • Shade Green Metallic • Weissach Package • 838 KM • Warranty 2027.'),
(6, 'Porsche 911 GT3', 2024, 175000, './assets/img/porsche_gt3.png', 'assets/video/porsche_911gt3.mp4', 2, 'Track-focused high-performance 911.'),
(6, 'Porsche GT3 RS', 2024, 225000, './assets/img/porsche_gt3_rs.png', 'assets/video/Porsche_GT3_RS.mp4', 1, 'Ultimate track machine with extreme aero.'),
(6, 'Porsche 911', 2024, 110000, './assets/img/porsche_911.png', 'assets/video/porsche_911.mp4', 5, 'Iconic sports car with rear-engine layout.'),
(6, 'Porsche 918 Spyder Weissach', 2015, 900000, './assets/img/porsche_918_weissach.png', 'assets/video/Porsche_918_spyder_weisach.mp4', 1, 'Lightweight Weissach Package edition.'),

-- ==========================
-- 5. FERRARI (ID = 3)
-- ==========================
(3, 'Ferrari 812', 2024, 335000, './assets/img/ferrari_812.jpg', 'assets/video/ferrari_812.mp4', 1, 'Naturally aspirated V12 grand tourer.'),
(3, 'Ferrari SF90', 2024, 625000, './assets/img/ferrari_sf90.png', 'assets/video/ferrarisf90.mp4', 1, '1000 HP hybrid supercar.'),
(3, 'Ferrari Roma', 2024, 220000, './assets/img/ferrari_roma.jpg', 'assets/video/Ferrari_Roma.mp4', 1, 'Elegant V8 grand tourer.'),

-- ==========================
-- 6. MERCEDES-BENZ (ID = 1)
-- ==========================
(1, 'Maybach GLS 600', 2023, 214888, './assets/img/maybach_gls600.jpg', 'assets/video/maybach_2023.mp4', 3, 'Flagship luxury SUV with unmatched comfort.'),
(1, 'Mercedes-AMG CLA 45 S 4MATIC+', 2024, 309000, './assets/img/cla45.jpg', 'assets/video/Mercedes_AMG_45S.mp4', 4, '421 BHP • 14,000 KM • Full history • Warranty until 2029.'),
(1, 'Mercedes CLE53 AMG 4MATIC+', 2024, 439000, './assets/img/cle53.jpg', 'assets/video/Mercedes_CLE53.mp4', 3, '443 BHP • 5,980 KM • Warranty until 2029.'),
(1, 'Mercedes-AMG SL63', 2024, 155000, './assets/img/sl63.png', 'assets/video/Mercedes_AMG_SL63.mp4', 2, 'Luxury high-performance roadster.'),
(1, 'Mercedes-AMG GT Black Series', 2021, 300000, './assets/img/gt_black.png', 'assets/video/AMG_GT_BLACK_SERIES.mp4', 1, 'Extreme aerodynamics • track-bred V8 monster.'),

-- ==========================
-- 7. MCLAREN (ID = 7)
-- ==========================
(7, 'McLaren Senna', 2020,  1000000, './assets/img/mclaren_senna.png', 'assets/video/Maclaren_senna.mp4', 1, 'Track-focused hypercar named after Ayrton Senna.'),
(7, 'McLaren Speedtail', 2020, 2100000, './assets/img/mclaren_speedtail.png', 'assets/video/Mclaren_Speedtail.mp4', 1, 'Hybrid hyper-GT with 403 km/h top speed.'),
(7, 'McLaren 750S', 2024, 330000, './assets/img/mclaren_750s.webp', 'assets/video/McLaren_750S_Spider.mp4', 2, 'Lighter, sharper successor to the 720S.');

-- 3. Tạo Customers 
INSERT INTO customer (name, age, phone_number, email, dob, username, password, role, address) VALUES
('System Administrator', 30, '0909000111', 'admin@carselling.com', '1990-01-01', 'admin', 'admin123', 'admin', '123 Admin Street, District 1, Ho Chi Minh City'),
('Dinh Khai', 25, '0912345678', 'ronadokhaibeo@gmail.com', '1999-05-15', 'khaibeo', '123456', 'user', '45 Nguyen Hue, District 3, Ho Chi Minh City'),
('Minh Ly', 40, '0988777666', 'minhca@gmail.com', '1984-12-20', 'minhca', 'password', 'user', '789 Le Loi, District 1, Ho Chi Minh City'),
('Namdo', 22, '0365554444', 'namdo@gmail.com', '2002-03-08', 'namdo', 'security', 'user', '56 Tran Hung Dao, District 5, Ho Chi Minh City'),
('Lanlitdo', 35, '0901239876', 'trumcho@gmail.com', '1989-07-27', 'lan', 'lan123', 'user', '321 Pham Ngu Lao, District 1, Ho Chi Minh City');

-- 4. Tạo Orders
INSERT INTO orders (customer_id, status, created_at) VALUES
(2, 4, '2024-01-15 10:30:00'),   -- Completed
(3, 2, '2024-02-20 14:15:00'),   -- Booked
(4, 4, '2024-03-05 09:00:00'),   -- Completed
(2, 2, '2024-04-10 16:45:00'),   -- Booked
(5, 3, '2024-05-01 11:20:00');   -- Cancelled / Pending / your rule

-- 5. Tạo Order Detail phù hợp bảng order_detail
INSERT INTO order_detail 
(customer_id, vehicle_id, order_id, amount, quantity, payment_method, status) 
VALUES
-- Order #1 của customer 2
(2, 1, 1, 458000000, 1, 'Cash', 4),

-- Order #2 của customer 3
(3, 3, 2, 665000000, 1, 'Bank Transfer', 2),

-- Order #3 của customer 4
(4, 2, 3, 559000000, 1, 'Credit Card', 4),

-- Order #4 của customer 2
(2, 5, 4, 1090000000, 1, 'Bank Transfer', 2),

-- Order #5 của customer 5
(5, 4, 5, 1050000000, 1, 'Cash', 5);


ALTER TABLE orders
ADD COLUMN total_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00;

ALTER TABLE orders
ADD COLUMN shipping_fee DECIMAL(10, 2) DEFAULT 0;

CREATE TABLE payment_methods (
    payment_method_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO payment_methods (name) VALUES 
('Bank Transfer'), 
('Cash'),
('Credit Card');

ALTER TABLE orders 
ADD COLUMN shipping_name VARCHAR(255),
ADD COLUMN shipping_phone VARCHAR(20),
ADD COLUMN shipping_address TEXT;
