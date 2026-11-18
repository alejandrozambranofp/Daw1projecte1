<?php

class productoDAO
{

    private static $mapaClases = [
        1 => 'Carnes',
        2 => 'Bebida',
        3 => 'Arroces',
        4 => 'Empanados',
        5 => 'Especiales'
    ];

    public static function getAll()
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM productos");
        $stmt->execute();
        $resultado = $stmt->get_result();

        $producto = null;

        $listaProductos = [];

        require_once "models/Producto.php"; 

        while($fila = $resultado->fetch_assoc()){
            $cat_id = $fila['categoria_id'];
            $nombreClase = self::$mapaClases[$cat_id];
        

            $rutaSubclase = "models/" . $nombreClase . ".php";
            if(file_exists($rutaSubclase)){
                require_once $rutaSubclase;
            }

            $producto = new $nombreClase(
                $fila['id'],
                $fila['categoria_id'],
                $fila['nombre'],
                $fila['descripcion'],
                $fila['precio'],
                $fila['imagen'],
                $fila['activo']
            );

            $listaProductos[] = $producto;
        }
        return $listaProductos;
    }

    public static function getById($id)
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM productos where id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $producto = null;

        $listaProductos = [];

        require_once "models/Producto.php"; 

        if($fila = $resultado->fetch_assoc()){
            $cat_id = $fila['categoria_id'];
            $nombreClase = self::$mapaClases[$cat_id];
        

            $rutaSubclase = "models/" . $nombreClase . ".php";
            if(file_exists($rutaSubclase)){
                require_once $rutaSubclase;
            }

            $producto = new $nombreClase(
                $fila['id'],
                $fila['categoria_id'],
                $fila['nombre'],
                $fila['descripcion'],
                $fila['precio'],
                $fila['imagen'],
                $fila['activo']
            );
        }

        return $producto;
        
    }

    public static function store($producto)
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("INSERT INTO productos (categoria_id, nombre, descripcion, precio, imagen, activo) VALUES (?, ?, ?, ?, ?, ?)");

        $categoria_id = $producto->getCategoriaId();
        $nombre       = $producto->getNombre();
        $descripcion  = $producto->getDescripcion();
        $precio       = $producto->getPrecio();
        $imagen       = $producto->getImagen();
        $activo       = $producto->getActivo();

        $stmt->bind_param("issdsi", $categoria_id, $nombre, $descripcion, $precio, $imagen, $activo);
        $stmt->execute();

        $stmt->close();
        $con->close();
    }

    public static function destroy($id)
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt->close();
        $con->close();
    }

    public static function update($producto)
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("UPDATE productos SET categoria_id = ?, nombre = ?, descripcion = ?, precio = ?, imagen = ?, activo = ? WHERE id = ?");

        $categoria_id = $producto->getCategoriaId();
        $nombre       = $producto->getNombre();
        $descripcion  = $producto->getDescripcion();
        $precio       = $producto->getPrecio();
        $imagen       = $producto->getImagen();
        $activo       = $producto->getActivo();
        $id           = $producto->getId();

        $stmt->bind_param("issdsii", $categoria_id, $nombre, $descripcion, $precio, $imagen, $activo, $id);
        $stmt->execute();

        $stmt->close();
        $con->close();
    }
}
