<?php
use Slim\Factory\AppFactory;
use DI\Container;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/bd.php';

$container = new Container();
AppFactory::setContainer($container);

$app = AppFactory::create();

// CORS (si quieres usarlo)
(require __DIR__ . '/../app/Middlewares/Cors.php')($app);

// Rutas
(require __DIR__ . '/../rutas/tickets.php')($app);

$app->run();