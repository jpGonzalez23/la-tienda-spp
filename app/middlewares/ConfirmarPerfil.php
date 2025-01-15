<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class ConfirmarPerfil
{
    private array $perfilesValidos;

    public function __construct(array $perfilesValidos)
    {
        $this->perfilesValidos = $perfilesValidos;
    }

    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        $headers = $request->getHeader('Authorization');

        // Verificar si el encabezado Authorization está presente
        if (empty($headers)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(["mensaje" => "Token no encontrado en el encabezado."]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = str_replace('Bearer ', '', $headers[0]); // Extraer el token JWT del encabezado

        try {
            // Decodificar el token
            $secretKey = 'mi_clave_secreta'; // Cambia esto por tu clave secreta
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            // Verificar el perfil en el token
            $perfil = $decoded->data->perfil ?? null;
            if (!$perfil || !in_array($perfil, $this->perfilesValidos)) {
                $response = new \Slim\Psr7\Response();
                $response->getBody()->write(json_encode(["mensaje" => "Perfil no autorizado."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
            }

            // Pasar al siguiente middleware o controlador
            return $handler->handle($request);
        } catch (\Exception $e) {
            // Manejar errores de JWT
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(["mensaje" => "Token inválido o expirado.", "error" => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }
}
