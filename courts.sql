CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE courts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    court_name VARCHAR(50) NOT NULL,
    location VARCHAR(100),
    capacity INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE court_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    court_id INT NOT NULL,
    booking_date DATE NOT NULL,
    time_slot TIME NOT NULL,
    duration INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (court_id) REFERENCES courts(id) ON DELETE CASCADE
);

SELECT time_slot FROM court_bookings
WHERE court_id = ? AND booking_date = ?;

ALTER TABLE users 
ADD COLUMN favorite_sports VARCHAR(255),
ADD COLUMN major VARCHAR(100),
ADD COLUMN minor VARCHAR(100),
ADD COLUMN about_me TEXT,
ADD COLUMN facebook_url VARCHAR(255),
ADD COLUMN twitter_url VARCHAR(255),
ADD COLUMN instagram_url VARCHAR(255),
ADD COLUMN linkedin_url VARCHAR(255);