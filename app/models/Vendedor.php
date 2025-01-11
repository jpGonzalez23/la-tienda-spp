<?php

require_once './interfaces/ICrud.php';
require_once './db/AccesoDatos.php';

class Vendedor implements ICrud
{

    private $id;
    private $mail;
    private $nombre;
    private $tipo;
    private $marca;
    private $stock;
    private $precio_total;
    private $fecha_venta;
    private $numero_pedido;
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

    public function getMail()
    {
        return $this->mail;
    }

    public function setMail($mail)
    {
        if (!empty($mail)) {
            $this->mail = $mail;
        }
    }

    public function getNombreProducto()
    {
        return $this->nombre;
    }

    public function setNombreProducto($nombre_producto)
    {
        if (!empty($nombre_producto)) {
            $this->nombre = $nombre_producto;
        } else {
            throw new Exception("El nombre del producto no puede estar vacio");
        }
    }

    public function getTipoProducto()
    {
        return $this->tipo;
    }

    public function setTipoProducto($tipo_producto)
    {
        if (!empty($tipo_producto)) {
            $this->tipo = $tipo_producto;
        } else {
            throw new Exception("El tipo del producto no puede estar vacio");
        }
    }

    public function getMarcaProducto()
    {
        return $this->marca;
    }

    public function setMarcaProducto($marca_producto)
    {
        if (!empty($marca_producto)) {
            $this->marca = $marca_producto;
        } else {
            throw new Exception("La marca del producto no puede estar vacio");
        }
    }

    public function getStockProducto()
    {
        return $this->stock;
    }

    public function setStockProducto($stock_producto)
    {
        if ($stock_producto > 0) {
            $this->stock = $stock_producto;
        } else {
            throw new Exception("El stock del producto debe ser positivo");
        }
    }

    public function getPrecioTotal()
    {
        return $this->precio_total;
    }

    public function setPrecioTotal($precio_total)
    {
        if ($precio_total > 0) {
            $this->precio_total = $precio_total;
        } else {
            throw new Exception("El precio total debe ser positivo");
        }
    }

    public function getFechaVenta()
    {
        return $this->fecha_venta;
    }

    public function setFechaVenta($fecha_venta)
    {
        if (!empty($fecha_venta)) {
            $this->fecha_venta = $fecha_venta;
        } else {
            throw new Exception("La fecha de venta no puede estar vacio");
        }
    }

    public function getNumeroPedido()
    {
        return $this->numero_pedido;
    }

    public function setNumeroPedido($numero_pedido)
    {
        if ($numero_pedido > 0) {
            $this->numero_pedido = $numero_pedido;
        } else {
            throw new Exception("El numero de pedido debe ser positivo");
        }
    }

    public function getImagen()
    {
        return $this->imagen;
    }

    public function setImagen($imagen)
    {
        if (!empty($imagen)) {
            $this->imagen = $imagen;
        } else {
            throw new Exception("La imagen no puede estar vacia");
        }
    }

