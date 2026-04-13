-- schema.sql

-- Database creation (Run these commands in phpMyAdmin)
CREATE DATABASE IF NOT EXISTS `myschooldesk_db`;
USE `myschooldesk_db`;

-- 1. Users Table (Admin, School Owners, Parents)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `role` ENUM('admin', 'school', 'parent') NOT NULL DEFAULT 'parent',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Schools Table
CREATE TABLE IF NOT EXISTS `schools` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL, -- Owner of the school listing
    `name` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(200) UNIQUE NOT NULL, -- For SEO URLs: e.g., eurokids-akota-vadodara
    `address` TEXT,
    `city` VARCHAR(100),
    `state` VARCHAR(100),
    `board` VARCHAR(100), -- e.g., CBSE, ICSE, State Board
    `photos` TEXT, -- JSON array of image paths
    `fees_min` DECIMAL(10,2) DEFAULT NULL,
    `fees_max` DECIMAL(10,2) DEFAULT NULL,
    `facilities` TEXT, -- Comma separated or JSON: AC, Transport, etc.
    `classes_offered` VARCHAR(100), -- e.g., Playgroup to 12th
    `teachers_strength` INT DEFAULT NULL,
    `teacher_min_qual` VARCHAR(100),
    `teacher_max_qual` VARCHAR(100),
    `contact_email` VARCHAR(150),
    `contact_phone` VARCHAR(50),
    `description` TEXT,
    `is_verified` BOOLEAN DEFAULT FALSE,
    `is_thick_sign` BOOLEAN DEFAULT FALSE, -- 'Thick sign' requirement from the user
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `sort_order` INT DEFAULT 0, -- Used to list school above or below order
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- 3. Parent Enquiries Table
CREATE TABLE IF NOT EXISTS `enquiries` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `lead_id` VARCHAR(50) UNIQUE NOT NULL, -- MSD12345
    `parent_name` VARCHAR(100) NOT NULL,
    `mobile` VARCHAR(20) NOT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `child_name` VARCHAR(100) NOT NULL,
    `child_dob` DATE DEFAULT NULL,
    `child_class` VARCHAR(50) NOT NULL,
    `location` VARCHAR(100),
    `budget_range` VARCHAR(100),
    `board_preference` VARCHAR(100),
    `message` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. School-Enquiry Mapping (Since a parent can select multiple schools for one enquiry)
CREATE TABLE IF NOT EXISTS `enquiry_school_mapping` (
    `enquiry_id` INT NOT NULL,
    `school_id` INT NOT NULL,
    `admission_status` ENUM('pending', 'admission_done', 'not_converted') DEFAULT 'pending',
    PRIMARY KEY (`enquiry_id`, `school_id`),
    FOREIGN KEY (`enquiry_id`) REFERENCES `enquiries`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`school_id`) REFERENCES `schools`(`id`) ON DELETE CASCADE
);

-- 5. Reviews Table
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `school_id` INT NOT NULL,
    `parent_name` VARCHAR(100) NOT NULL,
    `rating` TINYINT(1) CHECK (`rating` BETWEEN 1 AND 5),
    `review_text` TEXT,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`school_id`) REFERENCES `schools`(`id`) ON DELETE CASCADE
);

-- Insert Default Admin User
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role`) VALUES 
('Super Admin', 'admin', 'admin@myschooldesk.co.in', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'); 
-- Note: Default password for admin is 'password' (hashed with bcrypt).

-- Optional sample schools for quick testing
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role`, `phone`) VALUES
('EuroKids Owner', 'eurokids', 'eurokids@myschooldesk.co.in', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'school', '9999999991'),
('DPS Owner', 'dps', 'dps@myschooldesk.co.in', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'school', '9999999992');

INSERT INTO `schools`
(`user_id`, `name`, `slug`, `address`, `city`, `state`, `board`, `fees_min`, `fees_max`, `facilities`, `classes_offered`,
`teachers_strength`, `teacher_min_qual`, `teacher_max_qual`, `contact_email`, `contact_phone`, `description`, `is_verified`,
`is_thick_sign`, `status`, `sort_order`)
VALUES
((SELECT id FROM users WHERE email='eurokids@myschooldesk.co.in' LIMIT 1), 'EuroKids Preschool', 'eurokids-akota-vadodara',
'Akota Main Road', 'Vadodara', 'Gujarat', 'CBSE', 40000, 60000, 'AC classroom,Transport,Books,Uniform,Playground,Activity room',
'Playgroup to Sr KG', 25, 'NTT', 'B.Ed', 'eurokids@myschooldesk.co.in', '9999999991',
'A trusted preschool focused on foundational learning and child safety.', 1, 1, 'approved', 1),
((SELECT id FROM users WHERE email='dps@myschooldesk.co.in' LIMIT 1), 'Delhi Public School', 'dps-vadodara',
'Kalali Road', 'Vadodara', 'Gujarat', 'CBSE', 80000, 150000, 'Transport,Library,Science lab,Sports,Computer lab',
'Nursery to Class 12', 80, 'B.Ed', 'M.Ed', 'dps@myschooldesk.co.in', '9999999992',
'A premier K-12 school with strong academics and extracurricular programs.', 1, 1, 'approved', 2);
