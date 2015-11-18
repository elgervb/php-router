<?php
namespace router;

use router\Router;

/**
 *
 * @author eaboxt
 *        
 */
class TemplateViewTest extends \PHPUnit_Framework_TestCase
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
}
