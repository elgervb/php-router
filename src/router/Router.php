<?php
namespace router;

/**
 *
 * @author eaboxt
 *        
 */
class Router
{

    /**
     *
     * @var \ArrayObject
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
     *            the path regex (slashes will be automatically escaped)
     * @param \Closure $aController
     *            the controller to execute when a route matches
     * @param string $requestType
     *            the HTTP request method
     *            
     * @return \compact\routing\Router for chaining purposes
     */
    public function route($path,\Closure $controller, $requestMethod = 'GET')
    {
        $requestMethod = strtoupper($requestMethod);
        $routes = $this->getRoutes(strtoupper($requestMethod));
        $routes->offsetSet($path, $controller);
        return $this;
    }

    /**
     * Returns all routes for a request method, eg.
     * GET, POST, etc
     *
     * @param string $for
     *            the HTTP request method
     *            
     * @return mixed
     */
    private function getRoutes($for)
    {
        if (! $this->routes->offsetExists($for)) {
            $this->routes->offsetSet($for, new \ArrayObject());
        }
        
        return $this->routes->offsetGet($for);
    }

    /**
     * Executes a controller registered to the path with the route params as arguments
     *
     * @param string $aPath
     *            the request path to check for eg. /route/:arg1/:arg2
     * @param string $requestMethod
     *            the HTTP request method
     *            
     * @return mixed The result from the controller or <code>null</code>
     */
    public function match($route, $requestMethod)
    {
        $requestMethod = strtoupper($requestMethod); 
        $routes = $this->getRoutes($requestMethod);
        
        // check regex
        foreach ($routes as $path => $controller) {
            $path = preg_replace("/\//", "\\\/", $path);
            if (preg_match('/' . $path . '/', $route, $matches)) {
                
                array_shift($matches);
                return call_user_func_array($controller, $matches);
            }
        }
        return null;
    }
}
