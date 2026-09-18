<?php
declare(strict_types=1);

return [
    'name' => 'StockFlow Pro',
    'app_title' => 'StockFlow Pro - Inventory & Warehouse Management',
    'version' => '2.5.0',
    'timezone' => 'UTC',
    'currency' => '$',
    'currency_code' => 'USD',
    'debug' => true,
    'session_lifetime' => 7200,
    'pagination' => [
        'default_limit' => 15,
    ],
    'roles' => [
        'admin' => 'Super Administrator',
        'manager' => 'Warehouse / Inventory Manager',
        'dept_lead' => 'Department Lead',
        'staff' => 'Store & Department Staff',
    ],
    'movement_reasons' => [
        'IN' => ['Purchase Receipt', 'Customer Return', 'Initial Balance', 'Supplier Bonus'],
        'OUT' => ['Department Issuance', 'Customer Order / Sale', 'Scrap / Disposal', 'Donation'],
        'TRANSFER' => ['Warehouse to Storefront', 'Storefront to Warehouse', 'Inter-Office Transfer', 'Department Relocation'],
        'ADJUSTMENT' => ['Inventory Audit Cycle', 'Damaged Stock', 'Expired Goods', 'Theft / Loss Discrepancy', 'Physical Recount']
    ]
];
