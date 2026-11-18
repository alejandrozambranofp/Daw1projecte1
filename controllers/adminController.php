<?php
require_once "models/ProductoDAO.php";
require_once "models/IngredienteDAO.php";

class adminController
{
    public function index()
    {
        // Descomenta cuando tengas el login listo
        /*
        if (!isset($_SESSION['identity']) || $_SESSION['identity']->rol !== 'admin') {
            header("Location: index.php");
            exit();
        }
        */

        $view = "views/admin/admin.php";
        include_once "views/main.php";
    }

    public function api()
    {
        $metodo = $_SERVER['REQUEST_METHOD'];

        if ($metodo == "GET") {
            $funcion = $_GET['function'] ?? '';

            if ($funcion == "ingredientes") {
                $ingredientes = IngredienteDAO::getAllActivos();
                echo json_encode($ingredientes);
            } elseif ($funcion == "productos") {
                $productos = ProductoDAO::getAll(); // Cámbialo si tu método en ProductoDAO se llama distinto
                echo json_encode($productos);
            }
        } elseif ($metodo == "POST") {
            // Leemos el cuerpo JSON enviado por fetch()
            $datos = json_decode(file_get_contents("php://input"), true);
            $funcion = $_GET['function'] ?? '';

            if ($funcion == "crearIngrediente") {
                $nombre = trim($datos['nombre'] ?? '');
                $precio = (float) ($datos['precio_extra'] ?? 0);

                if ($nombre !== '') {
                    $ok = IngredienteDAO::create($nombre, $precio);
                    echo json_encode(["success" => $ok, "mensaje" => "Ingrediente añadido a la posada"]);
                } else {
                    echo json_encode(["success" => false, "mensaje" => "El nombre es obligatorio"]);
                }
            } elseif ($funcion == "eliminarIngrediente") {
                $id = (int) ($datos['id'] ?? 0);
                $ok = IngredienteDAO::delete($id);
                echo json_encode(["success" => $ok]);
            }
        }
        exit();
    }
}