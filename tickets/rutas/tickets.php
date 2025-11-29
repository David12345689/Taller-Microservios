<?php
use Slim\App;
use App\Controladores\ControladorTicket;
use App\Middlewares\AutenticacionMiddleware;

return function (App $app) {
    $app->put('/tickets/{id}/estado', [ControladorTicket::class, 'cambiarEstado'])->add(new AutenticacionMiddleware());
    $app->put('/tickets/{id}/asignar', [ControladorTicket::class, 'asignar'])->add(new AutenticacionMiddleware());
    $app->post('/tickets/{id}/comentario', [ControladorTicket::class, 'comentar'])->add(new AutenticacionMiddleware());
    $app->post('/tickets', [ControladorTicket::class, 'crear'])->add(new AutenticacionMiddleware());
    $app->get('/tickets', [ControladorTicket::class, 'listar'])->add(new AutenticacionMiddleware());
    $app->get('/tickets/{id}', [ControladorTicket::class, 'verDetalle'])->add(new AutenticacionMiddleware());
    $app->get('/', function ($req, $res) {
        $res->getBody()->write("Microservicio de Tickets activo");
        return $res;
    });
    $app->get('/debug', function ($req, $res) use ($app) {
    $routes = $app->getRouteCollector()->getRoutes();
    foreach ($routes as $route) {
        $res->getBody()->write($route->getPattern() . ' [' . implode(', ', $route->getMethods()) . "]\n");
    }
    return $res;
});
    
};
