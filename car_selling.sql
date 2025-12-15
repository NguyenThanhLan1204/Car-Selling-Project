DROP DATABASE IF EXISTS car_selling;
CREATE DATABASE car_selling;
USE car_selling;

-- ======================================================
-- 1. BẢNG MANUFACTURER (Nhà sản xuất)
-- ======================================================
CREATE TABLE manufacturer (
    manufacturer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    country VARCHAR(100),
    description TEXT
);

-- ======================================================
-- 2. BẢNG PAYMENT METHODS (Phương thức thanh toán)
-- ======================================================
CREATE TABLE payment_methods (
    payment_method_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- ======================================================
-- 3. BẢNG CUSTOMER (Khách hàng)
-- ======================================================
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

-- ======================================================
-- 4. BẢNG VEHICLE (Xe)
-- ======================================================
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

-- ======================================================
-- 5. BẢNG ORDERS (Đơn hàng)
-- ======================================================
-- Đã thêm payment_id và các cột ship hàng, tổng tiền
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL, 
    payment_method_id INT DEFAULT 2, -- Mặc định ID 2 là Cash
    status INT(11) NOT NULL DEFAULT 2, 
    total_amount DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    shipping_fee DECIMAL(15, 2) DEFAULT 0.00,
    shipping_name VARCHAR(255),
    shipping_phone VARCHAR(20),
    shipping_address TEXT,
    created_at timestamp NOT NULL DEFAULT current_timestamp(), 
    CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES customer(customer_id),
    CONSTRAINT fk_orders_payment FOREIGN KEY (payment_method_id) REFERENCES payment_methods(payment_method_id)
);

-- ======================================================
-- 6. BẢNG ORDER DETAIL (Chi tiết đơn hàng)
-- ======================================================
-- Đã XÓA cột payment_method thừa
CREATE TABLE order_detail (
    order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    order_id INT DEFAULT NULL,
    amount DECIMAL(15, 2) NOT NULL, 
    quantity INT(11) NOT NULL,
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    CONSTRAINT fk_orderdetail_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicle(vehicle_id),
    CONSTRAINT fk_orderdetail_orders FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);

-- ======================================================
-- NẠP DỮ LIỆU (DATA INSERTION)
-- ======================================================

-- 1. Insert Payment Methods
INSERT INTO payment_methods (name) VALUES 
('Bank Transfer'), 
('Cash'),
('Credit Card');

-- 2. Insert Manufacturers
INSERT INTO manufacturer (name, country, description) VALUES
('Mercedes-Benz', 'Germany', 'Luxury vehicles, vans, trucks, buses, coaches and ambulances.'), 
('BMW', 'Germany', 'German multinational manufacturer of luxury vehicles and motorcycles.'),                          
('Ferrari', 'Italy', 'Italian luxury sports car manufacturer based in Maranello.'),              
('Audi', 'Germany', 'German automotive manufacturer of luxury vehicles.'),
('Volvo', 'Sweden', 'Swedish premium automotive manufacturer focusing on safety.'),
('Porsche', 'Germany', 'German high-performance sports car manufacturer.'),
('McLaren', 'United Kingdom', 'British supercar manufacturer known for hypercars.');

-- 3. Insert Customers 
INSERT INTO customer (name, age, phone_number, email, dob, username, password, role, address) VALUES
('System Administrator', 30, '0909000111', 'admin@carselling.com', '1990-01-01', 'admin', 'admin123', 'admin', '123 Admin Street, District 1, Ho Chi Minh City'),
('Dinh Khai', 25, '0912345678', 'ronadokhaibeo@gmail.com', '1999-05-15', 'khaibeo', '123456', 'user', '45 Nguyen Hue, District 3, Ho Chi Minh City'),
('Minh Ly', 40, '0988777666', 'minhca@gmail.com', '1984-12-20', 'minhca', 'password', 'user', '789 Le Loi, District 1, Ho Chi Minh City'),
('Namdo', 22, '0365554444', 'namdo@gmail.com', '2002-03-08', 'namdo', 'security', 'user', '56 Tran Hung Dao, District 5, Ho Chi Minh City'),
('Lanlitdo', 35, '0901239876', 'trumcho@gmail.com', '1989-07-27', 'lan', 'lan123', 'user', '321 Pham Ngu Lao, District 1, Ho Chi Minh City');

