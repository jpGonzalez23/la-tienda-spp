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
require_once './controllers/VentaController.php';

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
  $group->get('/consultar', \ProductoController::class . ':TraerTodos');
  $group->post('/consultar/numero_de_producto/{id}', \ProductoController::class . ':TraerUno');
  $group->post('/consultar/nombre/marca/tipo', \ProductoController::class . ':TraerUnoPorNombreMarcaTipo');
});

$app->group('/ventas', function (RouteCollectorProxy $group) {
  $group->post('/alta', \VentaController::class . ':CargarUno');
  $group->get('/consultar', \VentaController::class . ':TraerTodos');
  $group->get('/consultar/numero_de_venta/{id}', \VentaController::class . ':TraerUno');
  $group->get('/consultar/productos/vendidos', \VentaController::class . ':ProductosVendidos');
  $group->get('/consultar/ventas/porUsuario/{mail}', \VentaController::class . ':VentasPorUsuario');
  $group->get('/consultar/ventas/porProducto/{tipo}', \VentaController::class . ':VentasPorProducto');
  $group->get('/consultar/productos/entreValores/{min}/{max}', \VentaController::class . ':ProductosEntreValores');
  $group->get('/productos/masVendidos', \VentaController::class . ':ProductosMasVendidos');
});

/*
$app->get('[/]', function (Request $request, Response $response) {
  $payload = json_encode(["mensaje" => "Slim Framework 4 PHP"]);

  $response->getBody()->write($payload);
  return $response->withHeader('Content-Type', 'application/json');
});
*/

$app->run();
