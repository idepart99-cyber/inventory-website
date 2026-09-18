<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler, array $middleware = []): self
    {
        return $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): self
    {
        return $this->add('POST', $path, $handler, $middleware);
    }

    private function add(string $method, string $path, array $handler, array $middleware = []): self
    {
        $this->routes[] = [
            'method' => $method,
            'path' => rtrim($path, '/') ?: '/',
            'handler' => $handler,
            'middleware' => $middleware,
        ];
        return $this;
    }

    public function dispatch(string $uri, string $requestMethod): void
    {
        $parsedUrl = parse_url($uri, PHP_URL_PATH);
        $path = rtrim($parsedUrl, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            // Match parameters e.g. /products/{id}
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Check middleware
                foreach ($route['middleware'] as $mw) {
                    if ($mw === 'auth' && !Auth::check()) {
                        Session::flash('warning', 'Please sign in to continue.');
                        header('Location: /login');
                        exit;
                    }
                    if ($mw === 'csrf' && $requestMethod === 'POST') {
                        $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
                        if (!Csrf::validate($token)) {
                            http_response_code(419);
                            die('CSRF validation failed.');
                        }
                    }
                }

                [$controllerClass, $method] = $route['handler'];
                if (!class_exists($controllerClass)) {
                    throw new \RuntimeException("Controller class {$controllerClass} not found");
                }
                $controller = new $controllerClass();
                if (!method_exists($controller, $method)) {
                    throw new \RuntimeException("Method {$method} not found in {$controllerClass}");
                }

                call_user_func_array([$controller, $method], array_values($params));
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        View::render('layouts/404', ['title' => 'Page Not Found'], 'layouts/main');
    }
}
