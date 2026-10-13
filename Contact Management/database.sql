-- Database: Contact Management System

CREATE DATABASE IF NOT EXISTS contact_manager
CHARACTER SET utf8
COLLATE utf8_persian_ci;

USE contact_manager;

-- Table: users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: contacts
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    mobile VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    address TEXT,
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_contacts_users
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE,

    CONSTRAINT unique_mobile
        UNIQUE (user_id, mobile),

    CONSTRAINT unique_email
        UNIQUE (user_id, email)
);