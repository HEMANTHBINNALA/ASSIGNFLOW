-- Migration: Add email and date_of_birth columns to users table
-- Run this SQL script in phpMyAdmin or MySQL command line to update your existing database

ALTER TABLE users ADD COLUMN email VARCHAR(100) AFTER full_name;
ALTER TABLE users ADD COLUMN date_of_birth DATE AFTER email;

-- Verify the changes
DESCRIBE users;
