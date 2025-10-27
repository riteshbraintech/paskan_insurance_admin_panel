-- ============================================================
-- Paskan Insurance Project - Database Schema (PostgreSQL)

---

-- This SQL schema defines the complete data structure for the
-- Paskan Insurance Platform. It manages user accounts, insurers,
-- products, quotes, policies, CMS content, commissions, and more.
-- Added `deleted_at` column in key tables to support soft deletes.
-- ============================================================

-- ============================================================
-- USERS TABLE
-- Stores all customers using the platform for comparison/purchase.
-- ============================================================
CREATE TABLE users (
id SERIAL PRIMARY KEY,
full_name VARCHAR(150) NOT NULL,
email VARCHAR(150) UNIQUE NOT NULL,
phone VARCHAR(20) UNIQUE,
password_hash TEXT NOT NULL,
dob DATE,
gender VARCHAR(10),
nationality VARCHAR(50),
marital_status VARCHAR(20),
address TEXT,
national_id VARCHAR(50),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL
);

-- ============================================================
-- ADMINS TABLE
-- Stores admin panel users managing backend features.
-- ============================================================
CREATE TABLE admins (
id SERIAL PRIMARY KEY,
name VARCHAR(150) NOT NULL,
email VARCHAR(150) UNIQUE NOT NULL,
password_hash TEXT NOT NULL,
role VARCHAR(50) DEFAULT 'admin',
last_login TIMESTAMP,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL
);

-- ============================================================
-- INSURANCE_CATEGORIES TABLE
-- Defines major insurance categories (motor, health, etc.).
-- ============================================================
CREATE TABLE insurance_categories (
id SERIAL PRIMARY KEY,
name VARCHAR(100) NOT NULL,
slug VARCHAR(100) UNIQUE,
description TEXT,
is_active BOOLEAN DEFAULT TRUE,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL
);

-- ============================================================
-- INSURERS TABLE
-- Stores insurance companies and their API credentials.
-- ============================================================
CREATE TABLE insurers (
id SERIAL PRIMARY KEY,
name VARCHAR(150) NOT NULL,
logo_url TEXT,
api_base_url TEXT,
api_key TEXT,
category_id INT REFERENCES insurance_categories(id) ON DELETE CASCADE,
is_active BOOLEAN DEFAULT TRUE,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL
);

-- ============================================================
-- INSURANCE_PRODUCTS TABLE
-- Insurer product details for displaying comparison results.
-- ============================================================
CREATE TABLE insurance_products (
id SERIAL PRIMARY KEY,
insurer_id INT REFERENCES insurers(id) ON DELETE CASCADE,
category_id INT REFERENCES insurance_categories(id) ON DELETE CASCADE,
name VARCHAR(150) NOT NULL,
description TEXT,
base_premium NUMERIC(12,2),
coverage_details TEXT,
benefits TEXT,
add_ons TEXT,
is_active BOOLEAN DEFAULT TRUE,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL
);

-- ============================================================
-- QUOTES TABLE
-- Stores quote requests and insurer API responses.
-- ============================================================
CREATE TABLE quotes (
id SERIAL PRIMARY KEY,
user_id INT REFERENCES users(id) ON DELETE CASCADE,
category_id INT REFERENCES insurance_categories(id),
request_payload JSONB,
response_payload JSONB,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL
);

-- ============================================================
-- POLICIES TABLE
-- Stores confirmed insurance policies linked to users.
-- ============================================================
CREATE TABLE policies (
id SERIAL PRIMARY KEY,
user_id INT REFERENCES users(id) ON DELETE CASCADE,
insurer_id INT REFERENCES insurers(id),
product_id INT REFERENCES insurance_products(id),
policy_number VARCHAR(100) UNIQUE,
premium_amount NUMERIC(12,2),
coverage_amount NUMERIC(12,2),
policy_start DATE,
policy_end DATE,
status VARCHAR(50) DEFAULT 'active',
policy_pdf_url TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL
);

-- ============================================================
-- PAYMENTS TABLE
-- Stores payment gateway and transaction references.
-- ============================================================
CREATE TABLE payments (
id SERIAL PRIMARY KEY,
policy_id INT REFERENCES policies(id) ON DELETE CASCADE,
payment_gateway VARCHAR(100),
transaction_ref VARCHAR(150),
amount NUMERIC(12,2),
status VARCHAR(50),
payment_date TIMESTAMP,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL
);

-- ============================================================
-- COMMISSIONS TABLE
-- Tracks commissions expected, received, and pending.
-- ============================================================
CREATE TABLE commissions (
id SERIAL PRIMARY KEY,
policy_id INT REFERENCES policies(id) ON DELETE CASCADE,
expected NUMERIC(12,2),
received NUMERIC(12,2),
pending NUMERIC(12,2),
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL
);

-- ============================================================
-- CMS_PAGES TABLE
-- CMS for static informational pages like About, Terms, etc.
-- ============================================================
CREATE TABLE cms_pages (
id SERIAL PRIMARY KEY,
slug VARCHAR(150) UNIQUE,
title VARCHAR(200) NOT NULL,
content TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL
);

-- ============================================================
-- BANNERS TABLE
-- Stores homepage banners and marketing visuals.
-- ============================================================
CREATE TABLE banners (
id SERIAL PRIMARY KEY,
title VARCHAR(200),
image_url TEXT,
redirect_url TEXT,
is_active BOOLEAN DEFAULT TRUE,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL
);

-- ============================================================
-- DISCOUNTS TABLE
-- Stores promotional discounts by category or insurer.
-- ============================================================
CREATE TABLE discounts (
id SERIAL PRIMARY KEY,
category_id INT REFERENCES insurance_categories(id),
insurer_id INT REFERENCES insurers(id),
discount_percent NUMERIC(5,2),
valid_from DATE,
valid_to DATE,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL
);

-- ============================================================
-- LOGS TABLE
-- Stores admin actions for audit tracking.
-- ============================================================
CREATE TABLE logs (
id SERIAL PRIMARY KEY,
admin_id INT REFERENCES admins(id) ON DELETE SET NULL,
action TEXT,
ip_address VARCHAR(50),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL
);

-- ============================================================
-- REMINDERS TABLE
-- Policy renewal reminders for user notifications.
-- ============================================================
CREATE TABLE reminders (
id SERIAL PRIMARY KEY,
policy_id INT REFERENCES policies(id) ON DELETE CASCADE,
reminder_date DATE,
sent BOOLEAN DEFAULT FALSE,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL
);

-- ============================================================
-- STRUCTURE SUMMARY

---

-- Added `deleted_at` to all major business tables to allow soft
-- deletes and data recovery without physical removal.
-- ============================================================
