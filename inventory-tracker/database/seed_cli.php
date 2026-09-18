<?php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Seeder.php';

use App\Database\Database;
use App\Database\Seeder;

echo "=== StockFlow Pro Database Initializer ===\n";
try {
    $db = Database::getConnection();
    echo "Connected to database engine: " . Database::getDriver() . "\n";

    // Recreate schema if requested
    $opts = getopt('', ['fresh']);
    if (isset($opts['fresh'])) {
        echo "Resetting database fresh...\n";
        $driver = Database::getDriver();
        if ($driver === 'sqlite') {
            $config = require __DIR__ . '/../config/database.php';
            $dbPath = $config['sqlite']['path'];
            // Re-run schema
            $sql = file_get_contents(__DIR__ . '/sqlite_schema.sql');
            $db->exec($sql);
        }
    }

    Seeder::run($db);
    echo "Seeding completed successfully!\n";
    echo "Default accounts:\n";
    echo "  - admin@stockflow.com     (Password: admin123)    - Super Administrator\n";
    echo "  - warehouse@stockflow.com (Password: manager123)  - Warehouse Manager\n";
    echo "  - it.lead@stockflow.com   (Password: lead123)     - Department Lead\n";
    echo "  - staff@stockflow.com     (Password: staff123)    - Staff / Cashier\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
