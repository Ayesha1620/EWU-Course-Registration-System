<?php

namespace App;

// ছোট্ট ও সহজ URL router (framework ছাড়া)
//
// ব্যবহার:
//   $router->get('/api/courses', [CourseController::class, 'index']);
//   $router->get('/api/courses/{id}', [CourseController::class, 'show'], ['student']);
//
// {id} এর জায়গায় URL এ যা আসবে তা handler method এ argument হিসাবে যাবে।
// ৩য় argument এ role list দিলে সেগুলো ছাড়া কেউ access করতে পারবে না।

class Router
{
    private $basePath;
    private $routes = [];

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function add(string $method, string $uri, array $handler, array $roles = []): void
    {
        $this->routes[] = compact('method', 'uri', 'handler', 'roles');
    }

    public function get(string $uri, array $handler, array $roles = []): void
    {
        $this->add('GET', $uri, $handler, $roles);
    }

    public function post(string $uri, array $handler, array $roles = []): void
    {
        $this->add('POST', $uri, $handler, $roles);
    }

    public function put(string $uri, array $handler, array $roles = []): void
    {
        $this->add('PUT', $uri, $handler, $roles);
    }

    public function delete(string $uri, array $handler, array $roles = []): void
    {
        $this->add('DELETE', $uri, $handler, $roles);
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // OPTIONS request — CORS preflight, সোজা 204 দিয়ে শেষ
        if ($method === 'OPTIONS') {
            http_response_code(204);
            exit;
        }

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = $this->stripBasePath($uri);
        $uri = '/' . trim($uri, '/');

        foreach ($this->routes as $route) {
            if (strcasecmp($route['method'], $method) !== 0) {
                continue;
            }

            $params = $this->match($route['uri'], $uri);
            if ($params === null) {
                continue;
            }

            // login ছাড়া access নিষেধ করতে পারবে
            Auth::authorize($route['roles']);

            [$controllerClass, $methodName] = $route['handler'];
            $controller = new $controllerClass(Database::connect());

            if (count($params) === 0) {
                $controller->$methodName();
            } else {
                $controller->$methodName(...array_values($params));
            }
            return;
        }

        json_error('Route not found', 404);
    }

    // base path (যেমন /ewu-course-backend) আর index.php prefix বাদ দেয়,
    // যাতে browser, php -S আর Apache সবার URL একই রকম match করে
    private function stripBasePath(string $uri): string
    {
        if ($this->basePath !== '' && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }

        if (strpos($uri, '/index.php') === 0) {
            $uri = substr($uri, strlen('/index.php'));
        }

        return $uri === '' ? '/' : $uri;
    }

    // URI pattern match করে, {param} গুলো value সহ return করে
    private function match(string $pattern, string $uri): ?array
    {
        $pattern = '/' . trim($pattern, '/');

        if (preg_match_all('#\{([a-zA-Z]+)\}#', $pattern, $names) === 0) {
            return $pattern === $uri ? [] : null;
        }

        $regex = preg_replace('#\{[a-zA-Z]+\}#', '([^/]+)', $pattern);
        if (!preg_match('#^' . $regex . '$#', $uri, $values)) {
            return null;
        }

        array_shift($values);
        return array_combine($names[1], $values);
    }
}