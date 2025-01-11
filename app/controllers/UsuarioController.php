<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
  private $perfilesValidos = ['administrador', 'empleado', 'cliente'];

  public function CargarUno($request, $response, $args)
  {
    $params = $request->getParsedBody();
    $files = $request->getUploadedFiles();

    if (empty($params['mail']) || empty($params['usuario']) || empty($params['contrasenia']) || empty($params['perfil'])) {
      $playload = json_encode(["mensaje" => "Faltan datos"]);
      $response->getBody()->write($playload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    $mail = $params['mail'];
    $usuario = $params['usuario'];
    $contrasenia = password_hash($params['contrasenia'], PASSWORD_DEFAULT);
    $perfil = $params['perfil'];
    $fecha_de_alta = date('Y-m-d H:i:s');
    $foto = $files['foto'] ?? null;

    if (isset($files['foto']) && $files['foto']->getError() === UPLOAD_ERR_OK) {
      $nombreImagen = "{$usuario}_{$perfil}" . date("Ymd_His") . ".jpg";
      $rutaImagen = "../public/ImagenesDeUsuarios/2024/{$nombreImagen}";

      if (!is_dir("../public/ImagenesDeUsuarios/2024")) {
        mkdir("../public/ImagenesDeUsuarios/2024", 0777, true);
      }
      $files['foto']->moveTo($rutaImagen);
    }

    if (in_array($perfil, $this->perfilesValidos) === false) {
      $playload = json_encode(["mensaje" => "Perfil invalido"]);
      $response->getBody()->write($playload);
      return $response->withHeader('Content-Type', 'application/json');
    } else {
      $user = new Usuario();
      $user->setMail($mail);
      $user->setUsuario($usuario);
      $user->setContrasenia($contrasenia);
      $user->setPerfil($perfil);
      $user->setFechaDeAlta($fecha_de_alta);
      $user->setFoto($nombreImagen);

      Usuario::create($user);

      $playload = json_encode(["mensaje" => "Usuario creado con exito"]);
    }
    $response->getBody()->write($playload);
    return $response->withHeader('Content-Type', 'application/json');
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
}
