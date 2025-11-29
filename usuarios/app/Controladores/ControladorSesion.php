<?php
namespace App\Controladores;

use App\Modelos\Usuario;
use App\Modelos\Token;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ControladorSesion
{
    public function login(Request $request, Response $response): Response
    {
        $datos = json_decode($request->getBody()->getContents(), true);

        if (!isset($datos['email'], $datos['password'])) {
            $response->getBody()->write(json_encode(['error' => 'Faltan credenciales']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $usuario = Usuario::where('email', $datos['email'])->first();

        if (!$usuario || !password_verify($datos['password'], $usuario->password)) {
            $response->getBody()->write(json_encode(['error' => 'Credenciales inválidas']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = bin2hex(random_bytes(32));

        $usuario->tokens()->create([
            'token' => $token
        ]);

        $response->getBody()->write(json_encode([
            'mensaje' => 'Inicio de sesión exitoso',
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->name,
                'rol' => $usuario->role
            ]
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function logout(Request $request, Response $response): Response
    {
        $tokenHeader = $request->getHeaderLine('Authorization');
        $token = trim(str_replace('Bearer', '', $tokenHeader));

        $eliminado = Token::where('token', $token)->delete();

        $mensaje = $eliminado ? 'Sesión cerrada correctamente' : 'Token no encontrado o ya eliminado';

        $response->getBody()->write(json_encode(['mensaje' => $mensaje]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}