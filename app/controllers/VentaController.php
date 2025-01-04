<?php
require_once './models/Vendedor.php';
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class VentaController extends Vendedor  implements IApiUsable {
    public function CargarUno($request, $response, $args){
        $params = $request->getParsedBody();

        $mail = $params['mail'];
        $nombre = $params['nombre'];
        $tipo = $params['tipo'];
        $marca = $params['marca'];
        $stock = $params['stock'];

        $producto = Producto::findByNombreAndMarcaAndTipo($nombre, $marca, $tipo);

        if($producto && $producto->getStock() >= $stock){
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

            Vendedor::create($venta);

            $nuevoProducto = $producto->getStock() - $stock;
            $producto->setStock($nuevoProducto);
            Producto::update($producto);

            $playload = json_encode(["mensaje" => "Venta realizada con exito"]);
        }
        else {
            $playload = json_encode(["mensaje" => "Producto no enctrado o no hay stock suficiente"]);
        }

        $response->getBody()->write($playload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function TraerUno($request, $response, $args){
        
    }
	public function TraerTodos($request, $response, $args){}
	public function BorrarUno($request, $response, $args){}
	public function ModificarUno($request, $response, $args){}
}