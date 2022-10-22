<?php

require_once __DIR__ . "/../vendor/autoload.php";

class Parser{
	
	private $routes = array();
	private $routesVariables = array();
	private $notFoundCallback;
    private $default;

	public function __construct(){}

	public function addDefault(string $r){

	    $this->default = $r;
    }

    public function addRoute(string $route, callable $callback, array $rendererVariables = array()){
        $this->routes[$route] = $callback;
        $this->routesVariables[$route] = $rendererVariables;
    }

    public function onNotFound(callable $callback){

	    $this->notFoundCallback = $callback;
    }

    public function parse(): void
    {
        $route = ($_GET['r'] ?? false);

        if ($route === false) {

            $location = "/mtesser/?r=" . $this->default . "&" . http_build_query($_GET);
            header("Location: $location");

        }else{

            $routeExists = array_key_exists($route, $this->routes);
            if (!$routeExists) {
                call_user_func($this->notFoundCallback);
            } else {
                $renderer = new Renderer();
                $data = $renderer->renderFile($route, $this->routesVariables[$route]);
                $data = call_user_func($this->routes[$route], $data);
                echo Renderer::clean($data);
            }
        }
    }

}