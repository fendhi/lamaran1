-- database.sql
-- Jalankan di phpMyAdmin / MySQL client

CREATE DATABASE IF NOT EXISTS lamaran_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE lamaran_db;

CREATE TABLE IF NOT EXISTS applicants (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  position_applied VARCHAR(150) NOT NULL,
  address VARCHAR(255) NULL,
  cover_letter TEXT NULL,
  cv_original_name VARCHAR(255) NOT NULL,
  cv_stored_name VARCHAR(255) NOT NULL,
  cv_mime VARCHAR(100) NOT NULL,
  cv_size INT UNSIGNED NOT NULL,
  mail_sent TINYINT(1) NOT NULL DEFAULT 0,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_applicants_email (email),
  UNIQUE KEY uq_applicants_phone (phone),
  KEY idx_created_at (created_at)
) ENGINE=InnoDB;
