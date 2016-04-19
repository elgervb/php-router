<?php
namespace router;

/**
 *
 * @author eaboxt
 */
class Route
{
    const ROUTE_PARAMS_REGEX = "#(/:[^/]+)#i";

    private $controller;

    private $requestMethod;

    private $path;

    private $regex;

    /**
     * Route Contructor
     *
     * @param string $route the route path
     * @param \Closure $controller the controller function
     * @param string $requestMethod the HTTP request method this route is valid for
     */
    public function __construct($route, \Closure $controller, $requestMethod = 'GET')
    {
        $this->path = $route;
        $this->controller = $controller;
        $this->requestMethod = $requestMethod;
    }

    /**
     * Build a regex to match the route's path.
     * This will replace all route params with a regex matcher.
     */
    private function buildRegex()
    {
        $regex = $this->path;
        
        // check route params
        if (preg_match_all(self::ROUTE_PARAMS_REGEX, $this->path, $matches)) {
            foreach ($matches[1] as $part) {
                $search = preg_quote($part);
                // replace the route param. Make use we only replace one, but stop at the next slash
                $regex = preg_replace("#$search#i", '/([^/]+)', $regex, 1);
            }
        }
        $this->regex = $regex . '$';
    }

    /**
     * Returns the controller
     *
     * @return Closure
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Returns the path of the route
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Returns the regular expression to match a path to
     *
     * @return string
     */
    public function getRegex()
    {
        if ($this->regex === null) {
            $this->buildRegex();
        }
        return $this->regex;
    }
    
    /**
     * Returns the HTTP request method
     * 
     * @return string
     */
    public function getRequestMethod() {
        return $this->requestMethod;
    }
}
