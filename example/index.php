<?php
use router\Router;
function loader($class)
{
    $file = $class . '.php';
    if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . '../src'. DIRECTORY_SEPARATOR . $file)) {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '../src'. DIRECTORY_SEPARATOR . $file;
    }
}
spl_autoload_register('loader');
include __DIR__ . '/../vendor/autoload.php';


$comment = '<h2>Routes</h2>
    <ul>
        <li><a href=".">root</a></li>
        <li><a href="/arg1">/:arg1</a> <a href="/arg2">/:arg1</a></li>
    </ul>
    <hr />';

$router = new Router();
$router->route('/', function() use ($comment){
	return $comment . 'Root';
})->route('/:arg1', function($arg1) use ($comment){
	return $comment . 'route with argument "' . $arg1 . '"';
});


echo $router->match($_SERVER['REQUEST_URI']);