    public static function create($data)
    {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("INSERT INTO vendedor (mail, nombre, tipo, marca, stock, precio_total, fecha_venta, numero_pedido, imagen) VALUES (:mail, :nombre, :tipo, :marca, :stock, :precio_total, :fecha_venta, :numero_pedido, :imagen)");
            $query->bindValue(':mail', $data->getMail(), PDO::PARAM_STR);
            $query->bindValue(':nombre', $data->getNombreProducto(), PDO::PARAM_STR);
            $query->bindValue(':tipo', $data->getTipoProducto(), PDO::PARAM_STR);
            $query->bindValue(':marca', $data->getMarcaProducto(), PDO::PARAM_STR);
            $query->bindValue(':stock', $data->getStockProducto(), PDO::PARAM_INT);
            $query->bindValue(':precio_total', $data->getPrecioTotal(), PDO::PARAM_INT);
            $query->bindValue(':fecha_venta', $data->getFechaVenta(), PDO::PARAM_STR);
            $query->bindValue(':numero_pedido', $data->getNumeroPedido(), PDO::PARAM_STR);
            $query->bindValue(':imagen', $data->getImagen(), PDO::PARAM_STR);
            
            $query->execute();

            return $db->obtenerUltimoId();
        } catch (Exception $e) {
            return ["Error" => "No se pudo crear el vendedor", "Exception" => $e->getMessage()];
        }
    }
    public static function read($id)
    {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("SELECT * FROM vendedor WHERE id = :id");
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();

            return $query->fetchObject('Vendedor');
        } catch (Exception $e) {
            return ["Error" => "No se pudo leer el vendedor", "Exception" => $e->getMessage()];
        }
    }
    public static function readAll()
    {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("SELECT * FROM vendedor");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ["Error" => "No se pudo leer los vendedores", "Exception" => $e->getMessage()];
        }
    }
    public static function update($data)
    {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("UPDATE vendedor SET mail = :mail, nombre = :nombre, tipo = :tipo, marca = :marca, stock = :stock WHERE id = :id");
            $query->bindValue(':id', $data->getId(), PDO::PARAM_INT);
            $query->bindValue(':mail', $data->getMail(), PDO::PARAM_STR);
            $query->bindValue(':nombre', $data->getNombreProducto(), PDO::PARAM_STR);
            $query->bindValue(':tipo', $data->getTipoProducto(), PDO::PARAM_STR);
            $query->bindValue(':marca', $data->getMarcaProducto(), PDO::PARAM_STR);
            $query->bindValue(':stock', $data->getStockProducto(), PDO::PARAM_INT);
            $query->execute();
            return $query->fetchObject('Vendedor');
        } catch (Exception $e) {
            return ["Error" => "No se pudo actualizar el vendedor", "Exception" => $e->getMessage()];
        }
    }
    public static function delete($id)
    {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("DELETE FROM vendedor WHERE id = :id");
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
        } catch (Exception $e) {
            return ["Error" => "No se pudo borrar el vendedor", "Exception" => $e->getMessage()];
        }
    }

    public static function productosVendidosPorFecha($fecha) {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("SELECT nombre, tipo, marca, SUM(stock) as total_vendido FROM vendedor WHERE DATE(fecha_venta) = :fecha");
            $query->bindParam(':fecha', $fecha, PDO::PARAM_STR);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ["Error" => "No se pudo leer los vendedores", "Exception" => $e->getMessage()];
        }
    }

    public static function ventasPorUsuario($mail){
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("SELECT * FROM vendedor WHERE mail = :mail");
            $query->bindParam(':mail', $mail, PDO::PARAM_STR);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ["Error" => "No se pudo leer los vendedores", "Exception" => $e->getMessage()];
        }
    }

    public static function ventasPorTipo(){
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("SELECT tipo, SUM(stock) AS total_vendido FROM vendedor GROUP BY tipo");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ["Error" => "No se pudo leer los vendedores", "Exception" => $e->getMessage()];
        }
    }

    public static function ventasEntreValores($min, $max) {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("SELECT * FROM vendedor WHERE precio_total BETWEEN :min AND :max");
            $query->bindParam(':min', $min, PDO::PARAM_INT);
            $query->bindParam(':max', $max, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ["Error" => "No se pudo leer los vendedores", "Exception" => $e->getMessage()];
        }
    }

    public static function ingresoPorFecha($fecha) 
    {
        try 
        {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("SELECT SUM(precio_total) AS total_ingresos FROM vendedor WHERE DATE(fecha) = :fecha");
            $query->bindValue(':fecha', $fecha, PDO::PARAM_STR);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        catch (Exception $e) 
        {
            return ["Error" => "No se pudo leer los vendedores", "Exception" => $e->getMessage()];
        }
    }

    public static function ingresosTotales() {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("SELECT SUM(precio_total) AS total_ingresos FROM vendedor");
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ["Error" => "No se pudo leer los vendedores", "Exception" => $e->getMessage()];
        }
    }

    public static function productosMasVendidos() {
        try {
            $db = AccesoDatos::obtenerInstancia();
            $query = $db->prepararConsulta("SELECT nombre, tipo, marca, SUM(stock) AS total_vendido FROM vendedor GROUP BY nombre, tipo, marca ORDER BY total_vendido DESC LIMIT 1");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ["Error" => "No se pudo leer los vendedores", "Exception" => $e->getMessage()];
        }
    }
}
