<?php

require_once './interfaces/ICrud.php';

class Producto implements ICrud
{
    private $id;
    private $nombre;
    private $tipo;
    private $marca;
    private $stock;
    private $precio;
    private $imagen;

    public function __construct() {}

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        if ($id > 0) {
            $this->id = $id;
        } else {
            throw new Exception("El ID debe ser positivo");
        }
    }

    public function getNombre()
    {
        return $this->nombre;
    }
    public function setNombre($nombre)
    {
        if (!empty($nombre)) {
            $this->nombre = $nombre;
        } else {
            throw new Exception("El nombre no puede estar vacio");
        }
    }

    public function getPrecio()
    {
        return $this->precio;
    }
    public function setPrecio($precio)
    {
        if ($precio > 0) {
            $this->precio = $precio;
        } else {
            throw new Exception("El precio debe ser positivo");
        }
    }

    public function getTipo()
    {
        return $this->tipo;
    }
    public function setTipo($tipo)
    {
        if (in_array($tipo, ['Smartphone', 'Tablet'])) {
            $this->tipo = $tipo;
        } else {
            throw new Exception("El tipo debe ser 'Smartphone' o 'Tablet'.");
        }
    }

    public function getMarca()
    {
        return $this->marca;
    }
    public function setMarca($marca)
    {
        if (!empty($marca)) {
            $this->marca = $marca;
        } else {
            throw new Exception("La marca no puede estar vacia");
        }
    }

    public function getStock()
    {
        return $this->stock;
    }
    public function setStock($stock)
    {
        if ($stock > 0) {
            $this->stock = $stock;
        } else {
            throw new Exception("El stock debe ser positivo");
        }
    }

    public function getImagen()
    {
        return $this->imagen;
    }

    public function setImagen($imagen)
    {
        if (!empty($imagen) && is_string($imagen)) {
            $this->imagen = $imagen;
        }
    }

    public static function create($data)
    {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("INSERT INTO producto (nombre, tipo, marca, stock, precio, imagen) VALUES (:nombre, :tipo, :marca, :stock, :precio, :imagen)");

            $query->bindValue(':nombre', $data->getNombre(), PDO::PARAM_STR);
            $query->bindValue(':tipo', $data->getTipo(), PDO::PARAM_STR);
            $query->bindValue(':marca', $data->getMarca(), PDO::PARAM_STR);
            $query->bindValue(':stock', $data->getStock(), PDO::PARAM_INT);
            $query->bindValue(':precio', $data->getPrecio(), PDO::PARAM_STR);
            $query->bindValue(':imagen', $data->getImagen(), PDO::PARAM_STR);

            $query->execute();

            return $db->obtenerUltimoId();
        } catch (Exception $e) {
            return ["mensaje" => "Error al crear el producto", "error" => $e->getMessage()];
        }
    }

    public static function readAll()
    {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("SELECT * FROM producto");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ["mensaje" => "Error al leer los productos", "error" => $e->getMessage()];
        }
    }

    public static function read($id)
    {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("SELECT * FROM producto WHERE id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();

            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ["mensaje" => "Error al leer el producto", "error" => $e->getMessage()];
        }
    }

    public static function update($data)
    {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("UPDATE producto SET nombre = :nombre, tipo = :tipo, marca = :marca, stock = :stock, precio = :precio WHERE id = :id");
            $query->bindValue(':id', $data->getId(), PDO::PARAM_INT);
            $query->bindValue(':nombre', $data->getNombre(), PDO::PARAM_STR);
            $query->bindValue(':tipo', $data->getTipo(), PDO::PARAM_STR);
            $query->bindValue(':marca', $data->getMarca(), PDO::PARAM_STR);
            $query->bindValue(':stock', $data->getStock(), PDO::PARAM_INT);
            $query->bindValue(':precio', $data->getPrecio(), PDO::PARAM_INT);
            $query->execute();
        } catch (Exception $e) {
            return ["mensaje" => "Error al actualizar el producto", "error" => $e->getMessage()];
        }
    }

    public static function delete($id)
    {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("DELETE FROM productos WHERE id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
        } catch (Exception $e) {
            return ["mensaje" => "Error al eliminar el producto", "error" => $e->getMessage()];
        }
    }

    public static function findByMarcaAndTipo($marca, $tipo)
    {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("SELECT * FROM producto WHERE marca = :marca AND tipo = :tipo");
            $query->bindValue(':marca', $marca, PDO::PARAM_STR);
            $query->bindValue(':tipo', $tipo, PDO::PARAM_STR);
            $query->execute();

            return $query->fetchObject('Producto');
        } catch (Exception $e) {
            return ["mensaje" => "Error al buscar el producto", "error" => $e->getMessage()];
        }
    }

    public static function findByNombreAndMarcaAndTipo($nombre, $marca, $tipo)
    {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("SELECT * FROM producto WHERE nombre = :nombre AND marca = :marca AND tipo = :tipo");
            $query->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $query->bindValue(':marca', $marca, PDO::PARAM_STR);
            $query->bindValue(':tipo', $tipo, PDO::PARAM_STR);
            $query->execute();

            return $query->fetchObject('Producto');
        } catch (Exception $e) {
            return ["mensaje" => "Error al buscar el producto", "error" => $e->getMessage()];
        }
    }

    public static function findByNombre($nombre)
    {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("SELECT * FROM producto WHERE nombre = :nombre");
            $query->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $query->execute();

            return $query->fetch(PDO::FETCH_ASSOC) !== false;
        } catch (Exception $e) {
            return ["mensaje" => "Error al buscar el producto", "error" => $e->getMessage()];
        }
    }

    public static function findByMarca($marca)
    {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("SELECT * FROM producto WHERE marca = :marca");
            $query->bindValue(':marca', $marca, PDO::PARAM_STR);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) !== false;
        } catch (Exception $e) {
            return ["mensaje" => "Error al buscar el producto", "error" => $e->getMessage()];
        }
    }

    public static function findByTipo($tipo)
    {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("SELECT * FROM producto WHERE tipo = :tipo");
            $query->bindValue(':tipo', $tipo, PDO::PARAM_STR);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) !== false;
        } catch (Exception $e) {
            return ["mensaje" => "Error al buscar el producto", "error" => $e->getMessage()];
        }
    }
}
