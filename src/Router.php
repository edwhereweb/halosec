<?php

declare(strict_types=1);

namespace HaloSec;

final class Router
{
    /** @var list<array{string, string, callable}> */
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler): void
    {
        $this->routes[] = [$method, $pattern, $handler];
    }

    /**
     * Returns the handler result, or null when no route matches.
     */
    public function dispatch(string $method, string $path): mixed
    {
        $path = '/' . trim($path, '/');
        $pathMatched = false;

        foreach ($this->routes as [$routeMethod, $pattern, $handler]) {
            $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[a-z0-9\-]+)', $pattern) . '$#';
            if (preg_match($regex, $path, $matches) !== 1) {
                continue;
            }
            $pathMatched = true;
            if ($routeMethod !== $method) {
                continue;
            }
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            return $handler(...array_values($params));
        }

        return $pathMatched ? 405 : null;
    }
}
