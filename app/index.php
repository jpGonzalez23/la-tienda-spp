<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
// require_once './middlewares/Logger.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();


// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/tienda', function (RouteCollectorProxy $group) {
  $group->post('/alta', \ProductoController::class . ':CargarUno');
  //$group->post('/consulta', \ProductoController::class . ':TraerTodos');
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->post('/consulta/numero_de_producto/{id}', \ProductoController::class . ':TraerUno');
  $group->post('/consulta/', \ProductoController::class . ':TraerUnoPorNombreMarcaTipo');
});

$app->get('[/]', function (Request $request, Response $response) {
  $payload = json_encode(["mensaje" => "Slim Framework 4 PHP"]);

  $response->getBody()->write($payload);
  return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
