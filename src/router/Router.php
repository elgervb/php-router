<?php
namespace router;

/**
 *
 * @author eaboxt
 */
class Router
{

    /**
     *
     * @var \ArrayObject of Routes
     */
    private $routes;

    public function __construct()
    {
        $this->routes = new \ArrayObject();
    }

    /**
     * Add a new route
     *
     * @param string $path
     *            the path to the route
     * @param \Closure $aController
     *            the controller to execute when a route matches
     * @param string $requestType
     *            the HTTP request method
     *            
     * @return \compact\routing\Router for chaining purposes
     *        
     * @throws RouterException when route with same name already exists
     */
    public function route($name, $path,\Closure $controller, $requestMethod = 'GET')
    {
        if ($this->routes->offsetExists($name)) {
            throw new RouterException("Route with name $name already exists");
        }
        $route = new Route($name, $path, $controller, strtoupper($requestMethod));
        $this->routes->offsetSet($name, $route);
        
        return $this;
    }

    /**
     * Executes a controller registered to the path with the route params as arguments
     *
     * @param string $aPath
     *            the request path to check for eg. /route/:arg1/:arg2
     * @param string $requestMethod
     *            the HTTP request method
     *            
     * @return mixed The result from the controller or <code>false</code> when route could not be found
     */
    public function match($path, $requestMethod = 'GET')
    {
        $requestMethod = strtoupper($requestMethod);
        
        /* @var $route Route */
        foreach ($this->routes as $route) {
            if ($requestMethod === $route->getRequestMethod()) {
                if (preg_match("#{$route->getRegex()}#", $path, $matches)) {
                    array_shift($matches);
                    return call_user_func_array($route->getController(), $matches);
                }
            }
        }
        return false;
    }
}
