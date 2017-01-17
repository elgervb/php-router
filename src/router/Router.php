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

    /**
     * Create a new router
     */
    public function __construct()
    {
        $this->routes = new \ArrayObject();
    }

    /**
     * Add a new route
     *
     * @param string $path the path to the route
     * @param \Closure $controller the controller to execute when a route matches
     * @param string $requestType the HTTP request method
     *            
     * @return \routing\Router for chaining purposes
     *        
     * @throws \router\RouterException when route with same key already exists
     */
    public function route($path, \Closure $controller, $requestMethod = 'GET')
    {
        $key = $path . strtoupper($requestMethod);
        if ($this->routes->offsetExists($key)) {
            throw new RouterException("Route with key $key already exists");
        }
        $route = new Route($path, $controller, strtoupper($requestMethod));
        $this->routes->offsetSet($key, $route);
        
        return $this;
    }

    /**
     * Executes a controller registered to the path with the route params as arguments
     *
     * @param string $path the request path to check for eg. /route/:arg1/:arg2
     * @param string $requestMethod the HTTP request method
     *            
     * @return mixed The result from the controller or <code>false</code> when route could not be found
     */
    public function match($path, $requestMethod = 'GET')
    {
        $requestMethod = strtoupper($requestMethod);
        
        // strip url hash and querystring from url. These can be accessed as params and are not part of the url matching
        $i =0; // prevent endless loop
        while(preg_match('/(.*)[\?|\#]+/', $path, $matches) && $i<5) {
            $path = $matches[1];
            $i++;
        }
        
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
