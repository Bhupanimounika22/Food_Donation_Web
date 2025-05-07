-- Create database if not exists
-- CREATE DATABASE IF NOT EXISTS hackathon;
-- USE hackathon;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    user_type ENUM('donor', 'recipient', 'admin') NOT NULL DEFAULT 'donor',
    profile_image VARCHAR(255),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Food donations table
CREATE TABLE IF NOT EXISTS food_donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    food_type ENUM('raw_food', 'cooked_food') NOT NULL,
    food_name VARCHAR(100) NOT NULL,
    food_details TEXT,
    quantity VARCHAR(50),
    serves_people INT,
    expiry_time VARCHAR(100) NOT NULL,
    pickup_address TEXT NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    status ENUM('available', 'reserved', 'completed', 'expired') DEFAULT 'available',
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Reservations table
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donation_id INT NOT NULL,
    recipient_id INT NOT NULL,
    reservation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    pickup_time TIMESTAMP NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (donation_id) REFERENCES food_donations(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Feedback table
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    donation_id INT,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (donation_id) REFERENCES food_donations(id) ON DELETE SET NULL
);

-- Insert admin user
INSERT INTO users (first_name, last_name, email, password, contact, user_type)
VALUES ('Admin', 'User', 'admin@donatehere.com', '$2y$10$8KlsQB8vWktGYYjqJQjGIeWHDjO3PzxUCpIYZ3P.fOJO0jQw2u4Uy', '9876543210', 'admin');

-- Insert sample users
INSERT INTO users (first_name, last_name, email, password, contact, user_type, address)
VALUES 
('John', 'Doe', 'john@example.com', '$2y$10$8KlsQB8vWktGYYjqJQjGIeWHDjO3PzxUCpIYZ3P.fOJO0jQw2u4Uy', '9876543210', 'donor', '123 Main St, Anytown'),
('Jane', 'Smith', 'jane@example.com', '$2y$10$8KlsQB8vWktGYYjqJQjGIeWHDjO3PzxUCpIYZ3P.fOJO0jQw2u4Uy', '9876543211', 'recipient', '456 Oak Ave, Anytown');

-- Insert sample donations
INSERT INTO food_donations (donor_id, food_type, food_name, food_details, quantity, serves_people, expiry_time, pickup_address, contact_number, status)
VALUES 
(2, 'cooked_food', 'Vegetable Biryani', 'Freshly cooked vegetable biryani with raita', NULL, 20, 'Today until 9 PM', '123 Main St, Anytown', '9876543210', 'available'),
(2, 'raw_food', 'Rice and Lentils', '5kg rice and 2kg lentils', '7kg', NULL, 'No expiry', '123 Main St, Anytown', '9876543210', 'available');