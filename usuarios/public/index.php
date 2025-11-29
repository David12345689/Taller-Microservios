<?php
use Slim\Factory\AppFactory;
use DI\Container;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/bd.php';


$contenedor = new Container();
AppFactory::setContainer($contenedor);

$app = AppFactory::create();

(require __DIR__ . '/../app/Middlewares/Cors.php')($app);

(require __DIR__ . '/../rutas/usuarios.php')($app);

$app->run();