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
    public function route($name, $path, \Closure $controller, $requestMethod = 'GET')
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
    
    /**
     * Get the url of a named route
     *
     * @param string $name the name of the route
     * @param string $arguments the arguments matching the route params
     *
     * @return string|Ambigous <mixed, string>
     *
     * @throws RouterException when number of arguments and the number of route params do not match
     */
    public function url($name, $arguments = null)
    {
        if (! $this->routes->offsetExists($name)) {
            throw new RouterException("No such route here: $name");
        }
        $arguments = func_get_args();
        array_shift($arguments); // remove the $name argument
    
        /* @var $route Route */
        $route = $this->routes->offsetGet($name);
        $path = $route->getPath();
        $url = $path;
    
        // replace route params with arguments
        if (preg_match_all(Route::ROUTE_PARAMS_REGEX, $path, $matches)) {
            // arguments and params MUST be the same
            if (count($arguments) !== count($matches[1])) {
                throw new RouterException('Number of given arguments ('.count($arguments).') must be equal to the number of route params ('.count($matches[1]).')');
            }
    
            foreach ($matches[1] as $part) {
                $search = preg_quote($part);
                // replace the route param. Make use we only replace one, but stop at the next slash
                $url = preg_replace("#$search#i", '/'.array_shift($arguments), $url, 1);
            }
        }
    
        // calculate base
        $base  = dirname($_SERVER['PHP_SELF']);
        // sometimes the file part (like index.php) just seems to stick... remove it here
        if (preg_match('#(/[a-zA-Z0-9]+\.php)#', $base, $matches)){
            $base = str_replace($matches[1], '', $base);
        }
        // Update request when we have a subdirectory
        if(ltrim($base, DIRECTORY_SEPARATOR)){
    
            return $base . $url;
        }
    
        return $url;
    }
}
