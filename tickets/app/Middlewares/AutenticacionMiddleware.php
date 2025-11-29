<?php
namespace App\Middlewares;

use App\Modelos\Token;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class AutenticacionMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $headers = $request->getHeader('Authorization');
        if (!$headers) {
            return $this->unauthorized('Token no proporcionado');
        }

        $tokenTexto = trim(str_replace('Bearer', '', $headers[0]));

        $token = Token::where('token', $tokenTexto)->with('usuario')->first();

        if (!$token || !$token->usuario) {
            return $this->unauthorized('Token inválido o usuario no encontrado');
        }

        // Guardamos el usuario en los atributos de la request para usarlo después
        $request = $request->withAttribute('usuario', $token->usuario);

        return $handler->handle($request);
    }

    private function unauthorized($mensaje)
    {
        $response = new SlimResponse();
        $response->getBody()->write(json_encode(['error' => $mensaje]));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
}