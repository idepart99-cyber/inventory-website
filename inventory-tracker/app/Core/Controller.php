<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $viewPath, array $data = [], string $layout = 'layouts/main'): void
    {
        View::render($viewPath, $data, $layout);
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        exit;
    }

    protected function redirect(string $url, ?string $message = null, string $type = 'success'): void
    {
        if ($message !== null) {
            Session::flash($type, $message);
        }
        header('Location: ' . $url);
        exit;
    }

    protected function request(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $_REQUEST;
        }
        return $_REQUEST[$key] ?? $default;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function validateCsrf(): void
    {
        $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!Csrf::validate($token)) {
            http_response_code(419);
            die('Page Expired (CSRF token verification failed). Please go back, refresh the page, and try again.');
        }
    }

    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login', 'Please sign in to access this page.', 'warning');
        }
    }

    protected function requireRole(array|string $roles): void
    {
        $this->requireAuth();
        if (!Auth::hasRole($roles)) {
            http_response_code(403);
            die('Access Denied: Your account role does not have permission to perform this action.');
        }
    }
}
