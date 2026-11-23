<?php
//importamos Ingrediente.php para que dao tenga el objeto que rellenar
require_once "models/Ingrediente.php";

class IngredienteDAO{

    public static function getAllActivos()
    {
        $con = DataBase::connect();
        //Esta consulta es para obtener todos los ingredientes que esten activos ordenados alfabeticamente
        $stmt = $con->prepare("SELECT * FROM ingredientes WHERE activo = 1 ORDER BY nombre ASC");
        $stmt->execute();
        //guardamos en resultado lo que devuelve la consulta
        $resultado = $stmt->get_result();
        //creamos el array donde guardaremos los datos
        $ingredientes = [];
        //este bucle pasa por cada linea de la consulta con fetch y guarda en un objeto(ingrediente) con new Ingrediente los datos de cada linea
        //se hace con assoc por que sino con object creariamos un objeto(un ingrediente) sin las seguridad que tenemos con Ingrediente.php
        //de que una vista pueda cambiar el contenido para siempre en la sesion
        while ($fila = $resultado->fetch_assoc()) {
            $ingredientes[] = new Ingrediente(
                //guardamos cada dato de cada campo en este objeto para cada fila
                $fila['id'],
                $fila['nombre'],
                $fila['precio_extra'],
                $fila['activo']
            );
        }
        //devolveremos este objeto que es una array con todos los ingredientes que podremos utilizar en el controlador
        return $ingredientes;
    }


    //Esta funcion es necesaria para que cuando un usuario entre a algun producto en show se muestren
    //los ingredientes correspondientes ya marcados, para poder quitarlos en caso que no los desee
    public static function getIdsPorProducto($productoId)
    {
        $con = DataBase::connect();
        //pediremos saber que id de ingredientes son los que ese producto tiene que tener marcado
        //por que en la tabla producto_ingrediente se asocia el ingrediente_id con el producto_id
        $stmt = $con->prepare("SELECT ingrediente_id FROM producto_ingrediente WHERE producto_id = ?");
        $stmt->bind_param("i", $productoId);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $ids = [];
        while ($fila = $resultado->fetch_assoc()) {
            $ids[] = (int) $fila['ingrediente_id'];
        }
        //devolvemos las id al controlador para que le diga a la vista cuales ingredientes marcar
        return $ids;
    }

    public static function create($nombre, $precio_extra)
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("INSERT INTO ingredientes (nombre, precio_extra, activo) VALUES (?, ?, 1)");
        $stmt->bind_param("sd", $nombre, $precio_extra);
        $ok = $stmt->execute();
        $stmt->close();
        $con->close();
        return $ok;
    }

    public static function delete($id)
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("UPDATE ingredientes SET activo = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        $con->close();
        return $ok;
    }

}