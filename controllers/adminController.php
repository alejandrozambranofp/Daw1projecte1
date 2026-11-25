<?php
// Importamos el DAO de productos para poder leer platos desde MySQL
require_once "models/productoDAO.php";
// Importamos el DAO de ingredientes para leer, crear y borrar ingredientes en MySQL
require_once "models/IngredienteDAO.php";

// Definimos la clase controladora del panel de administración
class adminController
{
    // Método clásico: carga la página HTML del panel cuando entras por el navegador
    public function index()
    {
        //aqui dejamos claro que no es peticion json y que se esta haciendo por url
        //ya que si fuera json aun que el usuario no sea admin podria recibir datos a traves de un fetch en consola
        //redirigiremos a login en caso de que no sea admin
        $this->verificarAdmin(false);
        // Guardamos la ruta de la vista del panel de administración
        $view = "views/admin/admin.php";
        // Cargamos la plantilla principal (cabecera + vista + pie de página)
        include_once "views/main.php";
    }

    // Método API REST: no devuelve HTML, sino datos puros en JSON para JavaScript (fetch)
    public function api()
    {
        //En el caso de que nos envie una peticion json pondremos true para pasar a la parte donde hemos
        //puesto lo que pasara si la peticion es json que seria un error 403
        $this->verificarAdmin(true);
        // Le avisamos al navegador que la respuesta será JSON en UTF-8 (para tildes y ñ)
        header('Content-Type: application/json; charset=utf-8');
        // Detectamos si JavaScript nos llama por GET (pedir datos) o por POST (guardar/borrar)
        $metodo = $_SERVER['REQUEST_METHOD'];

        // Si la petición es GET (JavaScript pide listas para rellenar las tablas)
        if ($metodo === "GET") {
            // Leemos qué lista pide la URL (?function=ingredientes o ?function=productos)
            $funcion = $_GET['function'] ?? '';

            // Si JavaScript pide los ingredientes activos
            if ($funcion === "ingredientes") {
                // Pedimos los ingredientes a MySQL y los imprimimos convertidos a texto JSON
                echo json_encode(IngredienteDAO::getAllActivos());
            // Si JavaScript pide todos los productos
            } elseif ($funcion === "productos") {
                // Pedimos los productos a MySQL y los imprimimos convertidos a texto JSON
                echo json_encode(productoDAO::getAll());
            }

        // Si la petición es POST (JavaScript envía datos nuevos para guardar o borrar)
        } elseif ($metodo === "POST") {
            // Leemos el paquete JSON crudo que envía fetch() y lo convertimos a array de PHP
            $datos = json_decode(file_get_contents("php://input"), true);
            // Leemos qué acción pide la URL (?function=crearIngrediente o eliminarIngrediente)
            $funcion = $_GET['function'] ?? '';

            // Si JavaScript pide crear un ingrediente nuevo
            if ($funcion === "crearIngrediente") {
                // Sacamos el nombre del JSON y le quitamos espacios sobrantes a los lados
                $nombre = trim($datos['nombre'] ?? '');
                // Sacamos el precio extra del JSON y lo forzamos a número decimal
                $precio = (float) ($datos['precio_extra'] ?? 0);

                // Comprobamos que el nombre no venga vacío
                if ($nombre !== '') {
                    // Llamamos al DAO para hacer el INSERT en MySQL (devuelve true o false)
                    $ok = IngredienteDAO::create($nombre, $precio);
                    // Respondemos a JavaScript en formato JSON si se guardó bien y el mensaje
                    echo json_encode([
                        "success" => $ok,
                        "mensaje" => $ok ? "Ingrediente añadido a la BBDD" : "Error al guardar en BBDD"
                    ]);
                } else {
                    // Si el nombre estaba vacío, devolvemos error en JSON sin tocar MySQL
                    echo json_encode(["success" => false, "mensaje" => "El nombre es obligatorio"]);
                }

            // Si JavaScript pide eliminar (ocultar) un ingrediente
            } elseif ($funcion === "eliminarIngrediente") {
                $id = (int) ($datos['id'] ?? 0);
                $ok = IngredienteDAO::delete($id);
                echo json_encode(["success" => $ok]);
            }
        }

        // Cortamos la ejecución de PHP aquí mismo para que no se imprima HTML debajo del JSON
        exit();
    }

    private function verificarAdmin($responderConJson = false)
    {
        // 1. Comprobamos si el usuario ha iniciado sesión Y su rol es 'admin'
        $esAdmin = isset($_SESSION['usuario']) && ($_SESSION['usuario']['rol'] ?? '') === 'admin';

        // 2. Si NO cumple ambas cosas, bloqueamos el paso:
        if (!$esAdmin) {
            if ($responderConJson) {
                // Si venimos de api(), respondemos en formato JSON para JavaScript
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(403);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Acceso denegado: se requieren permisos de administrador."
                ]);
            } else {
                // Si venimos de index(), redirigimos al formulario de login
                header("Location: index.php?controller=usuario&action=login");
            }
            exit();
        }
    }
}