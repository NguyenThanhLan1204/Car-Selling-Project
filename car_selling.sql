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
    password VARCHAR(255),
    role VARCHAR(20) DEFAULT 'user' 
);

-- 4. Bảng Order (Đơn hàng)
CREATE TABLE `order` (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT, 
    vehicle_id INT,  
    amount DECIMAL(15, 2), 
    payment_method VARCHAR(50),
    status VARCHAR(50), 
    payment_date DATETIME, 
    CONSTRAINT fk_order_customer FOREIGN KEY (customer_id) REFERENCES customer(customer_id),
    CONSTRAINT fk_order_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicle(vehicle_id)
);

-- 1. Insert Manufacturers
INSERT INTO manufacturer (name, country, description) VALUES
('Toyota', 'Japan', 'Reliable and durable automotive brand from Japan.'),
('Honda', 'Japan', 'Famous for sporty design and powerful engines.'),
('Ford', 'USA', 'American brand specializing in pickup trucks and SUVs.'),
('Hyundai', 'South Korea', 'Korean manufacturer known for modern design and technology.'),
('VinFast', 'Vietnam', 'Vietnamese smart electric vehicle manufacturer.');

-- 2. Insert Vehicles
INSERT INTO vehicle (manufacturer_id, model, year, price, image_url, stock, description) VALUES
(1, 'Toyota Vios', 2024, 458000000, './assets/img/vios.jpg', 20, 'The national sedan, fuel-efficient and highly durable.'),
(2, 'Honda City', 2024, 559000000, './assets/img/city.jpg', 15, 'Sporty sedan with the best driving sensation in its class.'),
(3, 'Ford Ranger', 2023, 665000000, './assets/img/ranger.jpg', 10, 'King of pickup trucks, powerful off-road capabilities.'),
(4, 'Hyundai SantaFe', 2024, 1050000000, './assets/img/santafe.jpg', 8, 'Spacious 7-seater SUV, packed with modern technology.'),
(5, 'VinFast VF8', 2024, 1090000000, './assets/img/vf8.jpg', 12, 'Luxury electric SUV with sophisticated Italian design.');

-- 3. Insert Customers 
INSERT INTO customer (name, age, phone_number, email, dob, username, password, role) VALUES
('System Administrator', 30, '0909000111', 'admin@carselling.com', '1990-01-01', 'admin', 'admin123', 'admin'),
('Dinh Khai', 25, '0912345678', 'ronadokhaibeo@gmail.com', '1999-05-15', 'khaibeo', '123456', 'user'),
('Minh Ly', 40, '0988777666', 'minhca@gmail.com', '1984-12-20', 'minhca', 'password', 'user'),
('Namdo', 22, '0365554444', 'namdo@gmail.com', '2002-03-08', 'namdo', 'security', 'user'),
('Lanlitdo', 35, '0901239876', 'trumcho@gmail.com', '1989-07-27', 'lan', 'lan123', 'user');

-- 4. Insert Orders
INSERT INTO `order` (customer_id, vehicle_id, amount, payment_method, status, payment_date) VALUES
(2, 1, 458000000, 'Cash', 'Completed', '2024-01-15 10:30:00'),          
(3, 3, 665000000, 'Bank Transfer', 'Processing', '2024-02-20 14:15:00'),   
(4, 2, 559000000, 'Credit Card', 'Completed', '2024-03-05 09:00:00'),      
(2, 5, 1090000000, 'Bank Transfer', 'Pending', '2024-04-10 16:45:00'),     
(5, 4, 1050000000, 'Cash', 'Cancelled', '2024-05-01 11:20:00');            -