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
        if (is_numeric($id) && $id > 0) {
            $this->id = $id;
        }
    }

    public function setMail($mail)
    {
        if (is_string($mail) && strlen($mail) > 0) {
            $this->mail = $mail;
        }
    }


    public function setUsuario($usuario)
    {
        if (is_string($usuario) && strlen($usuario) > 0) {
            $this->usuario = $usuario;
        }
    }

    public function setContrasenia($contrasenia)
    {
        if (is_string($contrasenia) && strlen($contrasenia) > 0) {
            $this->contrasenia = $contrasenia;
        }
    }

    public function setPerfil($perfil)
    {
        if (is_string($perfil)) {
            $this->perfil = $perfil;
        }
    }

    public function setFoto($foto)
    {
        if (!empty($foto)) {
            $this->foto = $foto;
        }
    }

    public function setFechaDeAlta($fecha_de_alta)
    {
        if (is_string($fecha_de_alta) && strlen($fecha_de_alta) > 0) {
            $this->fecha_de_alta = $fecha_de_alta;
        }
    }

    public function setFechaDeBaja($fecha_de_baja)
    {
        if (is_string($fecha_de_baja) && strlen($fecha_de_baja) > 0) {
            $this->fecha_de_baja = $fecha_de_baja;
        }
    }

    public function __construct () {}

    public static function create($data)
    {
        try {
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
        } catch (Exception $e) {
            return ["error" => "Error al crear el usuario", "exception" => $e->getMessage()];
        }
    }

    public static function read($data)
    {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $querry = $db->prepararConsulta("SELECT * FROM usuario WHERE usuario = :usuario AND contrasenia = :contrasenia");
            $querry->bindValue(':usuario', $data->getUsuario(), PDO::PARAM_STR);
            $querry->bindValue(':contrasenia', $data->getContrasenia(), PDO::PARAM_STR);
            $querry->execute();

            return $querry->fetchAll(PDO::FETCH_CLASS, 'Usuario');
        } catch (Exception $e) {
            return ["error" => "Error al leer el usuario", "exception" => $e->getMessage()];
        }
    }

    public static function readAll()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuario");
            $consulta->execute();
            return $consulta->fetchObject('Usuario');
        } catch (Exception $e) {
            return ["error" => "Error al leer todos los usuarios", "exception" => $e->getMessage()];
        }
    }

    public static function update($data)
    {
        try {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE usuario SET usuario = :usuario, contrasenia = :contrasenia WHERE id = :id");
            $consulta->bindValue(':usuario', $data->getUsuario(), PDO::PARAM_STR);
            $consulta->bindValue(':contrasenia', $data->getContrasenia(), PDO::PARAM_STR);
            $consulta->bindValue(':id', $data->getId(), PDO::PARAM_INT);
            $consulta->execute();
        } catch (Exception $e) {
            return ["error" => "Error al actualizar el usuario", "exception" => $e->getMessage()];
        }
    }

    public static function delete($usuario)
    {
        try {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE usuario SET fecha_de_baja = :fecha_de_baja WHERE id = :id");
            $fecha = new DateTime(date("d-m-Y"));
            $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
            $consulta->bindValue(':fecha_de_baja', date_format($fecha, 'Y-m-d H:i:s'));
            $consulta->execute();
        } catch (Exception $e) {
            return ["error" => "Error al eliminar el usuario", "exception" => $e->getMessage()];
        }
    }
}
