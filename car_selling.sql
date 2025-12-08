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
)

-- 1.Tạo Manufacturers
INSERT INTO manufacturer (name, country, description) VALUES
('Toyota', 'Japan', 'Reliable and durable automotive brand from Japan.'),
('Honda', 'Japan', 'Famous for sporty design and powerful engines.'),
('Ford', 'USA', 'American brand specializing in pickup trucks and SUVs.'),
('Hyundai', 'South Korea', 'Korean manufacturer known for modern design and technology.'),
('VinFast', 'Vietnam', 'Vietnamese smart electric vehicle manufacturer.'),
('Ferrari', 'Italy', 'Famous Italian sports car manufacturer.'),          
('BMW', 'Germany', 'German luxury vehicle brand.'),                      
('Lamborghini', 'Italy', 'Iconic Italian brand known for luxury supercars.'), 
('Mercedes-Benz', 'Germany', 'One of the leading luxury car brands in the world.'), 
('Kia', 'South Korea', 'South Korean manufacturer, popular for city cars.'), 
('Mazda', 'Japan', 'Japanese automaker known for Kodo design language.');

-- 2. Tạo Vehicles
INSERT INTO vehicle (manufacturer_id, model, year, price, image_url, stock, description) VALUES
(1, 'Toyota Vios', 2024, 458000000, './assets/img/vios.jpg', 20, 'The national sedan, fuel-efficient and highly durable.'),
(2, 'Honda City', 2024, 559000000, './assets/img/city.jpg', 15, 'Sporty sedan with the best driving sensation in its class.'),
(3, 'Ford Ranger', 2023, 665000000, './assets/img/ranger.jpg', 10, 'King of pickup trucks, powerful off-road capabilities.'),
(4, 'Hyundai SantaFe', 2024, 1050000000, './assets/img/santafe.jpg', 8, 'Spacious 7-seater SUV, packed with modern technology.'),
(5, 'VinFast VF8', 2024, 1090000000, './assets/img/vf8.jpg', 12, 'Luxury electric SUV with sophisticated Italian design.'),
(6, 'Ferrari SF90 Stradale', 2024, 34000000000, './assets/img/sf90.jpg', 2, '1000hp Plug-in Hybrid supercar, 0-100km/h in 2.5 seconds.'),
(7, 'BMW i7 xDrive60', 2024, 7200000000, './assets/img/bmw_i7.jpg', 5, 'Flagship electric luxury sedan with high-tech interior.'),
(8, 'Lamborghini Huracán Tecnica', 2023, 19000000000, './assets/img/huracan.jpg', 3, 'V10 naturally aspirated supercar with aerodynamic design.'),
(9, 'Mercedes-Maybach S680', 2024, 17000000000, './assets/img/maybach.jpg', 4, 'The pinnacle of luxury sedans, featuring a smooth V12 engine.'),
(9, 'Mercedes-AMG G63', 2024, 11800000000, './assets/img/g63.jpg', 6, 'The off-road king, iconic boxy design with powerful performance.'),
(10, 'Kia Morning Premium', 2024, 429000000, './assets/img/morning.jpg', 25, 'Compact urban hatchback, fuel-efficient and flexible.'),
(11, 'Mazda 2 Sport', 2024, 550000000, './assets/img/mazda2.jpg', 18, 'Stylish hatchback with the best sporty driving feel in its class.');

-- 3. Tạo Customers 
INSERT INTO customer (name, age, phone_number, email, dob, username, password, role) VALUES
('System Administrator', 30, '0909000111', 'admin@carselling.com', '1990-01-01', 'admin', 'admin123', 'admin'),
('Dinh Khai', 25, '0912345678', 'ronadokhaibeo@gmail.com', '1999-05-15', 'khaibeo', '123456', 'user'),
('Minh Ly', 40, '0988777666', 'minhca@gmail.com', '1984-12-20', 'minhca', 'password', 'user'),
('Namdo', 22, '0365554444', 'namdo@gmail.com', '2002-03-08', 'namdo', 'security', 'user'),
('Lanlitdo', 35, '0901239876', 'trumcho@gmail.com', '1989-07-27', 'lan', 'lan123', 'user');

-- 4. Tạo Orders
INSERT INTO `order` (customer_id, vehicle_id, amount, payment_method, status, payment_date) VALUES
(2, 1, 458000000, 'Cash', 'Completed', '2024-01-15 10:30:00'),          
(3, 3, 665000000, 'Bank Transfer', 'Processing', '2024-02-20 14:15:00'),   
(4, 2, 559000000, 'Credit Card', 'Completed', '2024-03-05 09:00:00'),      
(2, 5, 1090000000, 'Bank Transfer', 'Pending', '2024-04-10 16:45:00'),     
(5, 4, 1050000000, 'Cash', 'Cancelled', '2024-05-01 11:20:00');   