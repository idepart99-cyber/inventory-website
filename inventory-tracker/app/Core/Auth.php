<?php
declare(strict_types=1);

namespace App\Core;

use App\Database\Database;
use PDO;

class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT u.*, d.name as department_name FROM users u LEFT JOIN departments d ON u.department_id = d.id WHERE u.email = ? AND u.status = 'active' LIMIT 1");
        $stmt->execute([trim($email)]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            Session::set('auth_user', $user);
            return true;
        }
        return false;
    }

    public static function check(): bool
    {
        return Session::has('auth_user');
    }

    public static function user(): ?array
    {
        return Session::get('auth_user');
    }

    public static function id(): ?int
    {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }

    public static function role(): ?string
    {
        $user = self::user();
        return $user['role'] ?? null;
    }

    public static function hasRole(array|string $roles): bool
    {
        $current = self::role();
        if (!$current) {
            return false;
        }
        if (is_string($roles)) {
            $roles = [$roles];
        }
        return in_array($current, $roles, true);
    }

    public static function logout(): void
    {
        Session::remove('auth_user');
    }
}
