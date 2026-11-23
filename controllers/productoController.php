<?php

//utilizo require once para que si falla algo no siga a ciegas sino que pare la web
//en archivos como el header o el footer se pone include por que asi se seguiran mostrando aunq de error
//dando la posibilidad a que el usuario pueda seguir navegando
require_once "models/ProductoDAO.php";
require_once "models/IngredienteDAO.php";

class productoController
{
    public function api(){
        $metodo = $_SERVER['REQUEST_METHOD'];
        if($metodo == "GET"){
            if($_GET['function'] == "todos"){
                //devuelvo todos los productos
            }
        }else if($metodo = "POST"){

        }
        echo json_encode($metodo);
    }

    public function index()
    {
        $productos = ProductoDAO::getAll();
        $view = "views/productos/carta.php";
        include_once 'views/main.php';
    }

    public function verCarta(){
        $productos = ProductoDAO::getAll();
        $view = "views/productos/carta.php";
        include_once 'views/main.php';
    }

    public function create()
    {
        $view = "views/productos/create.php";
        include_once 'views/main.php';
    }

    public function store()
    {
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $talla = isset($_POST['talla']) ? $_POST['talla'] : '';
        $precio = isset($_POST['precio']) ? $_POST['precio'] : '';

        if ($nombre === '' || !in_array($talla, self::TALLAS_VALIDAS, true) || !is_numeric($precio) || $precio < 0) {
            header('Location:?controller=producto&action=create');
            exit;
        }

        $producto = new Camiseta($nombre, $talla, (float) $precio);
        CamisetaDAO::store($producto);

        header('Location:?controller=producto');
        exit;
    }

    public function destroy()
    {
        if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
            header('Location:?controller=producto');
            exit;
        }

        CamisetaDAO::destroy((int) $_GET['id']);
        header('Location:?controller=producto');
        exit;
    }

    public function show()
    {
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];

            $producto = ProductoDAO::getById($id);
            $todosLosIngredientes = IngredienteDAO::getAllActivos();
            $ingredientesDelPlato = IngredienteDAO::getIdsPorProducto($id);

            $view = "views/productos/show.php";
            include_once "views/main.php";
        } else {
            header("Location: index.php");
        }
    }
}
