<?php
namespace Core;

class Router {
    private array $routes = [];
    public function get(string $uri, string $controller): void {
        $this->add('GET', $uri, $controller);
    }
    public function post(string $uri, string $controller): void {
        $this->add('POST', $uri, $controller);
    }
    public function delete(string $uri, string $controller): void {
        $this->add('DELETE', $uri, $controller);
    }
    public function patch(string $uri, string $controller): void {
        $this->add('PATCH', $uri, $controller);
    }
    public function put(string $uri, string $controller): void {
        $this->add('PUT', $uri, $controller);
    }

    private function add(string $method, string $uri, string $controller): void {
        $this->routes[$method][$uri] = $controller;
    }

    public function dispatch(string $uri, string $method, array $params = []): void {
        $controller = $this->routes[$method][$uri] ?? null;

        if (!$controller) {
            http_response_code(404);
            require base_path("views/404.view.php");
            return;
        }

        /// Makes $db (and future other) variables a local variable in this scope
        /// I find this cleaner than defining a bunch of global variables at the index page.
        extract($params);
        require base_path($controller);
    }
}