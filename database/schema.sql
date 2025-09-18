PRAGMA foreign_keys = ON;

-- USERS & ROLES
CREATE TABLE IF NOT EXISTS users (
  id               INTEGER PRIMARY KEY,
  name             TEXT NOT NULL,
  email            TEXT UNIQUE,
  phone            TEXT,
  role             TEXT NOT NULL CHECK (role IN ('admin','staff','cleaner','customer')),
  password_hash    TEXT,
  is_active        INTEGER NOT NULL DEFAULT 1 CHECK (is_active IN (0,1)),
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS customers (
  id                 INTEGER PRIMARY KEY,
  user_id            INTEGER NOT NULL UNIQUE REFERENCES users(id) ON DELETE CASCADE,
  default_address_id INTEGER REFERENCES addresses(id) ON DELETE SET NULL,
  notes              TEXT
);

CREATE TABLE IF NOT EXISTS employees (
  id                 INTEGER PRIMARY KEY,
  user_id            INTEGER NOT NULL UNIQUE REFERENCES users(id) ON DELETE CASCADE,
  hire_date          DATE,
  hourly_rate_cents  INTEGER NOT NULL DEFAULT 0 CHECK (hourly_rate_cents >= 0),
  is_cleaner         INTEGER NOT NULL DEFAULT 1 CHECK (is_cleaner IN (0,1)),
  is_active          INTEGER NOT NULL DEFAULT 1 CHECK (is_active IN (0,1)),
  notes              TEXT
);

-- ADDRESSES WITH GEO
CREATE TABLE IF NOT EXISTS addresses (
  id            INTEGER PRIMARY KEY,
  user_id       INTEGER REFERENCES users(id) ON DELETE SET NULL,
  label         TEXT,
  line1         TEXT NOT NULL,
  line2         TEXT,
  barangay      TEXT,
  city          TEXT,
  province      TEXT,
  postal_code   TEXT,
  latitude      REAL,
  longitude     REAL,
  map_place_id  TEXT,
  is_primary    INTEGER NOT NULL DEFAULT 0 CHECK (is_primary IN (0,1)),
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_addresses_user ON addresses(user_id, is_primary DESC);

PRAGMA foreign_keys = ON;

-- SERVICES & ADD-ONS
CREATE TABLE IF NOT EXISTS services (
  id                 INTEGER PRIMARY KEY,
  name               TEXT NOT NULL UNIQUE,
  description        TEXT,
  base_price_cents   INTEGER NOT NULL CHECK (base_price_cents >= 0),
  duration_minutes   INTEGER NOT NULL CHECK (duration_minutes > 0),
  is_active          INTEGER NOT NULL DEFAULT 1 CHECK (is_active IN (0,1)),
  created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- BOOKINGS, ASSIGNMENTS, PAYMENTS
CREATE TABLE IF NOT EXISTS bookings (
  id                   INTEGER PRIMARY KEY,
  code                 TEXT UNIQUE,
  customer_id          INTEGER NOT NULL REFERENCES customers(id) ON DELETE RESTRICT,
  address_id           INTEGER NOT NULL REFERENCES addresses(id) ON DELETE RESTRICT,
  service_id           INTEGER NOT NULL REFERENCES services(id) ON DELETE RESTRICT,
  scheduled_start      DATETIME NOT NULL,
  scheduled_end        DATETIME,
  status               TEXT NOT NULL DEFAULT 'pending'
                          CHECK (status IN ('pending','confirmed','in_progress','completed','cancelled','no_show')),
  notes                TEXT,
  base_price_cents     INTEGER NOT NULL CHECK (base_price_cents >= 0),
  discount_cents       INTEGER NOT NULL DEFAULT 0 CHECK (discount_cents >= 0),
  tax_cents            INTEGER NOT NULL DEFAULT 0 CHECK (tax_cents >= 0),
  total_due_cents      INTEGER NOT NULL CHECK (total_due_cents >= 0),
  payment_method       TEXT CHECK (payment_method IN ('cash','gcash')),
  payment_status       TEXT NOT NULL DEFAULT 'unpaid'
                          CHECK (payment_status IN ('unpaid','partial','paid','refunded')),
  amount_paid_cents    INTEGER NOT NULL DEFAULT 0 CHECK (amount_paid_cents >= 0),
  created_at           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  completed_at         DATETIME,
  cancelled_at         DATETIME,
  cancelled_reason     TEXT
);

CREATE INDEX IF NOT EXISTS idx_bookings_schedule ON bookings(status, scheduled_start);
CREATE INDEX IF NOT EXISTS idx_bookings_customer ON bookings(customer_id);
CREATE INDEX IF NOT EXISTS idx_bookings_service ON bookings(service_id);



CREATE TABLE IF NOT EXISTS booking_staff_assignments (
  booking_id   INTEGER NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
  employee_id  INTEGER NOT NULL REFERENCES employees(id) ON DELETE RESTRICT,
  role         TEXT NOT NULL CHECK (role IN ('team_lead','cleaner','assistant')),
  assigned_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  assigned_by  INTEGER REFERENCES users(id) ON DELETE SET NULL,
  PRIMARY KEY (booking_id, employee_id)
);

CREATE INDEX IF NOT EXISTS idx_assignments_employee ON booking_staff_assignments(employee_id);

-- GALLERY IMAGES
CREATE TABLE IF NOT EXISTS gallery_images (
  id            INTEGER PRIMARY KEY,
  service_type  TEXT NOT NULL,
  image_path    TEXT NOT NULL,
  original_name TEXT NOT NULL,
  alt_text      TEXT,
  sort_order    INTEGER NOT NULL DEFAULT 0,
  is_active     INTEGER NOT NULL DEFAULT 1 CHECK (is_active IN (0,1)),
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- SERVICE COMMENTS
CREATE TABLE IF NOT EXISTS service_comments (
  id            INTEGER PRIMARY KEY,
  service_type  TEXT NOT NULL,
  customer_id   INTEGER NOT NULL REFERENCES customers(id) ON DELETE CASCADE,
  comment       TEXT NOT NULL,
  rating        INTEGER CHECK (rating BETWEEN 1 AND 5),
  is_approved   INTEGER NOT NULL DEFAULT 1 CHECK (is_approved IN (0,1)),
  is_edited     INTEGER NOT NULL DEFAULT 0 CHECK (is_edited IN (0,1)),
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_service_comments_type ON service_comments(service_type);

-- PAYMENT PROOFS
CREATE TABLE IF NOT EXISTS payment_proofs (
  id            INTEGER PRIMARY KEY,
  booking_id    INTEGER NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
  employee_id   INTEGER REFERENCES employees(id) ON DELETE SET NULL,
  image_path    TEXT NOT NULL,
  amount        DECIMAL(10,2) NOT NULL,
  payment_method TEXT NOT NULL,
  status        TEXT NOT NULL DEFAULT 'pending' CHECK (status IN ('pending','approved','declined')),
  admin_notes   TEXT,
  reviewed_by   INTEGER REFERENCES users(id) ON DELETE SET NULL,
  reviewed_at   DATETIME,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- PAYMENT SETTINGS
CREATE TABLE IF NOT EXISTS payment_settings (
  id            INTEGER PRIMARY KEY,
  payment_method TEXT NOT NULL UNIQUE,
  is_active     INTEGER NOT NULL DEFAULT 1 CHECK (is_active IN (0,1)),
  instructions  TEXT,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- BOOKING ITEMS
CREATE TABLE IF NOT EXISTS booking_items (
  id                INTEGER PRIMARY KEY,
  booking_id        INTEGER NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
  service_id        INTEGER REFERENCES services(id) ON DELETE SET NULL,
  item_type         TEXT,
  quantity          INTEGER NOT NULL DEFAULT 1,
  area_sqm          DECIMAL(10,2),
  unit_price_cents  INTEGER NOT NULL DEFAULT 0,
  line_total_cents  INTEGER NOT NULL DEFAULT 0,
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- INVENTORY
CREATE TABLE IF NOT EXISTS inventory_items (
  id            INTEGER PRIMARY KEY,
  item_code     TEXT UNIQUE NOT NULL,
  name          TEXT NOT NULL,
  category      TEXT NOT NULL CHECK (category IN ('Tools', 'Machine', 'Cleaning Agent', 'Consumables')),
  quantity      REAL NOT NULL DEFAULT 0 CHECK (quantity >= 0),
  unit_price    REAL NOT NULL DEFAULT 0 CHECK (unit_price >= 0),
  reorder_level REAL NOT NULL DEFAULT 0 CHECK (reorder_level >= 0),
  status        TEXT NOT NULL DEFAULT 'In Stock' CHECK (status IN ('In Stock', 'Low Stock', 'Out of Stock')),
  is_active     INTEGER NOT NULL DEFAULT 1 CHECK (is_active IN (0,1)),
  notes         TEXT,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- REPORTING VIEWS
CREATE VIEW IF NOT EXISTS booking_counts_by_day AS
SELECT
  DATE(scheduled_start) AS day,
  status,
  COUNT(*) AS cnt
FROM bookings
GROUP BY DATE(scheduled_start), status;

CREATE VIEW IF NOT EXISTS employee_job_stats AS
SELECT
  e.id AS employee_id,
  u.name AS employee_name,
  COUNT(DISTINCT bsa.booking_id) AS jobs_worked,
  COUNT(DISTINCT CASE WHEN b.status = 'completed' THEN bsa.booking_id END) AS jobs_completed
FROM employees e
JOIN users u ON u.id = e.user_id
LEFT JOIN booking_staff_assignments bsa ON bsa.employee_id = e.id
LEFT JOIN bookings b ON b.id = bsa.booking_id
GROUP BY e.id, u.name;


