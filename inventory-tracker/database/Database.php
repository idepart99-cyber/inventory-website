<?php
declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static string $driver = 'sqlite';

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../config/database.php';
            self::$driver = $config['default'] ?? 'sqlite';

            try {
                if (self::$driver === 'sqlite') {
                    $dbPath = $config['sqlite']['path'];
                    $dbDir = dirname($dbPath);
                    if (!is_dir($dbDir)) {
                        mkdir($dbDir, 0777, true);
                    }
                    $isNew = !file_exists($dbPath);

                    self::$instance = new PDO('sqlite:' . $dbPath);
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    self::$instance->exec('PRAGMA foreign_keys = ON;');

                    if ($isNew || filesize($dbPath) === 0) {
                        self::initializeSqlite();
                    }
                } else {
                    $mysql = $config['mysql'];
                    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',
                        $mysql['host'], $mysql['port'], $mysql['database'], $mysql['charset']);
                    self::$instance = new PDO($dsn, $mysql['username'], $mysql['password'], [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                }
            } catch (PDOException $e) {
                die('Database Connection Error: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }

    public static function getDriver(): string
    {
        return self::$driver;
    }

    public static function initializeSqlite(): void
    {
        $sql = file_get_contents(__DIR__ . '/sqlite_schema.sql');
        self::$instance->exec($sql);
        require_once __DIR__ . '/Seeder.php';
        \App\Database\Seeder::run(self::$instance);
    }
}
