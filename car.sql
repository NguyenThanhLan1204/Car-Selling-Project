CREATE DATABASE IF NOT EXISTS user_car_system;
USE user_car_system;

-- TABLE 1: users

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    dob DATE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    nationality VARCHAR(50),
    phonenumber VARCHAR(20)
);

-- TABLE 2: cars

CREATE TABLE cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    color VARCHAR(50),
    brand VARCHAR(50),
    picture VARCHAR(255),
    price BIGINT,
    year INT,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


INSERT INTO users (username, dob, password, email, nationality, phonenumber)
VALUES 
('admin', '1990-01-01', '123456', 'admin@example.com', 'Vietnam', '0123456789'),
('john', '1995-05-15', 'password123', 'john@gmail.com', 'USA', '0987654321'),
('lisa', '1998-07-10', 'lisa@123', 'lisa@yahoo.com', 'Canada', '0912345678'),
('minh', '2000-03-25', 'minhpass', 'minh@gmail.com', 'Vietnam', '0905123123'),
('alex', '1992-11-30', 'alex321', 'alex@hotmail.com', 'UK', '0845123456'),
('sakura', '1999-09-09', 'sakura99', 'sakura@jp.com', 'Japan', '0812345678'),
('smallsheep', '2004-04-12', 'yuu47319922', 'maria@es.com', 'Italia', '0961971204'),
('khaibeo', '1994-08-22', '890pass', 'khaibeo@us.com', 'Cambodia', '0978123456'),
('namdo', '2001-02-14', 'namdo2025', 'namdo@vn.com', 'Vietnam', '0918123123'),
('enzo', '1988-12-05', 'enzoF1', 'enzo@it.com', 'Italy', '0888999777');

INSERT INTO cars (name, color, brand, picture, price, year, user_id)
VALUES
('Toyota Camry', 'White', 'Toyota', 'cars/images1.jpg', 28855, 2020, 1),
('Honda Civic', 'Black', 'Honda', 'cars/images2.jpg', 20350, 2019, 2),
('Mazda CX5', 'Red', 'Mazda', 'cars/images3.jpg', 26595, 2021, 3),
('Hyundai Tucson', 'Blue', 'Hyundai', 'cars/images4.jpeg', 24950, 2022, 4),
('Ford Ranger', 'Silver', 'Ford', 'cars/images5.jpeg', 27690, 2018, 5),
('Nissan Altima', 'Gray', 'Nissan', 'cars/images6.jpeg', 25250, 2020, 6),
('Kia Seltos', 'Orange', 'Kia', 'cars/images7.jpeg', 23690, 2023, 7),
('Ferrari 812 Superfast', 'Red', 'Ferrari', 'cars/images8.jpeg', 335000, 2024, 8),
('Lamborghini Aventador', 'Green', 'Lamborghini', 'cars/images9.jpeg', 507353, 2023, 9),
('Bugatti Chiron', 'Blue', 'Bugatti', 'cars/images10.jpeg', 3300000, 2022, 10),
('Porsche 911', 'Gray', 'Porsche', 'cars/images11.jpeg', 100550, 2021, 1),
('Audi A6', 'White', 'Audi', 'cars/images12.jpeg', 54900, 2021, 6),
('BMW M4', 'Black', 'BMW', 'cars/images13.jpeg', 80875, 2023, 7),
('Mercedes AMG S63 E', 'White', 'Mercedes-Benz', 'cars/images14.jpeg', 189800, 2024, 8);








