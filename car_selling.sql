DROP DATABASE IF EXISTS car_selling;
CREATE DATABASE car_selling;
USE car_selling;

-- 1. Bảng Customer 
CREATE TABLE customer (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    phone VARCHAR(50),
    email VARCHAR(200),
    address TEXT,
    dob DATE
);

-- 2. Bảng Manufacturer 
CREATE TABLE manufacturer (
    manufacturer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    country VARCHAR(100)
);

-- 3. Bảng Dealer 
CREATE TABLE dealer (
    dealer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    location TEXT,
    contact VARCHAR(100)
);

-- 4. Bảng Vehicle Model 
CREATE TABLE vehicle_model (
    model_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    trim VARCHAR(100),
    body_type VARCHAR(50),
    manufacturer_id INT NOT NULL,
    year INT,
    base_price NUMERIC(12,2),
    FOREIGN KEY (manufacturer_id) REFERENCES manufacturer(manufacturer_id)
);

-- 5. Bảng Salesperson 
CREATE TABLE salesperson (
    salesperson_id INT AUTO_INCREMENT PRIMARY KEY,
    dealer_id INT,
    name VARCHAR(200) NOT NULL,
    phone VARCHAR(50),
    commission_rate NUMERIC(5,2) DEFAULT 3.00,
    FOREIGN KEY (dealer_id) REFERENCES dealer(dealer_id)
);

-- 6. Bảng Vehicle 
CREATE TABLE vehicle (
    vehicle_id INT AUTO_INCREMENT PRIMARY KEY,
    vin VARCHAR(50) NOT NULL UNIQUE,
    model_id INT NOT NULL,
    color VARCHAR(50),
    status VARCHAR(20) DEFAULT 'available',
    sale_price NUMERIC(12,2),
    arrival_date DATE,
    dealer_id INT NOT NULL,
    FOREIGN KEY (model_id) REFERENCES vehicle_model(model_id),
    FOREIGN KEY (dealer_id) REFERENCES dealer(dealer_id)
);

-- 7. Bảng Sale 
CREATE TABLE sale (
    sale_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL UNIQUE,
    customer_id INT NOT NULL,
    salesperson_id INT NOT NULL,
    sale_date DATE NOT NULL DEFAULT (CURRENT_DATE),
    total_amount NUMERIC(12,2) NOT NULL,
    sale_type VARCHAR(20),
    FOREIGN KEY (vehicle_id) REFERENCES vehicle(vehicle_id),
    FOREIGN KEY (customer_id) REFERENCES customer(customer_id),
    FOREIGN KEY (salesperson_id) REFERENCES salesperson(salesperson_id)
);

-- 8. Bảng Payment 
CREATE TABLE payment (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    payment_date DATE NOT NULL DEFAULT (CURRENT_DATE),
    amount NUMERIC(12,2) NOT NULL,
    method VARCHAR(50),
    note TEXT,
    FOREIGN KEY (sale_id) REFERENCES sale(sale_id)
);


INSERT INTO manufacturer (name, country) VALUES
('Toyota', 'Japan'), ('Honda', 'Japan'), ('Ford', 'USA'), ('VinFast', 'Vietnam'), ('Mercedes-Benz', 'Germany'),
('BMW', 'Germany'), ('Hyundai', 'South Korea'), ('Kia', 'South Korea'), ('Mazda', 'Japan'), ('Audi', 'Germany');

INSERT INTO dealer (name, location, contact) VALUES
('Toyota My Dinh', '15 Pham Hung, Ha Noi', '0901234567'), ('Honda Tay Ho', '196 Lac Long Quan, Ha Noi', '0902345678'),
('Ford Thang Long', '105 Lang Ha, Ha Noi', '0903456789'), ('VinFast Landmark 81', '208 Nguyen Huu Canh, HCMC', '0904567890'),
('Mercedes Haxaco', '46 Lang Ha, Ha Noi', '0905678901'), ('BMW Phu My Hung', '808 Nguyen Van Linh, HCMC', '0906789012'),
('Hyundai Dong Do', '11 Hoang Cau, Ha Noi', '0907890123'), ('Kia Go Vap', '189 Nguyen Oanh, HCMC', '0908901234'),
('Mazda Le Van Luong', '68 Le Van Luong, Ha Noi', '0909012345'), ('Audi Danang', '86 Duy Tan, Da Nang', '0910123456');

INSERT INTO customer (name, phone, email, address, dob) VALUES
('Nguyen Dinh Khai', '0987654321', 'khaibeogmail.com', 'Ba Dinh, Ha Noi', '1985-05-20'),
('Madam Lan', '0988776655', 'lan@gmail.com', 'Quan 1, HCMC', '1990-08-15'),
('Nguyen Huong Ly', '0977665544', 'chiyeuaminh@gmail.com', 'Cau Giay, Ha Noi', '1982-12-01'),
('Pham Tan Minh', '0966554433', 'minhcute@gmail.com', 'Hai Hau, Nam Dinh', '1995-03-10'),
('Hoang Van Khoa', '0955443322', 'hoangvane@gmail.com', 'Thu Duc, HCMC', '1988-07-25'),
('Nguyen Anh My', '0944332211', 'mymy@gmail.com', 'Dong Da, Ha Noi', '1992-11-30'),
('Ngo Ba Kha', '0933221100', 'vuvang@gmail.com', 'Ngo Quyen, Hai Phong', '1979-02-14'),
('Truong My Lan', '0922110099', 'mylan@gmail.com', 'Ninh Kieu, Can Tho', '1998-09-05'),
('Vu Ha Nam', '0911009988', 'thitcay@gmail.com', 'Thanh Xuan, Ha Noi', '1986-06-18'),
('Ho Quang Hieu', '0900998877', 'ngothihieu@gmail.com', 'Bien Hoa, Dong Nai', '1993-01-22');

