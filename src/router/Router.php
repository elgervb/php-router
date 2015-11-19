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
     * @var \ArrayObject
     */
    private $routes;
    private $regexes;

    public function __construct()
    {
        $this->routes = new \ArrayObject();
        $this->regexes = new \ArrayObject();
    }

    /**
     * Add a new route
     *
     * @param string $route
     *            the path to the route
     * @param \Closure $aController
     *            the controller to execute when a route matches
     * @param string $requestType
     *            the HTTP request method
     *            
     * @return \compact\routing\Router for chaining purposes
     */
    public function route($route, \Closure $controller, $requestMethod = 'GET')
    {
        $requestMethod = strtoupper($requestMethod);
        $routes = $this->getRoutes(strtoupper($requestMethod));
        $routes->offsetSet($route, $controller);
       
        $this->buildRegex($route);
        
        return $this;
    }
    
    /**
     * Build a regex to match the route. This will replace all route params with a regex matcher.
     * 
     * @param string $route
     */
    private function buildRegex($route) {
        $regex = $route;
        
        // check route params
        if (preg_match_all("#(/:[^/]+)#i", $route, $matches)) {
            $regex = $route;
            foreach ($matches[1] as $part) {
                $search = preg_quote($part);
                // replace the route param. Make use we only replace one
                $regex = preg_replace("#$search#i", '/(.*)', $regex, 1);
            }
        }
        $this->regexes->offsetSet($route, $regex);
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
    public function match($path, $requestMethod = 'GET')
    {
        $requestMethod = strtoupper($requestMethod); 
        $routes = $this->getRoutes($requestMethod);
        
        // check regex
        foreach ($routes as $route => $controller) {
            //$path = preg_replace("/\//", "\\\/", $path);
            $regex = $this->regexes->offsetGet($route);
            
            if (preg_match("#{$regex}#", $path, $matches)) {
                array_shift($matches);
                return call_user_func_array($controller, $matches);
            }
        }
        return null;
    }
}
