# StockFlow Pro - Production Inventory & Stock Management System

A full-stack, enterprise-ready **Inventory Tracker & Stock Management System** built with **PHP 8** and **MySQL / SQLite** (dual-driver PDO database layer).

Designed specifically for organizations operating across **retail storefronts, central warehouses, office stockrooms, and multiple internal departments**.

---

## Key Features & Capabilities

### 1. Multi-Location Inventory Tracking
- **Warehouses, Shops & Offices**: Manage independent stock levels for Central Main Warehouses, Downtown Retail Stores, Corporate Office Supply Rooms, and Distribution Hubs.
- **Real-Time Distribution Breakdown**: View per-location on-hand balances, aisle/bin shelf tracking, and facility capacity utilization indicators.
- **Inter-Location Stock Transfers**: Full chain-of-custody transfer workflow from source to destination location.

### 2. Multi-Department Supply Requisitions
- **Department Requests**: Employees and Department Leads (IT, Sales, Admin, Operations, Facilities) can submit multi-line requisitions.
- **Review & Approval Workflow**: Managers can review, approve, or reject requisition requests.
- **Direct Warehouse Fulfillment**: Approved requests can be fulfilled directly from warehouse stock with automatic deduction and stock-out ledger logging.

### 3. Product Catalog & Smart Barcode Management
- **Catalog Attributes**: SKU, Barcode (EAN-13, UPC, Code-128), Category, Supplier, Cost Price, Selling Price, Alert Thresholds, and Units of Measure.
- **In-Browser Camera Scanner**: Scan barcodes and QR codes live using device camera or handheld USB barcode guns.
- **Printable Barcode Sheet**: Generate and print standardized barcode label sheets (with Code-128 barcodes) for physical items and shelves.
- **Instant Search**: Debounced live global search dropdown by SKU, Barcode, or Product Name.

### 4. Complete Stock Movement Audit Trail
- **Stock In (Receiving)**: Inbound receipts from supplier purchase orders or returns, with reference numbers and unit cost logging.
- **Stock Out (Issuing / Dispatch)**: Issuances to departments or retail customer orders with quantity availability safeguards.
- **Location Transfers**: Move items between facilities with origin deduction and destination credit in a single database transaction.
- **Cycle Count Audits / Adjustments**: Reconcile physical inventory counts against system records for damaged, expired, lost, or recount discrepancies.
- **Filterable Transaction Ledger**: Search movements by movement type, date range, SKU, location, or department with 1-click **CSV export**.

### 5. Financial Valuation & Replenishment Analytics
- **Executive Dashboard**: Real-time KPI cards for Total Asset Valuation (At Cost), Gross Retail Realization, Active Items, Low Stock count, and Pending Requisitions.
- **Interactive Visualizations**: 14-day Inflow vs Outflow stock volume comparison bar chart and Category Valuation Doughnut chart powered by Chart.js.
- **Automated Replenishment Report**: Generates suggested reorder quantities and estimated procurement costs based on safety thresholds.

### 6. Role-Based Access Control (RBAC) & Security
- **Roles**:
  - **Super Administrator**: Full system access, users, locations, departments, and financial audits.
  - **Warehouse Manager**: Full stock movement, inbound receipts, dispatches, transfers, and cycle audits.
  - **Department Lead**: Submit and approve department requisitions and view departmental inventory.
  - **Staff / Cashier**: View stock, scan barcodes, and log basic stock issues.
- **Security Primitives**:
  - 100% prepared PDO SQL statements (SQL injection immune).
  - CSRF token validation on all POST actions.
  - BCRYPT password hashing.
  - Secure session handling.
  - Contextual XSS escaping.

---

## Quick Start (Zero Setup Required)

The application includes an embedded SQLite database driver with automatic table creation and realistic demo data out of the box.

### 1. Launch the Web Server
Open terminal in the project directory and run:

```powershell
cd "C:\Users\User\.gemini\antigravity\scratch\inventory-tracker"
php -S 127.0.0.1:8000 -t public
```

### 2. Access in Browser
Navigate to:
[http://127.0.0.1:8000](http://127.0.0.1:8000)

### 3. Demo Credentials
The login screen features **1-click login buttons** to instantly autofill any of the following accounts:

| Role | Email | Password | Access Level |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@stockflow.com` | `admin123` | Full system control & settings |
| **Warehouse Manager** | `warehouse@stockflow.com` | `manager123` | Inventory, Stock In/Out, Transfers, Audits |
| **IT Dept Lead** | `it.lead@stockflow.com` | `lead123` | IT Requisitions, approvals & tracking |
| **Store Staff** | `staff@stockflow.com` | `staff123` | Barcode scanning & stock lookup |

---

## Switching to Production MySQL / MariaDB

To use MySQL instead of SQLite:

1. Import `database/mysql_schema.sql` into your MySQL database:
   ```bash
   mysql -u root -p inventory_tracker < database/mysql_schema.sql
   ```
2. Set environment variables or configure `config/database.php`:
   ```php
   'default' => 'mysql',
   'mysql' => [
       'host' => '127.0.0.1',
       'port' => '3306',
       'database' => 'inventory_tracker',
       'username' => 'your_db_user',
       'password' => 'your_db_password',
       'charset' => 'utf8mb4'
   ]
   ```
3. Run the CLI seeder:
   ```bash
   php database/seed_cli.php
   ```

---

## Project Structure

```
inventory-tracker/
├── app/
│   ├── Controllers/         # MVC Controllers (Auth, Products, Stock, Requisitions, etc.)
│   ├── Core/                # Core primitives (Router, Auth, Csrf, Session, View, Controller)
│   ├── Models/              # Domain Models (Product, StockMovement, Requisition, etc.)
│   └── Views/               # UI Views (Dashboard, Products, Stock, Reports, Requisitions)
│       ├── layouts/         # Base HTML layouts (main, auth, print, 404)
│       ├── dashboard/
│       ├── products/
│       ├── stock/
│       ├── requisitions/
│       ├── locations/
│       ├── departments/
│       ├── suppliers/
│       └── reports/
├── config/
│   ├── app.php              # Application settings, roles, currency
│   └── database.php         # SQLite & MySQL configuration
├── database/
│   ├── Database.php         # PDO connection manager & auto-migrator
│   ├── sqlite_schema.sql    # SQLite DDL schema
│   ├── mysql_schema.sql     # MySQL DDL schema
│   ├── Seeder.php           # Realistic demo dataset seeder
│   └── seed_cli.php         # CLI seeding tool
├── public/                  # Web server public root
│   ├── index.php            # Front controller & routing dispatcher
│   ├── css/app.css          # Styling & fonts
│   ├── js/app.js            # Live search & responsive behaviors
│   └── js/scanner.js        # Barcode & QR scanner logic
└── README.md
```
