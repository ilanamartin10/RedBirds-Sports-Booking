CREATE DATABASE redbird_bookings;

USE redbird_bookings;

-- Login-related data
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Profile-related data
CREATE TABLE profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    favorite_sports VARCHAR(255),
    major VARCHAR(100),
    minor VARCHAR(100),
    about TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE VIEW profiles_with_name AS
SELECT 
    profiles.id AS profile_id,
    profiles.user_id,
    users.first_name,
    users.last_name,
    profiles.favorite_sports,
    profiles.major,
    profiles.minor,
    profiles.about
FROM 
    profiles
INNER JOIN 
    users ON profiles.user_id = users.id;

-- Bookings
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    booking_time DATETIME NOT NULL,
    status ENUM('pending', 'confirmed', 'canceled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Posts
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- User sessions
CREATE TABLE user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(64) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
    
-- Court Booking
CREATE TABLE court_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    court_name VARCHAR(50) NOT NULL,
    booking_start DATETIME NOT NULL,
    duration INT NOT NULL,
    booking_time DATETIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') NOT NULL DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Messaging 
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

-- Gym Memberships 
CREATE TABLE memberships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    membership_type VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    purchase_date DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);


CREATE TABLE events (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    max_participants INT(11) DEFAULT NULL,
    visibility ENUM('public', 'private') NOT NULL DEFAULT 'public',
    private_emails TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE event_options (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    event_id INT(11) NOT NULL,
    option_datetime DATETIME NOT NULL
);
CREATE TABLE event_votes (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    event_id INT(11) NOT NULL,
    option_id INT(11) NOT NULL,
    vote_count INT(11) NOT NULL DEFAULT 0
);
