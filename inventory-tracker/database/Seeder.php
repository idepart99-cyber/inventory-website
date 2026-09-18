<?php
declare(strict_types=1);

namespace App\Database;

use PDO;

class Seeder
{
    public static function run(PDO $db): void
    {
        // Check if already seeded
        $stmt = $db->query("SELECT COUNT(*) FROM users");
        if ((int)$stmt->fetchColumn() > 0) {
            return;
        }

        // 1. Departments
        $departments = [
            ['Information Technology', 'DEP-IT', 'Hardware, workstations, cloud infrastructure & accessories', 'David Kim'],
            ['Retail & Sales Operations', 'DEP-SALES', 'Storefront merchandise, POS, and sales supplies', 'Sarah Connor'],
            ['Corporate Administration & HR', 'DEP-ADMIN', 'General office supplies, stationery, and consumables', 'Linda Martinez'],
            ['Logistics & Warehouse Operations', 'DEP-OPS', 'Shipping, packaging, and heavy warehouse gear', 'James Rodriguez'],
            ['Facilities & Maintenance', 'DEP-FAC', 'Cleaning supplies, facility safety equipment, and tools', 'Thomas White']
        ];
        $deptStmt = $db->prepare("INSERT INTO departments (name, code, description, manager_name) VALUES (?, ?, ?, ?)");
        $deptIds = [];
        foreach ($departments as $d) {
            $deptStmt->execute($d);
            $deptIds[$d[1]] = (int)$db->lastInsertId();
        }

        // 2. Locations
        $locations = [
            ['Central Main Warehouse', 'LOC-WH01', 'warehouse', '100 Logistics Boulevard, Sector 4', 25000],
            ['Downtown Retail Storefront', 'LOC-SH01', 'shop', '42 Market Street, Downtown Plaza', 5000],
            ['Corporate HQ Stockroom', 'LOC-OF01', 'office', '750 Tech Avenue, 4th Floor', 2000],
            ['West Distribution Hub', 'LOC-WH02', 'warehouse', '88 Harbor Way, Westside Terminal', 15000]
        ];
        $locStmt = $db->prepare("INSERT INTO locations (name, code, type, address, capacity) VALUES (?, ?, ?, ?, ?)");
        $locIds = [];
        foreach ($locations as $l) {
            $locStmt->execute($l);
            $locIds[$l[1]] = (int)$db->lastInsertId();
        }

        // 3. Categories
        $categories = [
            ['Computers & Workstations', 'CAT-COMP', 'Laptops, desktops, workstations, and thin clients'],
            ['Networking & Telecommunications', 'CAT-NET', 'Switches, routers, APs, and bulk ethernet cables'],
            ['Computer Peripherals', 'CAT-PERI', 'Monitors, mechanical keyboards, mice, and docking stations'],
            ['Office Supplies & Stationery', 'CAT-OFF', 'A4 papers, high-yield toner cartridges, and pens'],
            ['Office Furniture & Ergonomics', 'CAT-FURN', 'Standing desks, ergonomic task chairs, and monitor arms'],
            ['Tools & Safety Equipment', 'CAT-TOOL', 'Cordless drills, ladders, safety glasses, and gloves']
        ];
        $catStmt = $db->prepare("INSERT INTO categories (name, code, description) VALUES (?, ?, ?)");
        $catIds = [];
        foreach ($categories as $c) {
            $catStmt->execute($c);
            $catIds[$c[1]] = (int)$db->lastInsertId();
        }

        // 4. Suppliers
        $suppliers = [
            ['TechMaster Global Distro', 'SUP-TECH', 'Robert Chen', 'sales@techmaster.com', '+1-800-555-0199', '1000 Silicon Valley Dr, CA', 'US-9928172', 'Net 30'],
            ['CyberCore Networks Inc', 'SUP-NET', 'Jessica Vance', 'orders@cybercore.net', '+1-888-555-0144', '45 Network Parkway, TX', 'US-8827361', 'Net 15'],
            ['OfficePro Direct Supplies', 'SUP-OFF', 'Michael Scott', 'service@officepro.com', '+1-800-555-0177', '172 Paper Mill Rd, PA', 'US-7716253', 'Net 30'],
            ['Titan Industrial & Safety', 'SUP-IND', 'Frank Miller', 'wholesale@titanindustrial.com', '+1-877-555-0122', '500 Ironworks Way, OH', 'US-6625143', 'Due on Receipt'],
            ['Ergonomix Workspace Ltd', 'SUP-FURN', 'Claire Bennett', 'accounts@ergonomix.com', '+1-866-555-0188', '210 Comfort Blvd, MI', 'US-5544332', 'Net 45']
        ];
        $supStmt = $db->prepare("INSERT INTO suppliers (name, code, contact_person, email, phone, address, tax_id, payment_terms) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $supIds = [];
        foreach ($suppliers as $s) {
            $supStmt->execute($s);
            $supIds[$s[1]] = (int)$db->lastInsertId();
        }

        // 5. Users
        $defaultPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $mgrPassword = password_hash('manager123', PASSWORD_BCRYPT);
        $leadPassword = password_hash('lead123', PASSWORD_BCRYPT);
        $staffPassword = password_hash('staff123', PASSWORD_BCRYPT);

        $users = [
            ['Admin User', 'admin@stockflow.com', $defaultPassword, 'admin', null, 'active'],
            ['Marcus Vance (Warehouse Mgr)', 'warehouse@stockflow.com', $mgrPassword, 'manager', $deptIds['DEP-OPS'], 'active'],
            ['David Kim (IT Lead)', 'it.lead@stockflow.com', $leadPassword, 'dept_lead', $deptIds['DEP-IT'], 'active'],
            ['Sarah Connor (Sales Lead)', 'sales.lead@stockflow.com', $leadPassword, 'dept_lead', $deptIds['DEP-SALES'], 'active'],
            ['Alex Miller (Staff)', 'staff@stockflow.com', $staffPassword, 'staff', $deptIds['DEP-SALES'], 'active']
        ];
        $userStmt = $db->prepare("INSERT INTO users (name, email, password, role, department_id, status) VALUES (?, ?, ?, ?, ?, ?)");
        $userIds = [];
        foreach ($users as $u) {
            $userStmt->execute($u);
            $userIds[$u[1]] = (int)$db->lastInsertId();
        }

        // 6. Products
        $products = [
            [
                'TECH-LAP-001', '890100100012', 'Dell Latitude 5530 15.6" Laptop',
                'Core i7-1265U, 16GB RAM, 512GB NVMe SSD, Win 11 Pro enterprise laptop',
                $catIds['CAT-COMP'], $supIds['SUP-TECH'], 'pcs', 750.00, 999.00, 8, 60
            ],
            [
                'TECH-LAP-002', '890100100029', 'Apple MacBook Pro 14" M3',
                'Apple M3 chip, 16GB Unified Memory, 512GB SSD, Space Gray',
                $catIds['CAT-COMP'], $supIds['SUP-TECH'], 'pcs', 1400.00, 1799.00, 5, 40
            ],
            [
                'TECH-MON-001', '890100100036', 'Dell UltraSharp 27" 4K USB-C Hub Monitor',
                'IPS panel, 3840x2160, 90W Power Delivery USB-C, HDR400',
                $catIds['CAT-PERI'], $supIds['SUP-TECH'], 'pcs', 320.00, 449.00, 10, 80
            ],
            [
                'TECH-KEY-001', '890100100043', 'Logitech MX Mechanical Wireless Keyboard',
                'Tactile Quiet switches, backlit keys, Bluetooth & Logi Bolt',
                $catIds['CAT-PERI'], $supIds['SUP-TECH'], 'pcs', 90.00, 149.00, 15, 120
            ],
            [
                'TECH-MOU-001', '890100100050', 'Logitech MX Master 3S Ergonomic Mouse',
                '8000 DPI Darkfield sensor, Quiet clicks, MagSpeed wheel',
                $catIds['CAT-PERI'], $supIds['SUP-TECH'], 'pcs', 60.00, 99.00, 15, 150
            ],
            [
                'NET-RTR-001', '890100100067', 'Cisco Catalyst 1000 24-Port PoE+ Switch',
                '24x Gigabit PoE+ ports (195W budget), 4x 1G SFP uplinks',
                $catIds['CAT-NET'], $supIds['SUP-NET'], 'pcs', 480.00, 680.00, 4, 30
            ],
            [
                'NET-CAB-CAT6', '890100100074', 'Cat6 Pure Copper UTP Cable 1000ft Roll',
                'Solid bare copper, 550MHz, CMR rated jacket, Blue spool',
                $catIds['CAT-NET'], $supIds['SUP-NET'], 'roll', 85.00, 135.00, 6, 50
            ],
            [
                'NET-WAP-001', '890100100081', 'Ubiquiti UniFi 6 Pro WiFi 6 Access Point',
                'Dual-band WiFi 6, 5.3 Gbps aggregate throughput, PoE powered',
                $catIds['CAT-NET'], $supIds['SUP-NET'], 'pcs', 110.00, 179.00, 8, 60
            ],
            [
                'OFF-PPR-A4', '890100100098', 'Multipurpose Copy Paper A4 (5 Reams/Box)',
                '80 GSM, 98 bright white paper for laser and inkjet printers',
                $catIds['CAT-OFF'], $supIds['SUP-OFF'], 'box', 22.00, 38.00, 20, 200
            ],
            [
                'OFF-PEN-001', '890100100104', 'Pilot G2 Premium Gel Pens 0.7mm (12/pk)',
                'Smooth writing black gel ink with comfortable rubber grip',
                $catIds['CAT-OFF'], $supIds['SUP-OFF'], 'pack', 8.50, 16.99, 15, 150
            ],
            [
                'OFF-TNR-001', '890100100111', 'HP LaserJet High-Yield Black Toner W1480X',
                'Yields up to 9,500 pages, crisp sharp text for enterprise MFP',
                $catIds['CAT-OFF'], $supIds['SUP-OFF'], 'pcs', 95.00, 145.00, 6, 40
            ],
            [
                'FURN-CHR-001', '890100100128', 'Ergonomic Mesh High-Back Task Chair',
                'Adjustable lumbar support, 3D armrests, synchronous tilt mechanism',
                $catIds['CAT-FURN'], $supIds['SUP-FURN'], 'pcs', 180.00, 299.00, 5, 30
            ],
            [
                'FURN-DSK-001', '890100100135', 'Electric Dual-Motor Standing Desk 60x30"',
                'Memory keypad, anti-collision sensor, solid bamboo top',
                $catIds['CAT-FURN'], $supIds['SUP-FURN'], 'pcs', 280.00, 480.00, 4, 25
            ],
            [
                'TOOL-DRL-001', '890100100142', 'DeWalt 20V MAX Cordless Drill/Driver Kit',
                'Includes 2x 2.0Ah lithium batteries, high-speed charger, carrying case',
                $catIds['CAT-TOOL'], $supIds['SUP-IND'], 'kit', 115.00, 189.00, 4, 30
            ],
            [
                'TOOL-SFT-001', '890100100159', 'ANSI Z87.1 Safety Glasses (Box of 12)',
                'Anti-scratch, anti-fog clear polycarbonate safety spectacles',
                $catIds['CAT-TOOL'], $supIds['SUP-IND'], 'box', 18.00, 32.00, 8, 60
            ],
            [
                'TOOL-LAD-001', '890100100166', 'Werner 8ft Fiberglass Step Ladder (Type IA)',
                '300 lbs load capacity, non-conductive side rails for electrical work',
                $catIds['CAT-TOOL'], $supIds['SUP-IND'], 'pcs', 130.00, 210.00, 2, 15
            ]
        ];

        $prodStmt = $db->prepare("INSERT INTO products (sku, barcode, name, description, category_id, supplier_id, unit, cost_price, selling_price, min_stock_alert, max_stock_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $prodIds = [];
        foreach ($products as $p) {
            $prodStmt->execute($p);
            $prodIds[$p[0]] = (int)$db->lastInsertId();
        }

        // 7. Inventory Stock Distribution across locations
        // Let's create realistic stock levels:
        $inventoryData = [
            // SKU, Location, Qty, Bin
            ['TECH-LAP-001', 'LOC-WH01', 35, 'A-01-01'],
            ['TECH-LAP-001', 'LOC-SH01', 8, 'DISP-01'],
            ['TECH-LAP-001', 'LOC-OF01', 4, 'IT-CAB-A'],

            // MacBook is intentionally low in retail & office to demonstrate alert
            ['TECH-LAP-002', 'LOC-WH01', 12, 'A-01-02'],
            ['TECH-LAP-002', 'LOC-SH01', 3, 'DISP-02'],
            ['TECH-LAP-002', 'LOC-OF01', 1, 'IT-CAB-A'], // Total = 16 (min alert 5)

            ['TECH-MON-001', 'LOC-WH01', 40, 'B-02-01'],
            ['TECH-MON-001', 'LOC-SH01', 12, 'DISP-03'],
            ['TECH-MON-001', 'LOC-OF01', 6, 'IT-CAB-B'],

            ['TECH-KEY-001', 'LOC-WH01', 65, 'B-03-01'],
            ['TECH-KEY-001', 'LOC-SH01', 20, 'DISP-04'],
            ['TECH-KEY-001', 'LOC-OF01', 10, 'IT-CAB-C'],

            ['TECH-MOU-001', 'LOC-WH01', 75, 'B-03-02'],
            ['TECH-MOU-001', 'LOC-SH01', 25, 'DISP-05'],
            ['TECH-MOU-001', 'LOC-OF01', 12, 'IT-CAB-C'],

            ['NET-RTR-001', 'LOC-WH01', 14, 'C-01-01'],
            ['NET-RTR-001', 'LOC-OF01', 2, 'SRV-RM'],

            ['NET-CAB-CAT6', 'LOC-WH01', 28, 'C-02-01'],
            ['NET-CAB-CAT6', 'LOC-OF01', 4, 'SRV-RM'],

            ['NET-WAP-001', 'LOC-WH01', 32, 'C-01-02'],
            ['NET-WAP-001', 'LOC-SH01', 6, 'STORE-IT'],

            ['OFF-PPR-A4', 'LOC-WH01', 120, 'D-01-01'],
            ['OFF-PPR-A4', 'LOC-SH01', 25, 'STR-BCK'],
            ['OFF-PPR-A4', 'LOC-OF01', 18, 'COPY-RM'],

            ['OFF-PEN-001', 'LOC-WH01', 90, 'D-02-01'],
            ['OFF-PEN-001', 'LOC-SH01', 30, 'DISP-06'],
            ['OFF-PEN-001', 'LOC-OF01', 15, 'SUPP-CL'],

            // Toner cartridge intentionally low in total (only 4 total across company, below min alert 6)
            ['OFF-TNR-001', 'LOC-WH01', 2, 'D-02-02'],
            ['OFF-TNR-001', 'LOC-OF01', 2, 'COPY-RM'],

            ['FURN-CHR-001', 'LOC-WH01', 15, 'E-01-01'],
            ['FURN-CHR-001', 'LOC-SH01', 4, 'FLR-DSP'],

            ['FURN-DSK-001', 'LOC-WH01', 10, 'E-02-01'],
            ['FURN-DSK-001', 'LOC-SH01', 3, 'FLR-DSP'],

            ['TOOL-DRL-001', 'LOC-WH01', 16, 'F-01-01'],
            ['TOOL-DRL-001', 'LOC-OF01', 2, 'MAINT-RM'],

            ['TOOL-SFT-001', 'LOC-WH01', 35, 'F-02-01'],
            ['TOOL-SFT-001', 'LOC-OF01', 5, 'MAINT-RM'],

            // Ladder is low stock (only 2 total)
            ['TOOL-LAD-001', 'LOC-WH01', 2, 'F-03-01'],
        ];

        $invStmt = $db->prepare("INSERT INTO inventory (product_id, location_id, quantity, aisle_bin, last_counted_at) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
        foreach ($inventoryData as $inv) {
            $invStmt->execute([
                $prodIds[$inv[0]],
                $locIds[$inv[1]],
                $inv[2],
                $inv[3]
            ]);
        }

        // 8. Initial Stock Movement History (Audit Trail)
        $movements = [
            ['PO-2026-001', 'IN', 'TECH-LAP-001', null, 'LOC-WH01', null, 50, 750.00, 50, 'admin@stockflow.com', 'Purchase Receipt', 'Initial supplier shipment received from TechMaster Global'],
            ['TR-2026-001', 'TRANSFER', 'TECH-LAP-001', 'LOC-WH01', 'LOC-SH01', null, 10, 750.00, 40, 'warehouse@stockflow.com', 'Warehouse to Storefront', 'Stock transfer to Downtown Retail showcase'],
            ['TR-2026-002', 'TRANSFER', 'TECH-LAP-001', 'LOC-WH01', 'LOC-OF01', null, 5, 750.00, 35, 'warehouse@stockflow.com', 'Inter-Office Transfer', 'Allocated to Corporate HQ for onboardings'],
            ['PO-2026-002', 'IN', 'TECH-LAP-002', null, 'LOC-WH01', null, 20, 1400.00, 20, 'admin@stockflow.com', 'Purchase Receipt', 'Bulk purchase order received'],
            ['ISS-2026-001', 'OUT', 'TECH-LAP-002', 'LOC-OF01', null, 'DEP-IT', 2, 1400.00, 1, 'warehouse@stockflow.com', 'Department Issuance', 'Issued 2 MacBooks to senior software developers'],
            ['PO-2026-003', 'IN', 'OFF-PPR-A4', null, 'LOC-WH01', null, 150, 22.00, 150, 'admin@stockflow.com', 'Purchase Receipt', 'Q3 Bulk office paper delivery'],
            ['TR-2026-003', 'TRANSFER', 'OFF-PPR-A4', 'LOC-WH01', 'LOC-OF01', null, 20, 22.00, 130, 'warehouse@stockflow.com', 'Inter-Office Transfer', 'HQ copy room stock replenishment'],
            ['ADJ-2026-001', 'ADJUSTMENT', 'OFF-TNR-001', null, 'LOC-WH01', null, -1, 95.00, 2, 'warehouse@stockflow.com', 'Damaged Stock', 'Damaged in transit packaging leak discarded during audit']
        ];

        $movStmt = $db->prepare("INSERT INTO stock_movements (reference_no, type, product_id, source_location_id, destination_location_id, department_id, quantity, unit_cost, balance_after, user_id, reason, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now', '-2 days'))");
        foreach ($movements as $m) {
            $movStmt->execute([
                $m[0],
                $m[1],
                $prodIds[$m[2]],
                $m[3] ? $locIds[$m[3]] : null,
                $m[4] ? $locIds[$m[4]] : null,
                $m[5] ? $deptIds[$m[5]] : null,
                $m[6],
                $m[7],
                $m[8],
                $userIds[$m[9]],
                $m[10],
                $m[11]
            ]);
        }

        // 9. Multi-Department Requisitions & Items
        // Req 1: IT Department request (Approved)
        $db->exec(sprintf(
            "INSERT INTO requisitions (req_number, department_id, requested_by, approved_by, status, priority, needed_by_date, notes, created_at) 
             VALUES ('REQ-2026-001', %d, %d, %d, 'approved', 'high', date('now', '+3 days'), 'New engineering team workstation provisioning', datetime('now', '-1 days'))",
            $deptIds['DEP-IT'], $userIds['it.lead@stockflow.com'], $userIds['admin@stockflow.com']
        ));
        $req1Id = (int)$db->lastInsertId();
        $db->exec(sprintf("INSERT INTO requisition_items (requisition_id, product_id, requested_quantity, approved_quantity, status) VALUES (%d, %d, 5, 5, 'approved')", $req1Id, $prodIds['TECH-LAP-001']));
        $db->exec(sprintf("INSERT INTO requisition_items (requisition_id, product_id, requested_quantity, approved_quantity, status) VALUES (%d, %d, 5, 5, 'approved')", $req1Id, $prodIds['TECH-KEY-001']));

        // Req 2: Sales Department request (Pending)
        $db->exec(sprintf(
            "INSERT INTO requisitions (req_number, department_id, requested_by, approved_by, status, priority, needed_by_date, notes, created_at) 
             VALUES ('REQ-2026-002', %d, %d, NULL, 'pending', 'medium', date('now', '+5 days'), 'Weekly storefront operational replenishment', datetime('now', '-4 hours'))",
            $deptIds['DEP-SALES'], $userIds['sales.lead@stockflow.com']
        ));
        $req2Id = (int)$db->lastInsertId();
        $db->exec(sprintf("INSERT INTO requisition_items (requisition_id, product_id, requested_quantity, approved_quantity, status) VALUES (%d, %d, 10, 0, 'pending')", $req2Id, $prodIds['OFF-PPR-A4']));
        $db->exec(sprintf("INSERT INTO requisition_items (requisition_id, product_id, requested_quantity, approved_quantity, status) VALUES (%d, %d, 2, 0, 'pending')", $req2Id, $prodIds['OFF-TNR-001']));

        // Req 3: Facilities request (Fulfilled)
        $db->exec(sprintf(
            "INSERT INTO requisitions (req_number, department_id, requested_by, approved_by, status, priority, needed_by_date, notes, created_at) 
             VALUES ('REQ-2026-003', %d, %d, %d, 'fulfilled', 'urgent', date('now', '-1 days'), 'Building electrical safety refit', datetime('now', '-3 days'))",
            $deptIds['DEP-FAC'], $userIds['warehouse@stockflow.com'], $userIds['admin@stockflow.com']
        ));
        $req3Id = (int)$db->lastInsertId();
        $db->exec(sprintf("INSERT INTO requisition_items (requisition_id, product_id, requested_quantity, approved_quantity, fulfilled_quantity, status) VALUES (%d, %d, 2, 2, 2, 'fulfilled')", $req3Id, $prodIds['TOOL-SFT-001']));

        // 10. Audit log entries
        $db->exec(sprintf(
            "INSERT INTO audit_logs (user_id, action, entity, entity_id, details, ip_address) 
             VALUES (%d, 'SYSTEM_INIT', 'database', 1, 'Initial system setup and demo database seed loaded', '127.0.0.1')",
            $userIds['admin@stockflow.com']
        ));
    }
}
