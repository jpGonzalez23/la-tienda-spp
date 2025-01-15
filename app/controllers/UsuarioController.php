<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UsuarioController extends Usuario implements IApiUsable
{
  private $perfilesValidos = ['administrador', 'empleado', 'cliente'];

  public function CargarUno($request, $response, $args)
  {
    $params = $request->getParsedBody();
    $files = $request->getUploadedFiles();

    if(empty($params['usuario']) || empty($params['contrasenia']) || empty($params['perfil'])) {
      $payload = json_encode(["mensaje" => "Faltan datos"]);
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    $mail = $params['mail'];
    $usuario = $params['usuario'];
    $contrasenia = password_hash($params['contrasenia'], PASSWORD_BCRYPT);
    $perfil = $params['perfil'];
    $fecha_de_alta = date('Y-m-d H:i:s');
    $foto = null;

    if(!in_array($perfil, $this->perfilesValidos)) {
      $payload = json_encode(["mensaje" => "Perfil invalido. Perfiles válidos: cliente, empleado, admin."]);
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    if(isset($files['foto']) && $files['foto']->getError() === UPLOAD_ERR_OK) {
      $foto = "{$usuario}_{$perfil}_" . date("Ymd_His") . ".jpg";
      $rutaFoto = "../public/ImagenesDeUsuarios/{$foto}";

      if(!is_dir("../public/ImagenesDeUsuarios/2024")) {
        mkdir("../public/ImagenesDeUsuarios/2024", 0777, true);
      }
      $files['foto']->moveTo($rutaFoto);

      $usuarioExistente = Usuario::findByMailOrUser($mail, $usuario);

      if($usuarioExistente) {
        $payload = json_encode(["mensaje" => "Usuario o mail ya existente"]);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
      }

      $user = new Usuario();
      $user->setMail($mail);
      $user->setUsuario($usuario);
      $user->setContrasenia($contrasenia);
      $user->setPerfil($perfil);
      $user->setFoto($foto);
      $user->setFechaDeAlta($fecha_de_alta);
      $user->setFechaDeBaja(null);  
      $resultado = Usuario::create($user);

      if($resultado) {
        $payload = json_encode(["mensaje" => "Usuario creado con exito"]);
      }
      else {
        $payload = json_encode(["mensaje" => "Error al crear usuario"]);
      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos usuario por nombre
    $usr = $args['usuario'];
    $usuario = Usuario::read($usr);
    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Usuario::readAll();
    $payload = json_encode(array("listaUsuario" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nombre = $parametros['nombre'];
    Usuario::update($nombre);

    $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuarioId = $parametros['usuarioId'];
    Usuario::delete($usuarioId);

    $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function Login($request, $response, $args)
  {
    $params = $request->getParsedBody();
    if(empty($params['usuario']) || empty($params['contrasenia'])) {
      $playload = json_encode(
        [
          "mensaje" => "Faltan datos"
        ]
      );
      $response->getBody()->write($playload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    $usuario = $params['usuario'];
    $contrasenia = $params['contrasenia'];

    $usuarioDB = Usuario::read($usuario);

    if(!$usuarioDB) {
      $playload = json_encode(
        [
          "mensaje" => "Usuario no encontrado"
        ]
      );
      $response->getBody()->write($playload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    if(!password_verify($contrasenia, $usuarioDB->getContrasenia())) {
      $playload = json_encode(["mensaje" => "Contraseña incorrecta"]);
      $response->getBody()->write($playload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    $token = $this->generarToken($usuarioDB);

    $playload = json_encode(
      [
        "mensaje" => "Login exitoso", 
        "token" => $token
      ]
    );

    $response->getBody()->write($playload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  private function generarToken($usuario)
  {
    $ahora = time();
    $expiracion = $ahora + 3600;

    $dataToken = [
      'iat' => $ahora,
      'exp' => $expiracion,
      'data' => [
        'id' => $usuario->getId(),
        'usuario' => $usuario->getUsuario(),
        'perfil' => $usuario->getPerfil()
      ]
    ];

    return JWT::encode($dataToken, 'mi_clave_secreta', 'HS256');
  }
}