-- 4. Insert Vehicles (Đầy đủ mô tả)
INSERT INTO vehicle (manufacturer_id, model, year, price, image_url, video_url, stock, description) VALUES
-- AUDI (ID 4)
(4, 'Audi RS6', 2023, 138888, './assets/img/audi_rs6.jpg', 'assets/video/audi_rs6.mp4', 5,
'The most brutal and practical super-wagon ever created – a 600 hp family estate that embarrasses genuine supercars on both road and track
Power: 600 hp 4.0L twin-turbo V8
0-100 km/h: 3.6 s
Top speed: 305 km/h (Dynamic Plus package)
Drivetrain: Legendary quattro with Drift Mode
Suspension: RS adaptive air suspension
Brakes: Carbon-ceramic · Wheels: 22-inch forged
Interior: Valcona leather RS sport seats · Virtual Cockpit Plus · Dual MMI touchscreens
Audio: Bang & Olufsen 3D Premium
The only car you’ll ever need.'),

(4, 'Audi Q3 SUV', 2024, 38595, './assets/img/audi_q3.webp', 'assets/video/Audi_Q3.mp4', 7,
'Compact luxury SUV that combines coupe-like elegance with genuine everyday usability and cutting-edge German technology
Power: 230 hp 2.0L TFSI quattro
0-100 km/h: 6.5 s
Top speed: 230 km/h
Lighting: Full Matrix LED with dynamic indicators
Exterior: S line package · Black optic styling
Interior: Alcantara sport seats · Multi-color ambient lighting
Technology: MMI Navigation Plus · Wireless Apple CarPlay · 360° camera
Safety: Adaptive cruise · Lane-keeping · Emergency assist
The perfect entry into the world of premium Audi.'),

(4, 'Audi RS7', 2024, 127800, './assets/img/audi_rs7.jpg', 'assets/video/Audi_RS7.mp4', 3,
'A four-door grand tourer that looks like it wants to fight everything on the road – 600 hp of pure aggression wrapped in stunning fastback design
Power: 600 hp 4.0L twin-turbo V8
0-100 km/h: 3.6 s
Top speed: 305 km/h
Aerodynamics: Active rear spoiler · Widened track
Lighting: HD Matrix LED laser + Dynamic OLED tail lights
Exhaust: RS sport with black oval tips
Interior: Full carbon package · Diamond-quilted RS seats
The most menacing luxury performance car money can buy.'),

(4, 'Audi A7 Sportback', 2024, 75000, './assets/img/audi_a7.jpg', 'assets/video/Audi_A7_Sportback.mp4', 6,
'One of the most beautiful four-door coupes ever designed – combining breathtaking elegance with cutting-edge luxury and mild-hybrid efficiency
Power: 340 hp 3.0L V6 mild-hybrid
0-100 km/h: 5.3 s
Top speed: 250 km/h
Interior: Open-pore wood · Valcona leather · 30-color ambient lighting
Comfort: Ventilated & massage seats · Air quality package with ioniser and fragrance
Audio: Bang & Olufsen 19-speaker 3D sound system
Features: Soft-close doors · Panoramic sunroof
S-Class levels of refinement in a sharper, sportier body.'),

-- BMW (ID 2)
(2, 'BMW X5M', 2024, 519000, './assets/img/bmw_x5m.png', 'assets/video/BMW_X5M.mp4', 2,
'The fastest, most powerful and most intimidating luxury SUV ever built by BMW – a true M monster in gentleman’s clothing
Power: 625 hp M TwinPower Turbo V8
0-100 km/h: 3.8 s
Top speed: 290 km/h
Drivetrain: M xDrive with rear-biased Drift Mode
Brakes: M Carbon ceramic
Interior: Full Merino leather · Carbon fiber everywhere · Curved panoramic display
Audio: Bowers & Wilkins Diamond surround
A supercar disguised as a family SUV.'),

(2, 'BMW X5 xDrive40i M Sport', 2025, 359000, './assets/img/bmw_x5_40i.jpg', 'assets/video/BMW_X5.mp4', 3,
'The perfect combination of luxury, technology and driving pleasure – the benchmark premium SUV that still delivers pure BMW DNA
Power: 380 hp inline-6 + 48V mild hybrid
0-100 km/h: 5.3 s
Top speed: 250 km/h
Package: M Sport Pro · Adaptive M suspension · Shadowline trim
Interior: Vernasca leather · Panoramic sky lounge LED roof
Audio: Harman Kardon 16-speaker surround
The ultimate all-rounder for business and pleasure.'),

(2, 'BMW M3 G80', 2024, 76995, './assets/img/bmw_m3_g80.png', 'assets/video/bmw_m3_g80.mp4', 4,
'The legend returns bolder, faster and more controversial than ever – the super-sedan that rewrote the rulebook and still dominates the segment
Power: 510 hp S58 twin-turbo inline-6
0-100 km/h: 3.9 s
Top speed: 290 km/h (M Driver’s Package)
Features: Carbon roof · M Carbon bucket seats · Laser headlights
Display: iDrive 8 curved screen · Head-Up Display
The benchmark performance sedan – angrier and better than ever.'),

-- VOLVO (ID 5)
(5, 'Volvo EM90', 2024, 80000, './assets/img/volvo_em90.avif', 'assets/video/VolvoEM90.mp4', 3,
'The world’s first true luxury electric MPV – a Scandinavian living room on wheels that redefines comfort, silence and sustainability
Power: 272 hp · Range: Up to 738 km (CLTC)
Charging: 10-80% in under 30 minutes
Interior: Full lounge cabin · Reclining second-row airline seats with massage
Entertainment: 15.6-inch ceiling screen · Bowers & Wilkins 21-speaker audio
Features: Built-in fridge · CleanZone air purification · Panoramic roof
The quietest and most serene people-mover ever created.'),

(5, 'Volvo S90', 2024, 55000, './assets/img/volvo_s90.png', 'assets/video/Volvo_S90.mp4', 5,
'Scandinavian flagship sedan that combines minimalist elegance with plug-in hybrid performance and unmatched safety innovation
Power: T8 Recharge – 455 hp total · Pure electric range: 100 km
0-100 km/h: 4.8 s
Interior: Nappa leather · Orrefors crystal gear selector · Open-pore wood
Safety: Pilot Assist II · City Safety · Best-in-class crash protection
Audio: Bowers & Wilkins premium sound
Luxury that cares about you and the planet.'),

(5, 'Volvo XC70', 2025, 45000, './assets/img/volvo_xc70.png', 'assets/video/Volvo_XC70.mp4', 4,
'The legendary all-road wagon reborn for the modern era – built for adventure, family and the toughest Scandinavian winters
Power: 300 hp mild-hybrid · AWD with Off-Road mode
Ground clearance: 210 mm
Special edition: Ocean Race styling · Water-resistant upholstery
Cargo: 1,800 liters · Roof rails · Tow bar ready
Suspension: Four-C active chassis · Hill descent control
The ultimate go-anywhere family wagon.'),

-- PORSCHE (ID 6)
(6, 'Porsche Taycan Turbo GT', 2025, 739000, './assets/img/taycan_gt.jpg', 'assets/video/Porsche_Taycan_Turbo_GT.mp4', 1,
'The fastest series-production electric car ever to lap the Nürburgring – a 1,019 hp missile that rewrites what EVs can do
Power: 1,019 hp with Launch Control
0-100 km/h: 2.2 s
Top speed: 305 km/h
Package: Weissach (-70 kg) · Active aerodynamics · Pirelli Trofeo R tires
Interior: Carbon bucket seats · No rear seats · Race-Tex trim
Current EV lap record holder – faster than many hypercars.'),

(6, 'Porsche 911 GT3', 2024, 175000, './assets/img/porsche_gt3.png', 'assets/video/porsche_911gt3.mp4', 2,
'The purest expression of the 911 – a high-revving, naturally aspirated masterpiece engineered for driving nirvana
Power: 510 hp 4.0L flat-6 · 9,000 rpm redline
0-100 km/h: 3.4 s
Top speed: 318 km/h
Features: Rear-wheel drive · Manual option · Carbon hood · Rear-axle steering
The closest thing to a race car you can legally drive on the road.'),

(6, 'Porsche GT3 RS', 2024, 225000, './assets/img/porsche_gt3_rs.png', 'assets/video/Porsche_GT3_RS.mp4', 1,
'The most extreme road-legal 911 ever created – a track weapon with number plates and active aerodynamics straight from motorsport
Power: 525 hp 4.0L flat-6 · 9,000 rpm
0-100 km/h: 3.2 s
Top speed: 296 km/h
Aerodynamics: DRS · Swan-neck wing · 860 kg downforce at 285 km/h
Weight: Only 1,450 kg
Built for lap times, engineered for obsession.'),

(6, 'Porsche 911', 2024, 110000, './assets/img/porsche_911.png', 'assets/video/porsche_911.mp4', 5,
'The icon that has defined sports cars for over 60 years – timeless design meets cutting-edge performance and everyday usability
Power: 450 hp twin-turbo flat-6
0-100 km/h: 3.7 s
Top speed: 308 km/h
Features: Wet Mode · Night Vision Assist · 18-way adaptive seats
Still the benchmark – and still the dream.'),

(6, 'Porsche 918 Spyder Weissach', 2015, 900000, './assets/img/porsche_918_weissach.png', 'assets/video/Porsche_918_spyder_weisach.mp4', 1,
'One of the holy trinity hypercars – 887 hp hybrid that dominated its era and still holds multiple records nearly a decade later
Power: 887 hp total (V8 + dual electric motors)
0-100 km/h: 2.6 s
Top speed: 345 km/h
Package: Weissach lightweight · Magnesium wheels
Production: Only 918 units worldwide
A future classic and collector masterpiece.'),

-- FERRARI (ID 3)
(3, 'Ferrari 812', 2024, 335000, './assets/img/ferrari_812.jpg', 'assets/video/ferrari_812.mp4', 1,
'The last front-engine, naturally aspirated V12 Ferrari – a 800 hp grand tourer that sings at nearly 9,000 rpm
Power: 800 hp 6.5L V12
0-100 km/h: 2.9 s
Top speed: Over 340 km/h
Engine: 8,900 rpm redline – the purest Ferrari soundtrack
Design: Pininfarina masterpiece
The end of an era – and the most beautiful one.'),

(3, 'Ferrari SF90', 2024, 625000, './assets/img/ferrari_sf90.png', 'assets/video/ferrarisf90.mp4', 1,
'Ferrari’s first series-production plug-in hybrid hypercar – 1,000 hp of electrified fury that redefined Prancing Horse performance
Power: 1,000 hp total (V8 twin-turbo + 3 electric motors)
0-100 km/h: 2.5 s
Top speed: 340 km/h
Electric range: 25 km silent running
Technology: eManettino · Torque vectoring AWD · 16-inch curved driver display
The future of Ferrari is here – and it’s breathtaking.'),

(3, 'Ferrari Roma', 2024, 220000, './assets/img/ferrari_roma.jpg', 'assets/video/Ferrari_Roma.mp4', 1,
'The most elegant and usable modern Ferrari – a stunning grand tourer that combines La Dolce Vita style with explosive V8 performance
Power: 620 hp 3.9L twin-turbo V8
0-100 km/h: 3.4 s
Top speed: 320 km/h
Design: “La Nuova Dolce Vita” philosophy
Interior: Passenger display · Frau leather · Dual cockpit layout
Beauty, power and sophistication in perfect harmony.'),

-- MERCEDES (ID 1)
(1, 'Maybach GLS 600', 2023, 214888, './assets/img/maybach_gls600.jpg', 'assets/video/maybach_2023.mp4', 3,
'The pinnacle of automotive luxury – an ultra-luxury SUV that makes first-class airline seats feel economy
Power: 557 hp 4.0L V8 mild-hybrid
0-100 km/h: 4.9 s
Rear cabin: Fully reclining seats with hot-stone massage · Champagne fridge · Folding tables
Features: Exclusive Maybach perfume atomizer · MBUX rear entertainment tablets
Silence: Best-in-class sound insulation
A palace that moves.'),

(1, 'Mercedes-AMG CLA 45 S 4MATIC+', 2024, 309000, './assets/img/cla45.jpg', 'assets/video/Mercedes_AMG_45S.mp4', 4,
'The most powerful four-cylinder production car ever built – a compact rocket that delivers genuine supercar performance in everyday clothing
Power: 421 hp hand-built 2.0L turbo
0-100 km/h: 4.0 s
Top speed: 270 km/h
Drivetrain: 4MATIC+ with Drift Mode · Race Start
Features: AMG Ride Control · Panoramic roof · AMG Performance seats
Pocket-sized fury with a monstrous heart.'),

(1, 'Mercedes CLE53 AMG 4MATIC+', 2024, 439000, './assets/img/cle53.jpg', 'assets/video/Mercedes_CLE53.mp4', 3,
'The perfect fusion of S-Class luxury and AMG performance in a stunning coupe body – elegance meets adrenaline
Power: 449 hp mild-hybrid inline-6
0-100 km/h: 4.2 s
Top speed: 270 km/h
Display: 52-inch MBUX Hyperscreen
Audio: Burmester 3D surround · Digital Light with projection
Interior: Nappa leather · Ambient lighting 64 colors
The grand tourer redefined.'),

(1, 'Mercedes-AMG SL63', 2024, 155000, './assets/img/sl63.png', 'assets/video/Mercedes_AMG_SL63.mp4', 2,
'The legendary SL returns as a full-fat AMG with fabric roof, all-wheel drive and open-top grand touring at its absolute finest
Power: 585 hp 4.0L V8 biturbo
0-100 km/h: 3.6 s
Roof: Fabric folding in 15 seconds
Drivetrain: 4MATIC+ all-wheel drive for the first time
Features: Rear-axle steering · Active roll stabilization
Pure freedom with brutal performance.'),

(1, 'Mercedes-AMG GT Black Series', 2021, 300000, './assets/img/gt_black.png', 'assets/video/AMG_GT_BLACK_SERIES.mp4', 1,
'The most extreme AMG road car ever created – a track-bred monster with 730 hp and aerodynamics that generate real downforce
Power: 730 hp flat-plane crank V8
0-100 km/h: 3.2 s
Top speed: 325 km/h
Aerodynamics: Over 400 kg downforce at 250 km/h
Nürburgring lap record holder in its class
A street-legal race car with no compromises.'),

-- MCLAREN (ID 7)
(7, 'McLaren Senna', 2020, 1000000, './assets/img/mclaren_senna.png', 'assets/video/Maclaren_senna.mp4', 1,
'The most extreme road-legal track car ever made by McLaren – named after the greatest F1 driver of all time
Power: 800 hp twin-turbo V8
0-100 km/h: 2.8 s
Top speed: 340 km/h
Downforce: 800 kg · Dry weight: 1,198 kg
Active aero everywhere · Carbon everything
Only 500 units exist – each one a masterpiece.'),

(7, 'McLaren Speedtail', 2020, 2100000, './assets/img/mclaren_speedtail.png', 'assets/video/Mclaren_Speedtail.mp4', 1,
'The fastest and most aerodynamic road car McLaren has ever built – a 403 km/h hyper-GT with central driving position
Power: 1,070 hp hybrid powertrain
Top speed: 403 km/h
0-300 km/h: 12.8 seconds
Seating: Three-seat layout with driver in the center
Production: Only 106 units worldwide
The ultimate expression of speed and exclusivity.'),

(7, 'McLaren 750S', 2024, 330000, './assets/img/mclaren_750s.webp', 'assets/video/McLaren_750S_Spider.mp4', 2,
'The lightest and most powerful series-production supercar McLaren has ever made – the new benchmark in driving purity
Power: 750 hp 4.0L twin-turbo V8
0-100 km/h: 2.8 s
Top speed: 332 km/h
Weight savings: 30 kg lighter than 720S
Chassis: Proactive Chassis Control III
Interior: Carbon racing seats · Alcantara everywhere
Open-top version available – pure exhilaration redefined.');

-- 5. Insert Orders
-- Payment ID: 2=Cash, 1=Bank, 3=Credit
INSERT INTO orders (customer_id, status, created_at, total_amount, payment_method_id) VALUES
(2, 4, '2024-01-15 10:30:00', 458000000, 2),   
(3, 2, '2024-02-20 14:15:00', 665000000, 1),   
(4, 4, '2024-03-05 09:00:00', 559000000, 3),   
(2, 2, '2024-04-10 16:45:00', 1090000000, 1),  
(5, 3, '2024-05-01 11:20:00', 1050000000, 2);  

-- 6. Insert Order Detail
-- Đã xóa payment_method
INSERT INTO order_detail 
(vehicle_id, order_id, amount, quantity, created_at) 
VALUES
(1, 1, 458000000, 1, '2024-01-15 10:30:00'),
(3, 2, 665000000, 1, '2024-02-20 14:15:00'),
(2, 3, 559000000, 1, '2024-03-05 09:00:00'),
(5, 4, 1090000000, 1, '2024-04-10 16:45:00'),
(4, 5, 1050000000, 1, '2024-05-01 11:20:00');