<?php
namespace App\Controladores;

use App\Modelos\Usuario;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ControladorAutenticacion
{
    public function registrar(Request $request, Response $response): Response
    {
        $datos = json_decode($request->getBody()->getContents(), true);

        if (!isset($datos['name'], $datos['email'], $datos['password'], $datos['role'])) {
            $response->getBody()->write(json_encode(['error' => 'Faltan datos obligatorios']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if (!in_array($datos['role'], ['gestor', 'admin'])) {
            $response->getBody()->write(json_encode(['error' => 'Rol no válido']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if (Usuario::where('email', $datos['email'])->exists()) {
            $response->getBody()->write(json_encode(['error' => 'El correo ya está registrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
        }

        $usuario = Usuario::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => password_hash($datos['password'], PASSWORD_BCRYPT),
            'role' => $datos['role']
        ]);

        $response->getBody()->write(json_encode([
            'mensaje' => 'Usuario registrado con éxito',
            'usuario' => $usuario
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}