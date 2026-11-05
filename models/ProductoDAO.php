<?php

class productoDAO
{
    public static function getAll()
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM productos");
        $stmt->execute();
        $result = $stmt->get_result();

        $lista = [];
        while ($receta = $result->fetch_object("Receta")) {
            $lista[] = $receta;
        }

        $stmt->close();
        $con->close();

        return $lista;
    }

    public static function getById($id)
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $producto = $result->fetch_object("receta");

        $stmt->close();
        $con->close();

        return $producto ?: null;
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
