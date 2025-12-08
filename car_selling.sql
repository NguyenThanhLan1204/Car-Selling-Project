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
    category VARCHAR(50) NOT NULL,
    year INT,
    price DECIMAL(15, 2), 
    image_url VARCHAR(500),
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
('Toyota', 'Japan', 'Japanese multinational automotive manufacturer.'),                        
('Ferrari', 'Italy', 'Italian luxury sports car manufacturer based in Maranello.'),              
('Audi', 'Germany', 'German automotive manufacturer of luxury vehicles.');

-- 2. Tạo Vehicles
INSERT INTO vehicle (manufacturer_id, model, category, year, price, image_url, stock, description) VALUES
-- --- 1. SEDAN 
(1, 'Mercedes-Maybach S680','sedan', 2024, 17000000000, './assets/img/maybach.jpg', 3, 'The absolute pinnacle of luxury sedans, V12 engine.'), 
(3, 'Toyota Camry 2.5Q','sedan', 2024, 1405000000, './assets/img/camry.jpg', 10, 'Business sedan, spacious and reliable.'),
(2, 'BMW 740i Pure Excellence','sedan', 2024, 6599000000, './assets/img/740i.jpg', 5, 'Flagship luxury sedan with theater screen.'),

-- --- 2. HATCHBACK 
(3, 'Toyota Yaris','hatchback', 2024, 684000000, './assets/img/yaris.jpg', 15, 'Compact hatchback, easy to drive in city.'),
(1, 'Mercedes A-Class','hatchback', 2024, 2400000000, './assets/img/aclass.jpg', 8, 'Luxury compact hatchback with modern tech.'),
(2, 'BMW 118i SportLine','hatchback', 2024, 1800000000, './assets/img/bmw118i.jpg', 6, 'Sporty hatchback, driving pleasure.'),

-- --- 3. SUV 
(5, 'Audi Q7 45 TFSI','suv', 2024, 3770000000, './assets/img/audi_q7.jpg', 5, 'Versatile 7-seater SUV, quattro technology.'), 
(1, 'Mercedes-AMG G63','suv', 2024, 11750000000, './assets/img/g63.jpg', 2, 'The off-road king, iconic boxy design.'),
(2, 'BMW X7 xDrive40i','suv', 2024, 6299000000, './assets/img/x7.jpg', 4, 'The President, 7-seater luxury SUV.'),

-- --- 4. MPV 
(3, 'Toyota Alphard','mpv', 2024, 4370000000, './assets/img/alphard.jpg', 3, 'Luxury MPV, business class on wheels.'),
(1, 'Mercedes V-Class V250','mpv', 2024, 3039000000, './assets/img/v250.jpg', 5, 'Premium MPV for family and business.'),
(3, 'Toyota Innova Cross','mpv', 2024, 990000000, './assets/img/innova.jpg', 12, 'Popular MPV, hybrid option available.'),

-- --- 5. PICKUP 
(3, 'Toyota Hilux Adventure','pickup', 2024, 1077000000, './assets/img/hilux.jpg', 8, 'Tough and durable pickup truck.'),
(3, 'Toyota Hilux E','pickup', 2024, 850000000, './assets/img/hilux_e.jpg', 10, 'Standard pickup for work.'),

-- --- 6. SUPERCAR 
(4, 'Ferrari SF90 Stradale','supercar', 2024, 34000000000, './assets/img/sf90.jpg', 1, '1000HP Hybrid Supercar, 0-100km/h in 2.5s.'), 
(4, 'Ferrari 296 GTB','supercar', 2024, 23000000000, './assets/img/296gtb.jpg', 2, 'V6 Hybrid, defining fun to drive.'), 
(4, 'Ferrari 296 Speciale A','supercar', 2024, 29000000000, './assets/img/296speciale.jpg', 1, 'Open-top special edition, pure emotion.'), 
(5, 'Audi RS e-tron GT','supercar', 2024, 5900000000, './assets/img/audi_rs.jpg', 3, 'Electric high-performance grand tourer.'); 

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
(5, 1, '2024-05-01 11:20:00');   -- Cancelled / Pending / your rule

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
