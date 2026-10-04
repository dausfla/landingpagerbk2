<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    /**
     * @param string $path
     * @param array|callable $handler
     * @param array $middlewares
     */
    public function get(string $path, $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    /**
     * @param string $path
     * @param array|callable $handler
     * @param array $middlewares
     */
    public function post(string $path, $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array|callable $handler
     * @param array $middlewares
     */
    private function addRoute(string $method, string $path, $handler, array $middlewares): void
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method'      => $method,
            'path'        => $path,
            'pattern'     => $pattern,
            'handler'     => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public function dispatch(Request $request): void
    {
        $method = $request->getMethod();
        $uri = $request->getUri();

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                // Filter string named keys for dynamic route parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Run middleware stack
                foreach ($route['middlewares'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    $result = $middleware->handle($request);
                    if ($result === false) {
                        return;
                    }
                }

                $handler = $route['handler'];
                if (is_array($handler)) {
                    [$controllerClass, $methodName] = $handler;
                    $controller = new $controllerClass();
                    $controller->$methodName($request, ...array_values($params));
                } else if (is_callable($handler)) {
                    $handler($request, ...array_values($params));
                }
                return;
            }
        }

        // 404 Not Found fallback
        Response::html(View::render('public/404'), 404);
    }
}
