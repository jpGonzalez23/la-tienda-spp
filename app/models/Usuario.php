<?php

require_once './interfaces/ICrud.php';
require_once './db/AccesoDatos.php';

class Usuario implements ICrud
{
    private $id;
    private $mail;
    private $usuario;
    private $contrasenia;
    private $perfil;
    private $foto;
    private $fecha_de_alta;
    private $fecha_de_baja;

    public function getId()
    {
        return $this->id;
    }

    public function getMail()
    {
        return $this->mail;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function getContrasenia()
    {
        return $this->contrasenia;
    }

    public function getPerfil()
    {
        return $this->perfil;
    }

    public function getFoto()
    {
        return $this->foto;
    }

    public function getFechaDeAlta()
    {
        return $this->fecha_de_alta;
    }

    public function getFechaDeBaja()
    {
        return $this->fecha_de_baja;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setMail($mail)
    {
        $this->mail = $mail;
    }


    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    public function setContrasenia($contrasenia)
    {
        $this->contrasenia = $contrasenia;
    }

    public function setPerfil($perfil)
    {
        $this->perfil = $perfil;
    }

    public function setFoto($foto)
    {
        $this->foto = $foto;
    }

    public function setFechaDeAlta($fecha_de_alta)
    {
        $this->fecha_de_alta = $fecha_de_alta;
    }

    public function setFechaDeBaja($fecha_de_baja)
    {
        $this->fecha_de_baja = $fecha_de_baja;
    }

    public function __construct() {}

    /**
     * Crea un nuevo usuario en la base de datos.
     *
     * @param object $data Un objeto que contiene los datos del usuario.
     * @return int El id del usuario recien creado o un array con el error
     * @throws Exception Si ocurre un error al crear el usuario
     */
    public static function create($data)
    {
        $db = AccesoDatos::obtenerInstancia();
        $querry = $db->prepararConsulta("INSERT INTO usuario (mail, usuario, contrasenia, perfil, foto, fecha_de_alta) VALUES (:mail, :usuario, :contrasenia, :perfil, :foto, :fecha_de_alta)");
        $querry->bindValue(':mail', $data->getMail(), PDO::PARAM_STR);
        $querry->bindValue(':usuario', $data->getUsuario(), PDO::PARAM_STR);
        $querry->bindValue(':contrasenia', $data->getContrasenia(), PDO::PARAM_STR);
        $querry->bindValue(':perfil', $data->getPerfil(), PDO::PARAM_STR);
        $querry->bindValue(':foto', $data->getFoto(), PDO::PARAM_STR);
        $querry->bindValue(':fecha_de_alta', $data->getFechaDeAlta(), PDO::PARAM_STR);

        $querry->execute();

        return $db->obtenerUltimoId();
    }

    /**
     * Lee un usuario de la base de datos
     *
     * @param string $data El usuario a leer
     *
     * @return Usuario El usuario leido, o un array con la clave "error" y "exception" en caso de error
     */
    public static function read($data)
    {
        $db = AccesoDatos::obtenerInstancia();
        $querry = $db->prepararConsulta("SELECT * FROM usuario WHERE usuario = :usuario");
        $querry->bindValue(':usuario', $data, PDO::PARAM_STR);
        $querry->execute();

        return $querry->fetchObject('Usuario');
    }
    public static function readAll()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuario");
        $consulta->execute();
        return $consulta->fetchObject('Usuario');
    }

    /**
     * Actualiza un usuario en la base de datos.
     *
     * @param object $data Un objeto que contiene los datos del usuario.
     * @return void
     * @throws Exception Si ocurre un error al actualizar el usuario
     */
    public static function update($data)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuario SET usuario = :usuario, contrasenia = :contrasenia WHERE id = :id");
        $consulta->bindValue(':usuario', $data->getUsuario(), PDO::PARAM_STR);
        $consulta->bindValue(':contrasenia', $data->getContrasenia(), PDO::PARAM_STR);
        $consulta->bindValue(':id', $data->getId(), PDO::PARAM_INT);
        $consulta->execute();
    }

    /**
     * Marca un usuario como eliminado en la base de datos.
     *
     * @param int $usuario El ID del usuario a eliminar.
     * @return array|null Retorna un array con el mensaje de error y excepción en caso de ocurrir un error, o null si la operación fue exitosa.
     * @throws Exception Si ocurre un error al marcar al usuario como eliminado.
     */

    public static function delete($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuario SET fecha_de_baja = :fecha_de_baja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_de_baja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }
    /**
     * Busca un usuario por su mail o nombre de usuario.
     *
     * @param string $mail El mail del usuario a buscar.
     * @param string $user El nombre de usuario del usuario a buscar.
     *
     * @return Usuario El usuario encontrado, o un array con la clave "error" y "exception" en caso de error.
     *
     * @throws Exception Si ocurre un error al buscar el usuario.
     */
    public static function findByMailOrUser($mail, $user)
    {
        $db = AccesoDatos::obtenerInstancia();
        $querry = $db->prepararConsulta("SELECT * FROM usuario WHERE mail = :mail OR usuario = :usuario");
        $querry->bindValue(':mail', $mail, PDO::PARAM_STR);
        $querry->bindValue(':usuario', $user, PDO::PARAM_STR);
        $querry->execute();

        return $querry->fetchObject('Usuario');
    }
}
