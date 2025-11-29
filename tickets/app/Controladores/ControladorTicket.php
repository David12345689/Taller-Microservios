<?php
namespace App\Controladores;

use App\Modelos\Ticket;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Modelos\Token;

class ControladorTicket
{
    public function crear(Request $request, Response $response): Response
    {
        $usuario = $request->getAttribute('usuario'); 

        if (!$usuario || $usuario->role !== 'gestor') {
            $response->getBody()->write(json_encode([
                'error' => 'Solo los gestores pueden crear tickets'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        $datos = json_decode($request->getBody()->getContents(), true);

        if (!isset($datos['titulo'], $datos['descripcion'])) {
            $response->getBody()->write(json_encode(['error' => 'Faltan datos del ticket']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $ticket = Ticket::create([
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'gestor_id' => $usuario->id,
            'estado' => 'abierto'
        ]);

        $response->getBody()->write(json_encode([
            'mensaje' => 'Ticket creado correctamente',
            'ticket' => $ticket
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
    
    public function listar(Request $request, Response $response): Response
    {
        $usuario = $request->getAttribute('usuario');

        if (!$usuario) {
            $response->getBody()->write(json_encode(['error' => 'No autenticado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        if ($usuario->role === 'gestor') {
            $tickets = \App\Modelos\Ticket::where('gestor_id', $usuario->id)->get();
        } elseif ($usuario->role === 'admin') {
            $tickets = \App\Modelos\Ticket::all();
        } else {
            $response->getBody()->write(json_encode(['error' => 'Rol no autorizado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        $response->getBody()->write(json_encode(['tickets' => $tickets]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function verDetalle(Request $request, Response $response, array $args): Response
    {
        $usuario = $request->getAttribute('usuario');
        $ticketId = $args['id'] ?? null;

        if (!$ticketId || !is_numeric($ticketId)) {
            $response->getBody()->write(json_encode(['error' => 'ID de ticket inválido']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $ticket = \App\Modelos\Ticket::with('actividad')->find($ticketId);

        if (!$ticket) {
            $response->getBody()->write(json_encode(['error' => 'Ticket no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        if ($usuario->role === 'gestor' && $ticket->gestor_id !== $usuario->id) {
            $response->getBody()->write(json_encode(['error' => 'No autorizado para ver este ticket']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        $response->getBody()->write(json_encode(['ticket' => $ticket]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function comentar(Request $request, Response $response, array $args): Response
    {
        $usuario = $request->getAttribute('usuario');
        $ticketId = $args['id'] ?? null;

        if (!$ticketId || !is_numeric($ticketId)) {
            $response->getBody()->write(json_encode(['error' => 'ID inválido']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $ticket = \App\Modelos\Ticket::find($ticketId);
        if (!$ticket) {
            $response->getBody()->write(json_encode(['error' => 'Ticket no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Solo el gestor dueño o cualquier admin puede comentar
        if ($usuario->role === 'gestor' && $ticket->gestor_id !== $usuario->id) {
            $response->getBody()->write(json_encode(['error' => 'No autorizado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        $datos = json_decode($request->getBody()->getContents(), true);
        if (empty($datos['mensaje'])) {
            $response->getBody()->write(json_encode(['error' => 'Mensaje requerido']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $actividad = new \App\Modelos\ActividadTicket();
        $actividad->ticket_id = $ticket->id;
        $actividad->user_id = $usuario->id;
        $actividad->mensaje = $datos['mensaje'];
        $actividad->save();

        $response->getBody()->write(json_encode(['mensaje' => 'Comentario guardado']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
    public function asignar(Request $request, Response $response, array $args): Response
    {
        $usuario = $request->getAttribute('usuario');
        $ticketId = $args['id'] ?? null;

        if ($usuario->role !== 'admin') {
            $response->getBody()->write(json_encode(['error' => 'Solo administradores pueden asignar tickets']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        $ticket = \App\Modelos\Ticket::find($ticketId);
        if (!$ticket) {
            $response->getBody()->write(json_encode(['error' => 'Ticket no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $datos = json_decode($request->getBody()->getContents(), true);
        $adminId = $datos['admin_id'] ?? null;

        $admin = \App\Modelos\User::where('id', $adminId)->where('role', 'admin')->first();
        if (!$admin) {
            $response->getBody()->write(json_encode(['error' => 'ID de administrador no válido']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $ticket->admin_id = $admin->id;
        $ticket->save();

        $response->getBody()->write(json_encode(['mensaje' => 'Ticket asignado correctamente']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function cambiarEstado(Request $request, Response $response, array $args): Response
    {
        $usuario = $request->getAttribute('usuario');
        $ticketId = $args['id'] ?? null;

        if ($usuario->role !== 'admin') {
            $response->getBody()->write(json_encode(['error' => 'Solo administradores pueden cambiar el estado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        $ticket = \App\Modelos\Ticket::find($ticketId);
        if (!$ticket) {
            $response->getBody()->write(json_encode(['error' => 'Ticket no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $datos = json_decode($request->getBody()->getContents(), true);
        $estado = $datos['estado'] ?? null;

        $estadosValidos = ['abierto', 'en_progreso', 'resuelto', 'cerrado'];
        if (!in_array($estado, $estadosValidos)) {
            $response->getBody()->write(json_encode(['error' => 'Estado inválido']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $ticket->estado = $estado;
        $ticket->save();

        $response->getBody()->write(json_encode(['mensaje' => 'Estado actualizado']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}