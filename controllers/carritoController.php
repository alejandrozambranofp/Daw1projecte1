<?php
// Importamos los DAOs para consultar el precio real del producto y de los ingredientes en la BBDD
require_once "models/ProductoDAO.php";
require_once "models/IngredienteDAO.php";

class carritoController
{
    /**
     * Muestra la vista del carrito (?controller=carrito&action=index)
     */
    public function index()
    {
        // Si todavía no existe el array del carrito en la sesión, lo inicializamos vacío
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        // Cargamos la vista del carrito dentro de nuestra plantilla maestra main.php
        $view = "views/carrito/index.php";
        include_once "views/main.php";
    }

    /**
     * Recibe por POST el formulario de show.php (?controller=carrito&action=add)
     * Calcula el precio real en el servidor y añade el plato personalizado a $_SESSION['carrito']
     */
    public function add()
    {
        // Solo aceptamos peticiones que vengan del formulario por POST con un id_producto válido
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_producto'])) {
            $idProducto = (int) $_POST['id_producto'];
            // Recogemos el array de ingredientes [id_ingrediente => cantidad] enviado desde show.php
            $ingredientesEnviados = $_POST['ingredientes'] ?? [];

            // 1. Buscamos el producto real en la base de datos (NUNCA confiamos en precios enviados desde HTML)
            $producto = ProductoDAO::getById($idProducto);

            if ($producto) {
                // Empezamos el cálculo del precio con el precio base oficial del plato
                $precioTotalPlato = (float) $producto->getPrecio();

                // Obtenemos qué ingredientes venían incluidos de serie en este plato (para no cobrarlos si cantidad == 1)
                $ingredientesBase = IngredienteDAO::getIdsPorProducto($idProducto);

                // Obtenemos todos los ingredientes activos para conocer sus nombres y precios extra oficiales
                $todosLosIngredientes = IngredienteDAO::getAllActivos();

                // Array donde guardaremos el resumen legible de ingredientes para mostrar en el carrito
                $detalleIngredientes = [];

                // 2. Recorremos todos los ingredientes de la posada para ver qué cantidad eligió el usuario
                foreach ($todosLosIngredientes as $ing) {
                    $idIng = (int) $ing->getId();
                    // Si el usuario envió una cantidad para este ingrediente la leemos; si no, es 0
                    $cantidadElegida = isset($ingredientesEnviados[$idIng]) ? (int) $ingredientesEnviados[$idIng] : 0;

                    // Evitamos valores negativos manipulados desde el navegador
                    if ($cantidadElegida < 0) $cantidadElegida = 0;

                    // Comprobamos si este ingrediente venía incluido por defecto (1 unidad gratis) o es extra (0 gratis)
                    $cantidadGratis = in_array($idIng, $ingredientesBase) ? 1 : 0;

                    // Calculamos cuántas unidades hay que cobrar como extra
                    $unidadesCobrables = max(0, $cantidadElegida - $cantidadGratis);
                    $precioTotalPlato += $unidadesCobrables * (float) $ing->getPrecioExtra();

                    // Guardamos el detalle si el plato lleva al menos 1 unidad de este ingrediente
                    if ($cantidadElegida > 0) {
                        $detalleIngredientes[] = [
                            "id"       => $idIng,
                            "nombre"   => $ing->getNombre(),
                            "cantidad" => $cantidadElegida
                        ];
                    }
                }

                // 3. Inicializamos el carrito en sesión si es el primer producto que añade el cliente
                if (!isset($_SESSION['carrito'])) {
                    $_SESSION['carrito'] = [];
                }

                // 4. Añadimos la nueva línea al carrito de la sesión
                $_SESSION['carrito'][] = [
                    "id_producto"     => $idProducto,
                    "nombre"          => $producto->getNombre(),
                    "imagen"          => $producto->getImagen(),
                    "precio_unitario" => $precioTotalPlato,
                    "cantidad"        => 1,
                    "ingredientes"    => $detalleIngredientes
                ];
            }
        }

        // Redirigimos al usuario a la vista del carrito para que vea su plato añadido (Patrón PRG: Post-Redirect-Get)
        header("Location: index.php?controller=carrito&action=index");
        exit();
    }

    /**
     * Elimina una línea concreta del carrito según su índice (?controller=carrito&action=remove&index=0)
     */
    public function remove()
    {
        if (isset($_GET['index']) && isset($_SESSION['carrito'][$_GET['index']])) {
            // unset() elimina esa posición del array de la sesión
            unset($_SESSION['carrito'][$_GET['index']]);
            // array_values() reordena los índices (0, 1, 2...) para no dejar huecos vacíos en el array
            $_SESSION['carrito'] = array_values($_SESSION['carrito']);
        }

        header("Location: index.php?controller=carrito&action=index");
        exit();
    }

    /**
     * Vacía por completo el carrito (?controller=carrito&action=deleteAll)
     */
    public function deleteAll()
    {
        unset($_SESSION['carrito']);
        header("Location: index.php?controller=carrito&action=index");
        exit();
    }

    /**
     * Simula la finalización de la compra (?controller=carrito&action=confirmar)
     * Guarda el resumen del pedido para mostrar el recibo y vacía el carrito activo
     */
    public function confirmar()
    {
        // Si el carrito está vacío, redirigimos al carrito para evitar entrar a la pantalla de éxito sin comprar
        if (empty($_SESSION['carrito'])) {
            header("Location: index.php?controller=carrito&action=index");
            exit();
        }

        // Calculamos el total final del pedido
        $totalPedido = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $totalPedido += $item['precio_unitario'] * $item['cantidad'];
        }

        // Guardamos los datos del ticket en una variable local para pasársela a la vista
        $resumenPedido = [
            "numero" => "HYR-" . rand(1000, 9999), // Código de ticket simulado
            "fecha"  => date("d/m/Y H:i"),
            "items"  => $_SESSION['carrito'],
            "total"  => $totalPedido
        ];

        // Vaciamos el carrito porque la compra ya se ha completado
        unset($_SESSION['carrito']);

        // Cargamos la vista de confirmación dentro de la plantilla maestra
        $view = "views/carrito/confirmado.php";
        include_once "views/main.php";
    }
}