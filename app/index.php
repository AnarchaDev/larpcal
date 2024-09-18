<?php
require __DIR__ . "/../vendor/autoload.php";

# Get env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

use \Nahkampf\Larpcal\Larp;

# Set up routes
$router = new \Bramus\Router\Router();

// deletes and inserts require an API key
// so use this middleware
$router->before('GET|POST|PUT|PATCH', '/edit/.*', function () {
    if (!isset($_SERVER['HTTP_X_API_KEY']) || $_SERVER['HTTP_X_API_KEY'] != $_ENV["API_KEY"]) {
        ob_start();
        \Nahkampf\Larpcal\Output::write(["error" => "This operation requires authentication."], 403);
        exit();
    }
});

$router->get('/', function () {
    $filters = ($_GET["filters"]) ? (array)$_GET["filters"] : null;
    $larps = Larp::getAll($filters);
    \Nahkampf\Larpcal\Output::write($larps);
});

$router->set404(function () {
    \Nahkampf\Larpcal\Output::write(
        ["error" => "404! It is pitch black. You are likely to be eaten by a grue."],
        404
    );
});

$router->run();
