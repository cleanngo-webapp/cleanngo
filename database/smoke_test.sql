INSERT INTO users (name, email, role) VALUES ('Alice Admin','admin@cng.test','admin');
INSERT INTO users (name, email, role) VALUES ('Carl Cleaner','cleaner@cng.test','cleaner');
INSERT INTO users (name, email, role) VALUES ('Cathy Customer','customer@cng.test','customer');

INSERT INTO employees (user_id, is_cleaner, hourly_rate_cents) VALUES (2,1,20000);
INSERT INTO customers (user_id) VALUES (3);

INSERT INTO addresses (user_id, label, line1, city, province, latitude, longitude, is_primary)
VALUES (3,'Home','123 Sample St','Naga City','Camarines Sur',13.6218,123.1946,1);

INSERT INTO services (name, base_price_cents, duration_minutes) VALUES ('Standard Clean',100000,120);

INSERT INTO bookings (customer_id, address_id, service_id, scheduled_start, status, base_price_cents, total_due_cents, payment_status)
VALUES (1, 1, 1, DATETIME('now','+1 day'), 'confirmed', 100000, 100000, 'unpaid');

INSERT INTO booking_staff_assignments (booking_id, employee_id, role) VALUES (1,1,'cleaner');

INSERT INTO job_time_logs (booking_id, employee_id, clock_in, clock_out)
VALUES (1,1,DATETIME('now'), DATETIME('now','+2 hours'));

INSERT INTO inventory_items (sku, name, unit) VALUES ('SOAP-1L','All-purpose Soap 1L','bottle');
INSERT INTO inventory_transactions (item_id, type, quantity) VALUES (1,'purchase',10);
INSERT INTO inventory_transactions (item_id, booking_id, employee_id, type, quantity) VALUES (1,1,1,'usage',2);


SELECT * FROM users;

-- Show quick results
SELECT * FROM inventory_stock_levels;
SELECT * FROM employee_job_stats;
SELECT * FROM booking_counts_by_day;


