<?php

include_once("models/ProductoDAO.php");
include_once("models/Producto.php");

class productoController
{
    public function index()
    {
        $productos = ProductoDAO::getAll();
        $view = "views/productos/listado.php";
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
        if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
            header('Location:?controller=producto');
            exit;
        }

        $producto = ProductoDAO::getById((int) $_GET['id']);
        if ($producto === null) {
            header('Location:?controller=producto');
            exit;
        }

        $view = "views/productos/show.php";
        include_once 'views/main.php';
    }
}
