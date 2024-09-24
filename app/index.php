<?php

ob_start();
require __DIR__ . "/../vendor/autoload.php";

# Get env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->safeLoad();

use Nahkampf\Larpcal\Larp;
use Nahkampf\Larpcal\Output;
use Nahkampf\Larpcal\DB;
use Nahkampf\Larpcal\Image;
use Nahkampf\Larpcal\Tags;
use Nahkampf\Larpcal\Utils;

# Set up routes
$router = new \Bramus\Router\Router();

// deletes and inserts require an API key
// so use this middleware
$router->before('POST|PUT|PATCH|DELETE', '/larp', function () {
    if (!isset($_SERVER['HTTP_X_API_KEY']) || $_SERVER['HTTP_X_API_KEY'] != $_ENV["API_KEY"]) {
        Output::write(["error" => "This operation requires authentication."], 403);
        exit();
    }
});

$router->get('/', function () {
    $filters = (!empty($_GET["filters"])) ? (array)$_GET["filters"] : null;
    $larps = Larp::getAll($filters);
    Output::write((array)$larps);
});

$router->get('/(\d+)', function (int $larpId) {
    $larp = Larp::getById($larpId);
    Output::write((array)$larp);
});


// create a larp
$router->post('/larp', function () {
    try {
        $payload = json_decode(file_get_contents('php://input'), true);
        $larp = new Larp($payload);
        $res = $larp->save();
        Output::write($res, 201);
    } catch (\Throwable $e) {
        Output::write("An error occured: " . $e->getMessage(), 400);
        exit;
    }
});

// Update a larp
$router->post('/larp/(\d+)', function ($larpId) {
});


// file uploads
$router->post('/larp/(\d+)/image', function (int $larpId) {
    $db = new DB();
    $larp = Larp::getById($larpId);
    Image::handleUpload($larp);
});

// Get a list of tags
$router->get('/tags', function () {
    $tags = Tags::getAll();
    Output::write($tags);
});

// Get a list of countries
$router->get('/countries', function () {
    $countries = Utils::getCountries();
    Output::write($countries);
});


$router->set404(function () {
    Output::write(
        ["error" => "404! It is pitch black. You are likely to be eaten by a grue."],
        404
    );
});

$router->run();
