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
        
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['PHP_SELF'] = '/index.php/';
    }

    protected function tearDown()
    {
        parent::tearDown();
    }
    
    public function testExisingNameFail(){
        $router = new Router();
        $router->route('test', '/', function ()
        {
            return 'rootRouteGet';
        });
        
        try {
           $router->route('test', '/', function ()
            {
                return 'rootRouteGet';
            });
           $this->fail('Expected exception');
        } catch (RouterException $ex) {
            //
        }
    }
    public function testNoMatch()
    {
        $router = new Router();
        $router->route('test', '/', function ()
        {
            return 'rootRouteGet';
        });
    
        $result = $router->match('/asdf');
        $this->assertFalse($result);
    }

    public function testRootGet()
    {
        $router = new Router();
        $router->route('test', '/', function ()
        {
            return 'rootRouteGet';
        });
        
        $result = $router->match('/');
        $this->assertEquals('rootRouteGet', $result);
    }

    public function testRootPost()
    {
        $router = new Router();
        $router->route('test','/', function ()
        {
            return 'rootRouteGet';
        })->route('test2','/', function ()
        {
            return 'rootRoutePost';
        }, 'POST');
        
        $result = $router->match('/', 'POST');
        $this->assertEquals('rootRoutePost', $result);
    }

    public function testRootGet1Param()
    {
        $router = new Router();
        $router->route('test','/:param', function ($arg1)
        {
            return $arg1;
        });
        
        $result = $router->match('/arg1');
        
        $this->assertNotNull($result);
        $this->assertEquals('arg1', $result);
    }

    public function testRootGet2Params()
    {
        $router = new Router();
        $router->route('test','/:param1/:param2', function ($arg1, $arg2)
        {
            return $arg1 . $arg2;
        });
        
        $result = $router->match('/param1/param2');
        $this->assertEquals('param1param2', $result);
    }

    public function testRootGetParamCombined()
    {
        $router = new Router();
        $router->route('test','/root/:param/path', function ($arg1)
        {
            return $arg1;
        });
        
        $result = $router->match('/root/arg1/path');
        
        $this->assertNotNull($result);
        $this->assertEquals('arg1', $result);
    }

    public function testRootGetParam2Combined()
    {
        $router = new Router();
        $router->route('test','/root/:param/path/:param2', function ($arg1, $arg2)
        {
            return $arg1 . $arg2;
        });
        
        $result = $router->match('/root/arg1/path/arg2');
        
        $this->assertNotNull($result);
        $this->assertEquals('arg1arg2', $result);
    }

    public function testRootGetParam2Combined2()
    {
        $router = new Router();
        $router->route('test','/root/:param2/path/:param', function ($arg1, $arg2)
        {
            return $arg1 . $arg2;
        });
        
        $result = $router->match('/root/arg2/path/arg1');
        
        $this->assertNotNull($result);
        $this->assertEquals('arg2arg1', $result);
    }

    public function testRoutesWithSameBasePath()
    {
        $router = new Router();
        $router->route('test','/root/:param/path/:param2', function ($arg1, $arg2)
        {
            return $arg1 . $arg2;
        })->route('test2', '/root/:param', function ($arg1)
        {
            return $arg1;
        });
        
        $result = $router->match('/root/arg1');
        
        $this->assertNotNull($result);
        $this->assertEquals('arg1', $result);
    }

    public function testRoutesWithSameBasePath2()
    {
        $router = new Router();
        $router->route('test','/root/:param', function ($arg1)
        {
            return $arg1;
        })->route('test2', '/root/:param/path/:param2', function ($arg1, $arg2)
        {
            return $arg1 . $arg2;
        });
        
        $result = $router->match('/root/arg1/path/arg2');
        
        $this->assertNotNull($result);
        $this->assertEquals('arg1arg2', $result);
    }
    
    public function testToUrl()
    {
        $path = '/';
        
        $router = new Router();
        $router->route('test', $path, function ()
        {
            return 'rootRouteGet';
        });
    
        $result = $router->toUrl('test');
        $this->assertEquals($result, $path);
    }
    
    public function testToUrl2Segments()
    {
        $path = '/root/path';
    
        $router = new Router();
        $router->route('test', $path, function ()
        {
            return 'rootRouteGet';
        });
    
        $result = $router->toUrl('test');
        $this->assertEquals($result, $path);
    }
    
    public function testToUrl1RouteParam()
    {
        $path = '/:arg';
    
        $router = new Router();
        $router->route('test', $path, function ()
        {
            return 'rootRouteGet';
        });
    
        $result = $router->toUrl('test', 'arg');
        $this->assertEquals($result, '/arg');
    }
    
    public function testToUrl2RouteParam()
    {
        $path = '/:arg/:arg2';
    
        $router = new Router();
        $router->route('test', $path, function ()
        {
            return 'rootRouteGet';
        });
    
        $result = $router->toUrl('test', 'arg', 'arg2');
        $this->assertEquals($result, '/arg/arg2');
    }
    
    public function testToUrlNoRouteFail()
    {
        $router = new Router();
        $router->route('test', '/', function ()
        {
            return 'rootRouteGet';
        });
    
        try {
            $router->toUrl('test2'); // test2 does not exist
            $this->fail('Exception expected');
        } catch(RouterException $ex) {
            //
        }
    }
    
    public function testToUrlArgumentsAndRouteParamsNotMatchFail()
    {
        $path = '/:arg/:arg2';
    
        $router = new Router();
        $router->route('test', $path, function ()
        {
            return 'rootRouteGet';
        });
    
        try {
          $router->toUrl('test', 'arg'); // 2 route params, 1 argument
          $this->fail('Exception expected');
        } catch(RouterException $ex) {
            //
        }
    }
}
