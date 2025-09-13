-- Add customer profile for user ID 24
INSERT INTO customers (user_id, default_address_id, notes, created_at, updated_at) 
VALUES (24, NULL, 'Auto-created customer profile', NOW(), NOW())
ON CONFLICT (user_id) DO NOTHING;

-- Check if it was added
SELECT c.id as customer_id, c.user_id, u.first_name, u.last_name 
FROM customers c 
JOIN users u ON c.user_id = u.id 
WHERE c.user_id = 24;
