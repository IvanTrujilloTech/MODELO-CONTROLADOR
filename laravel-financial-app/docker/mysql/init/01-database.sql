-- Create database if not exists
CREATE DATABASE IF NOT EXISTS finanzas_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user if not exists
CREATE USER IF NOT EXISTS 'laravel'@'%' IDENTIFIED BY 'password';

-- Grant privileges
GRANT ALL PRIVILEGES ON finanzas_db.* TO 'laravel'@'%';

-- Flush privileges
FLUSH PRIVILEGES;
