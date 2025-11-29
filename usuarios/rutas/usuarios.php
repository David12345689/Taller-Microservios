<?php
use App\Controladores\ControladorAutenticacion;
use App\Controladores\ControladorSesion;
use App\Middlewares\AutenticacionMiddleware;

return function ($app) {
    $app->get('/', function ($req, $res) {
        $res->getBody()->write("Microservicio de Usuarios activo");
        return $res;
    });

    $app->post('/registrar', [ControladorAutenticacion::class, 'registrar']);
    $app->post('/login', [ControladorSesion::class, 'login']);
    $app->post('/logout', [ControladorSesion::class, 'logout'])->add(new AutenticacionMiddleware());

    $app->get('/protegido', function ($req, $res) {
        $res->getBody()->write(json_encode(['mensaje' => 'Acceso concedido']));
        return $res->withHeader('Content-Type', 'application/json');
    })->add(new AutenticacionMiddleware());
};