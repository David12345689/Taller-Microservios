<?php
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app) {
    $app->options('/{routes:.+}', fn($req, $res) => $res);

    $app->add(function (Request $request, $handler) {
        $response = $handler->handle($request);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    });
};
