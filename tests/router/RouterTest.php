<?php
namespace router;

use router\Router;

/**
 *
 * @author eaboxt
 *        
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }
    
    public function testRootGet()
    {
        $router = new Router();
        $router->route('/', function ()
        {
            return 'rootRouteGet';
        });
        
        $result = $router->match('/');
        $this->assertEquals('rootRouteGet', $result);
    }

    public function testRootPost()
    {
        $router = new Router();
        $router->route('/', function ()
        {
            return 'rootRouteGet';
        })->route('/', function ()
        {
            return 'rootRoutePost';
        }, 'POST');
        
        $result = $router->match('/', 'POST');
        $this->assertEquals('rootRoutePost', $result);
    }
    
    public function testRootGet1Param()
    {
        $router = new Router();
        $router->route('/:param', function ($arg1)
        {
            return $arg1;
        });
    
        $result = $router->match('/param');
        
        $this->assertNotNull($result);
        $this->assertEquals('param', $result);
    }
    
    public function testRootGet2Params()
    {
        $router = new Router();
        $router->route('/:param1/:param2', function ($arg1, $arg2)
        {
            return $arg1 . $arg2;
        });
    
        $result = $router->match('/param1/param2');
        $this->assertEquals('param1param2', $result);
    }
    
}
