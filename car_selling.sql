CREATE DATABASE car_selling ;
USE car_selling ;

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

-- 3. Bảng Customer (Khách hàng)
CREATE TABLE customer (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    age INT, 
    phone_number VARCHAR(20),
    email VARCHAR(255),
    dob DATE, -- Ngày sinh
    username VARCHAR(100) UNIQUE, 
    password VARCHAR(255) 
);

-- 4. Bảng Admin (Quản trị viên)
CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    email VARCHAR(255)
);

-- 5. Bảng Order (Đơn hàng)
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