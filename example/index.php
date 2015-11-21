<?php
use router\Router;
function loader($class)
{
    $file = $class . '.php';
    if (file_exists(__DIR__ . '/../src/' . $file)) {
        require_once __DIR__ . '/../src/' . $file;
    }
}
spl_autoload_register('loader');
include __DIR__ . '/../vendor/autoload.php';


$router = new Router();
$comment = '';

$router->route('root', '/', function() use (&$comment){
	return $comment . 'Root';
})->route('args', '/:arg1', function($arg1) use (&$comment){
	return $comment . 'route with argument "' . $arg1 . '"';
});


$comment = '<h2>Routes</h2>
<ul>
    <li><a href="'.$router->toUrl('root').'">root</a></li>
    <li><a href="'.$router->toUrl('args', 'arg1').'">/:arg1</a> <a href="'.$router->toUrl('args', 'arg2').'">/:arg1</a></li>
</ul>
<hr />';
    
echo $router->match($_SERVER['REQUEST_URI']);