INSERT INTO vehicle_model (name, trim, body_type, manufacturer_id, year, base_price) VALUES
('Camry', '2.5Q', 'Sedan', 1, 2024, 1200000000), ('CR-V', 'L Sensing', 'SUV', 2, 2024, 1100000000),
('Ranger', 'Wildtrak', 'Pickup', 3, 2023, 950000000), ('VF 8', 'Plus', 'SUV', 4, 2023, 1300000000),
('C-Class', 'C300 AMG', 'Sedan', 5, 2024, 2100000000), ('X5', 'xDrive40i', 'SUV', 6, 2023, 4000000000),
('Santa Fe', 'Premium', 'SUV', 7, 2024, 1350000000), ('Seltos', 'Premium', 'SUV', 8, 2023, 750000000),
('CX-5', 'Signature', 'SUV', 9, 2024, 980000000), ('Q7', '45 TFSI', 'SUV', 10, 2023, 3800000000);

INSERT INTO salesperson (dealer_id, name, phone, commission_rate) VALUES
(1, 'Le Minh Tuan', '0912345123', 2.5), (2, 'Pham Thu Ha', '0923456234', 3.0),
(3, 'Nguyen Duc Hung', '0934567345', 2.8), (4, 'Tran Ngoc Lan', '0945678456', 3.5),
(5, 'Vu Tuan Anh', '0956789567', 4.0), (6, 'Hoang Mai Phuong', '0967890678', 3.2),
(7, 'Doan Van Hau', '0978901789', 2.5), (8, 'Ly Thi Mo', '0989012890', 2.9),
(9, 'Trinh Van Quyet', '0990123901', 3.0), (10, 'Dang Thuy Tram', '0901234012', 3.5);

INSERT INTO vehicle (vin, model_id, color, status, sale_price, arrival_date, dealer_id) VALUES
('VINTOY001', 1, 'Black', 'sold', 1250000000, '2024-01-10', 1), ('VINHON002', 2, 'White', 'sold', 1120000000, '2024-01-15', 2),
('VINFOR003', 3, 'Orange', 'sold', 960000000, '2023-12-20', 3), ('VINVIN004', 4, 'Blue', 'sold', 1290000000, '2024-02-01', 4),
('VINMER005', 5, 'Red', 'sold', 2150000000, '2024-01-05', 5), ('VINBMW006', 6, 'Black', 'sold', 4050000000, '2023-11-30', 6),
('VINHYU007', 7, 'Silver', 'sold', 1370000000, '2024-01-20', 7), ('VINKIA008', 8, 'White', 'sold', 760000000, '2024-02-10', 8),
('VINMAZ009', 9, 'Red', 'sold', 990000000, '2024-01-25', 9), ('VINAUD010', 10, 'Grey', 'sold', 3850000000, '2023-12-15', 10);

INSERT INTO sale (vehicle_id, customer_id, salesperson_id, sale_date, total_amount, sale_type) VALUES
(1, 1, 1, '2024-02-01', 1250000000, 'Direct'), (2, 2, 2, '2024-02-05', 1120000000, 'Installment'),
(3, 3, 3, '2024-01-15', 960000000, 'Direct'), (4, 4, 4, '2024-02-15', 1290000000, 'Installment'),
(5, 5, 5, '2024-01-20', 2150000000, 'Direct'), (6, 6, 6, '2023-12-25', 4050000000, 'Direct'),
(7, 7, 7, '2024-02-10', 1370000000, 'Direct'), (8, 8, 8, '2024-02-20', 760000000, 'Installment'),
(9, 9, 9, '2024-02-05', 990000000, 'Direct'), (10, 10, 10, '2024-01-10', 3850000000, 'Direct');

INSERT INTO payment (sale_id, payment_date, amount, method, note) VALUES
(1, '2024-02-01', 1250000000, 'Bank Transfer', 'Full payment'), (2, '2024-02-05', 300000000, 'Credit Card', 'Down payment (30%)'),
(3, '2024-01-15', 960000000, 'Cash', 'Full payment'), (4, '2024-02-15', 500000000, 'Bank Transfer', 'First installment'),
(5, '2024-01-20', 2150000000, 'Bank Transfer', 'Full payment'), (6, '2023-12-25', 1000000000, 'Bank Transfer', 'Deposit'),
(7, '2024-02-10', 1370000000, 'Bank Transfer', 'Full payment'), (8, '2024-02-20', 200000000, 'Cash', 'Down payment'),
(9, '2024-02-05', 990000000, 'Bank Transfer', 'Full payment'), (10, '2024-01-10', 3850000000, 'Bank Transfer', 'Company payment');