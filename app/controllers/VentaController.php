<?php
require_once './models/Vendedor.php';
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class VentaController extends Vendedor  implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $file = $request->getUploadedFiles();

        $mail = $params['mail'];
        $nombre = $params['nombre'];
        $tipo = $params['tipo'];
        $marca = $params['marca'];
        $stock = $params['stock'];
        $imagen = $file['imagen'] ?? null;

        $producto = Producto::findByNombreAndMarcaAndTipo($nombre, $marca, $tipo);

        if ($producto && $producto->getStock() >= $stock) {
            $precioTotal = $producto->getPrecio() * $stock;

            $venta = new Vendedor();

            $venta->setMail($mail);
            $venta->setNombreProducto($nombre);
            $venta->setTipoProducto($tipo);
            $venta->setMarcaProducto($marca);
            $venta->setStockProducto($stock);
            $venta->setPrecioTotal($precioTotal);
            $venta->setFechaVenta(date("Y-m-d H:i:s"));
            $venta->setNumeroPedido(uniqid());

            if ($imagen) {
                $emailUsario = explode("@", $mail)[0];
                $fechaVenta = date("Ymd_His");
                $nombreImagen = "{$nombre}_{$tipo}_{$marca}_{$emailUsario}_{$fechaVenta}.jpg";
                $rutaImagen = "../public/ImagenesDeVenta/2024{$nombreImagen}";
                $imagen->moveTo($rutaImagen);
                $venta->setImagen($rutaImagen);
            } else {
                $venta->setImagen(null);
            }

            Vendedor::create($venta);

            $nuevoProducto = $producto->getStock() - $stock;
            $producto->setStock($nuevoProducto);
            Producto::update($producto);

            $playload = json_encode(["mensaje" => "Venta realizada con exito"]);
        } else {
            $playload = json_encode(["mensaje" => "Producto no enctrado o no hay stock suficiente"]);
        }

        $response->getBody()->write($playload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $vender = Vendedor::read($id);
        if ($vender) {
            $playload = json_encode(["mensaje" => "Venta encontrada", "venta" => $vender]);
        } else {
            $playload = json_encode(["mensaje" => "Venta no encontrada"]);
        }
        $response->getBody()->write($playload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $ventas = Vendedor::readAll();
        $playload = json_encode(["mensaje" => "Ventas encontradas", "ventas" => $ventas]);
        $response->getBody()->write($playload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];
        $venta = Vendedor::read($id);
        if ($venta) {
            Vendedor::delete($id);
            $playload = json_encode(["mensaje" => "Venta eliminada", "venta" => $venta]);
        } else {
            $playload = json_encode(["mensaje" => "Venta no encontrada"]);
        }
        $response->getBody()->write($playload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function ModificarUno($request, $response, $args)
    {
        $id = $args['id'];
        $params = $request->getParsedBody();
        $venta = Vendedor::read($id);
        if ($venta) {
            $venta->setMail($params['mail']);
            $venta->setNombreProducto($params['nombre']);
            $venta->setTipoProducto($params['tipo']);
            $venta->setMarcaProducto($params['marca']);
            $venta->setStockProducto($params['stock']);
            $venta->setPrecioTotal($params['precio']);
            Vendedor::update($venta);
            $playload = json_encode(["mensaje" => "Venta modificada", "venta" => $venta]);
        } else {
            $playload = json_encode(["mensaje" => "Venta no encontrada"]);
        }
        $response->getBody()->write($playload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function trearProductosVendidos($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $fecha = $params['fecha'] ?? date('Y-m-d', strtotime('-1 day'));

        $cantidadVendidas = Vendedor::cantidadVendidaPorFecha($fecha);

        $playload = json_encode(["mensaje" => "Cantidad de productos vendidos", "cantidad" => $cantidadVendidas]);
        $response->getBody()->write($playload);
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function traerVentasPorUsuario($request, $response, $args) 
    {
        $mail = $args['mail'];
        $ventas = Vendedor::ventasPorUsuario($mail);
        if($ventas) {
            $playload = json_encode(["mensaje" => "Ventas encontradas", "ventas" => $ventas]);
        } else {
            $playload = json_encode(["mensaje" => "Ventas no encontradas"]);
        }

        $response->getBody()->write($playload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function traerVentasPorProducto($request, $response, $args)
    {
        $tipo = $args['tipo'];
        $ventas = Vendedor::ventasPorTipo($tipo);
        
        if($ventas) {
            $playload = json_encode(["mensaje" => "Ventas encontradas", "ventas" => $ventas]);
        } else {
            $playload = json_encode(["mensaje" => "Ventas no encontradas"]);
        }

        $response->getBody()->write($playload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function traerProductosEntreValores($request, $response, $args)
    {
        $min = $args['min'];
        $max = $args['max'];

        $productos = Vendedor::ventasEntreValores($min, $max);

        if($productos) {
            $playload = json_encode(["mensaje" => "Productos encontrados", "productos" => $productos]);
        } else {
            $playload = json_encode(["mensaje" => "Productos no encontrados"]);
        }

        $response->getBody()->write($playload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function Ingreso($request, $response, $args) {
        $params = $request->getParsedBody();
        $fecha = $params['fecha'] ?? null;

        if($fecha) 
        {
            $ingreso = Vendedor::ingresoPorFecha($fecha);
        }
        else {
            $ingreso = Vendedor::ingresosTotales();
        }

        $playload = json_encode(["mensaje" => "Ingresos", "ingresos" => $ingreso]);

        $response->getBody()->write($playload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function traerProductosMasVendido($reques, $response, $args) {
        $producto = Vendedor::productosMasVendidos();
        $playload = json_encode(["mensaje" => "Producto mas vendido", "producto" => $producto]);
        $response->getBody()->write($playload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
