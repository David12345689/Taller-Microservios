<?php
namespace App\Middlewares;

use App\Modelos\Token;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response;

class AutenticacionMiddleware
{
    public function __invoke(Request $request, Handler $handler): Response
    {
        // Obtener encabezado Authorization
        $tokenHeader = $request->getHeaderLine('Authorization');

        if (!$tokenHeader || !str_starts_with($tokenHeader, 'Bearer ')) {
            return $this->respuestaNoAutorizado('Token no proporcionado');
        }

        // Extraer el token sin "Bearer" y quitar espacios
        $token = trim(str_replace('Bearer', '', $tokenHeader));

        // Validar si el token existe en la base de datos
        $existe = Token::where('token', $token)->exists();

        if (!$existe) {
            return $this->respuestaNoAutorizado('Token inválido');
        }

        // Si el token es válido, continúa
        return $handler->handle($request);
    }

    private function respuestaNoAutorizado($mensaje): Response
    {
        $respuesta = new Response();
        $respuesta->getBody()->write(json_encode(['error' => $mensaje]));
        return $respuesta->withHeader('Content-Type', 'application/json')->withStatus(401);
    }
    public function logout(Request $request, Response $response): Response
    {
        $tokenHeader = $request->getHeaderLine('Authorization');

        // Extraer token limpio
        $token = trim(str_replace('Bearer', '', $tokenHeader));

        // Eliminar el token de la base de datos
        $eliminado = \App\Modelos\Token::where('token', $token)->delete();

        if ($eliminado) {
            $mensaje = 'Sesión cerrada correctamente';
        } else {
            $mensaje = 'Token no encontrado o ya eliminado';
        }

        $response->getBody()->write(json_encode(['mensaje' => $mensaje]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}     