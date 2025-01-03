<?php
require_once './interfaces/IApiUsable.php';
require_once './models/Producto.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        $nombre = $params['nombre'];
        $tipo = $params['tipo'];
        $marca = $params['marca'];
        $stock = (int)$params['stock'];
        $precio = (float)$params['precio'];
        $imagen = $files['imagen'] ?? null;

        if (!in_array($tipo, ['Smartphone', 'Tablet'])) {
            $playload = json_encode(["mensaje" => "Tipo invalido. Solo 'Smartphone' y 'Tablet' son validos."]);
            $response->getBody()->write($playload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        $productoExistente = Producto::findByMarcaAndTipo($marca, $tipo);

        if ($productoExistente) {
            $nuevoStock = $productoExistente->getStock() + $stock;
            $productoExistente->setStock($nuevoStock);
            $productoExistente->setPrecio($precio);
            Producto::update($productoExistente);

            if ($imagen) {
                $nombreImagen = "{$nombre}_{$tipo}.jpg";
                $rutaImagen = "../public/ImagenesDeProductos/2024/{$nombreImagen}";
                $imagen->moveTo($rutaImagen);
                $productoExistente->setImagen($rutaImagen);
            } else {
                $productoExistente->setImagen(null);
            }

            $playload = json_encode(["mensaje" => "Producto actualizado", "producto" => $productoExistente]);
        } else {
            $producto = new Producto();
            $producto->setNombre($nombre);
            $producto->setTipo($tipo);
            $producto->setMarca($marca);
            $producto->setStock($stock);
            $producto->setPrecio($precio);

            if ($imagen) {
                $nombreImagen = "{$nombre}_{$tipo}.jpg";
                $rutaImagen = "../public/ImagenesDeProductos/2024/{$nombreImagen}";
                $imagen->moveTo($rutaImagen);
                $producto->setImagen($rutaImagen);
            } else {
                $producto->setImagen(null);
            }
            Producto::create($producto);

            $playload = json_encode(["mensaje" => "Producto creado", "producto" => $producto]);
        }

        $response->getBody()->write($playload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args) {}
    public function ModificarUno($request, $response, $args) {}

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $producto = Producto::read($id);

        if ($producto) {
            $playload = json_encode(["mensaje" => "Producto encontrado", "producto" => $producto]);
        } else {
            $playload = json_encode(["mensaje" => "Producto no encontrado"]);
        }

        $response->getBody()->write($playload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function TraerTodos($request, $response, $args)
    {
        $listado = Producto::readAll();
        $playload = json_encode(["Listado de productos" => $listado]);
        $response->getBody()->write($playload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUnoPorNombreMarcaTipo($request, $response, $args)
    {
        $params = $request->getParsedBody();

        $nombre = $params['nombre'];
        $marca = $params['marca'];
        $tipo = $params['tipo'];

        $producto = Producto::findByNombreAndMarcaAndTipo($nombre, $marca, $tipo);
        if ($producto) {
            $playload = json_encode(["mensaje" => "Producto encontrado", "producto" => $producto]);
        } else {
            $marcaExiste = Producto::findByMarca($marca);
            $tipoExiste = Producto::findByTipo($tipo);

            if ($marcaExiste) {
                $playload = json_encode(["mensaje" => "No hay productos de la marca $marca"]);
            } elseif ($tipoExiste) {
                $playload = json_encode(["mensaje" => "No hay productos del tipo $tipo"]);
            } else {
                $playload = json_encode(["mensaje" => "Producto no encontrado"]);
            }
        }

        $response->getBody()->write($playload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
