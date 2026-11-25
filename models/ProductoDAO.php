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

    // Método fábrica: construye el objeto de la subclase correcta (Carnes, Bebida...) a partir de los datos del JSON
    public static function crearInstancia($id, $cat_id, $nombre, $descripcion, $precio, $imagen, $activo)
    {
        require_once "models/Producto.php";

        $nombreClase = self::$mapaClases[$cat_id] ?? 'Especiales';
        $rutaSubclase = "models/" . $nombreClase . ".php";

        if (file_exists($rutaSubclase)) {
            require_once $rutaSubclase;
        }

        return new $nombreClase($id, $cat_id, $nombre, $descripcion, $precio, $imagen, $activo);
    }

    public static function getAll()
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM productos ORDER BY activo DESC, id ASC");
        $stmt->execute();
        $resultado = $stmt->get_result();

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

    // POST: guarda un producto nuevo y devuelve true/false para que la API responda al fetch
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
        $ok = $stmt->execute();

        $stmt->close();
        $con->close();
        return $ok;
    }

    // DELETE: borrado físico con try/catch por si el plato tiene pedidos asociados
    public static function destroy($id)
    {
        $con = DataBase::connect();
        try {
            $stmt = $con->prepare("DELETE FROM productos WHERE id = ?");
            $stmt->bind_param("i", $id);
            $ok = $stmt->execute();
            $stmt->close();
            $con->close();
            return $ok;
        } catch (mysqli_sql_exception $e) {
            $con->close();
            return false;
        }
    }

    // PUT: actualiza todos los datos de un producto existente y devuelve true/false
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
        $ok = $stmt->execute();

        $stmt->close();
        $con->close();
        return $ok;
    }

    // PATCH: cambia el estado activo (1) o inactivo (0) sin borrar el registro
    public static function cambiarEstado($id, $activo)
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("UPDATE productos SET activo = ? WHERE id = ?");
        $stmt->bind_param("ii", $activo, $id);
        $ok = $stmt->execute();

        $stmt->close();
        $con->close();
        return $ok;
    }
}