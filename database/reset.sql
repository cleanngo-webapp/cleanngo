PRAGMA foreign_keys = OFF;

-- Drop views first
DROP VIEW IF EXISTS inventory_stock_levels;
DROP VIEW IF EXISTS booking_counts_by_day;
DROP VIEW IF EXISTS employee_job_stats;

-- Drop tables (children before parents)
DROP TABLE IF EXISTS payroll_items;
DROP TABLE IF EXISTS payroll_periods;

DROP TABLE IF EXISTS inventory_transactions;
DROP TABLE IF EXISTS inventory_items;

DROP TABLE IF EXISTS employee_time_off;
DROP TABLE IF EXISTS employee_availability;

DROP TABLE IF EXISTS booking_ratings;
DROP TABLE IF EXISTS job_photos;
DROP TABLE IF EXISTS job_time_logs;
DROP TABLE IF EXISTS booking_staff_assignments;
DROP TABLE IF EXISTS booking_addons;
DROP TABLE IF EXISTS bookings;

DROP TABLE IF EXISTS service_addons;
DROP TABLE IF EXISTS services;

DROP TABLE IF EXISTS addresses;
DROP TABLE IF EXISTS employees;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS users;

-- Drop default Laravel scaffolding tables if present
DROP TABLE IF EXISTS failed_jobs;
DROP TABLE IF EXISTS migrations;
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS personal_access_tokens;

PRAGMA foreign_keys = ON;


