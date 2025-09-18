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

CREATE TABLE IF NOT EXISTS service_addons (
  id                 INTEGER PRIMARY KEY,
  service_id         INTEGER NOT NULL REFERENCES services(id) ON DELETE CASCADE,
  name               TEXT NOT NULL,
  description        TEXT,
  price_cents        INTEGER NOT NULL CHECK (price_cents >= 0),
  duration_minutes   INTEGER NOT NULL DEFAULT 0 CHECK (duration_minutes >= 0),
  is_active          INTEGER NOT NULL DEFAULT 1 CHECK (is_active IN (0,1)),
  created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(service_id, name)
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
  addon_total_cents    INTEGER NOT NULL DEFAULT 0 CHECK (addon_total_cents >= 0),
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

CREATE TABLE IF NOT EXISTS booking_addons (
  booking_id        INTEGER NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
  addon_id          INTEGER NOT NULL REFERENCES service_addons(id) ON DELETE RESTRICT,
  quantity          INTEGER NOT NULL DEFAULT 1 CHECK (quantity > 0),
  unit_price_cents  INTEGER NOT NULL CHECK (unit_price_cents >= 0),
  PRIMARY KEY (booking_id, addon_id)
);

CREATE TABLE IF NOT EXISTS booking_staff_assignments (
  booking_id   INTEGER NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
  employee_id  INTEGER NOT NULL REFERENCES employees(id) ON DELETE RESTRICT,
  role         TEXT NOT NULL CHECK (role IN ('team_lead','cleaner','assistant')),
  assigned_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  assigned_by  INTEGER REFERENCES users(id) ON DELETE SET NULL,
  PRIMARY KEY (booking_id, employee_id)
);

CREATE INDEX IF NOT EXISTS idx_assignments_employee ON booking_staff_assignments(employee_id);

-- JOB LOGGING (TIME + PHOTOS)
CREATE TABLE IF NOT EXISTS job_time_logs (
  id             INTEGER PRIMARY KEY,
  booking_id     INTEGER NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
  employee_id    INTEGER NOT NULL REFERENCES employees(id) ON DELETE CASCADE,
  clock_in       DATETIME NOT NULL,
  clock_out      DATETIME,
  minutes_worked INTEGER GENERATED ALWAYS AS (
    CASE
      WHEN clock_out IS NOT NULL
        THEN CAST((julianday(clock_out) - julianday(clock_in)) * 24 * 60 AS INTEGER)
      ELSE NULL
    END
  ) VIRTUAL,
  UNIQUE(booking_id, employee_id, clock_in)
);

CREATE INDEX IF NOT EXISTS idx_job_logs_employee ON job_time_logs(employee_id, clock_in);
CREATE INDEX IF NOT EXISTS idx_job_logs_booking ON job_time_logs(booking_id);

CREATE TABLE IF NOT EXISTS job_photos (
  id           INTEGER PRIMARY KEY,
  booking_id   INTEGER NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
  employee_id  INTEGER REFERENCES employees(id) ON DELETE SET NULL,
  file_path    TEXT NOT NULL,
  caption      TEXT,
  uploaded_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS booking_ratings (
  id           INTEGER PRIMARY KEY,
  booking_id   INTEGER NOT NULL UNIQUE REFERENCES bookings(id) ON DELETE CASCADE,
  customer_id  INTEGER NOT NULL REFERENCES customers(id) ON DELETE RESTRICT,
  rating       INTEGER NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comment      TEXT,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- STAFF AVAILABILITY
CREATE TABLE IF NOT EXISTS employee_availability (
  id           INTEGER PRIMARY KEY,
  employee_id  INTEGER NOT NULL REFERENCES employees(id) ON DELETE CASCADE,
  day_of_week  INTEGER NOT NULL CHECK (day_of_week BETWEEN 0 AND 6),
  start_time   TEXT NOT NULL,
  end_time     TEXT NOT NULL,
  UNIQUE(employee_id, day_of_week, start_time, end_time)
);

CREATE TABLE IF NOT EXISTS employee_time_off (
  id           INTEGER PRIMARY KEY,
  employee_id  INTEGER NOT NULL REFERENCES employees(id) ON DELETE CASCADE,
  start_date   DATE NOT NULL,
  end_date     DATE NOT NULL,
  reason       TEXT
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

-- inventory_transactions table removed - now using simplified inventory_items table with direct quantity tracking

-- Old inventory_stock_levels view removed - now using direct inventory_items table with automatic status calculation

-- PAYROLL (BASIC)
CREATE TABLE IF NOT EXISTS payroll_periods (
  id            INTEGER PRIMARY KEY,
  period_start  DATE NOT NULL,
  period_end    DATE NOT NULL,
  status        TEXT NOT NULL DEFAULT 'open' CHECK (status IN ('open','processing','closed')),
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  closed_at     DATETIME,
  UNIQUE(period_start, period_end)
);

CREATE TABLE IF NOT EXISTS payroll_items (
  id                 INTEGER PRIMARY KEY,
  payroll_period_id  INTEGER NOT NULL REFERENCES payroll_periods(id) ON DELETE CASCADE,
  employee_id        INTEGER NOT NULL REFERENCES employees(id) ON DELETE CASCADE,
  base_minutes       INTEGER NOT NULL DEFAULT 0 CHECK (base_minutes >= 0),
  base_pay_cents     INTEGER NOT NULL DEFAULT 0 CHECK (base_pay_cents >= 0),
  bonus_cents        INTEGER NOT NULL DEFAULT 0 CHECK (bonus_cents >= 0),
  deductions_cents   INTEGER NOT NULL DEFAULT 0 CHECK (deductions_cents >= 0),
  total_pay_cents    INTEGER NOT NULL DEFAULT 0 CHECK (total_pay_cents >= 0),
  generated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(payroll_period_id, employee_id)
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
  COUNT(DISTINCT jtl.booking_id) AS jobs_worked,
  COALESCE(SUM(jtl.minutes_worked), 0) AS minutes_worked
FROM employees e
JOIN users u ON u.id = e.user_id
LEFT JOIN job_time_logs jtl ON jtl.employee_id = e.id
GROUP BY e.id, u.name;


