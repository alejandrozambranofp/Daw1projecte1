<?php
// Cargamos los DAOs necesarios para interactuar con las tablas de la BBDD
require_once "models/productoDAO.php";
require_once "models/IngredienteDAO.php";
require_once "models/UsuarioDAO.php";
require_once "models/PedidoDAO.php";

class adminController
{
    // Carga la vista principal del panel de administración
    public function index()
    {
        // Si no es admin, lo mandamos al login
        $this->verificarAdmin(false);
        $view = "views/admin/admin.php";
        include_once "views/main.php";
    }

    // Punto de entrada de la API REST para todas las peticiones fetch del panel
    public function api()
    {
        // Comprobamos permisos devolviendo JSON si no está autorizado
        $this->verificarAdmin(true);
        header('Content-Type: application/json; charset=utf-8');

        // Recogemos el verbo HTTP, el parámetro 'function' de la URL y el JSON del body
        $metodo  = $_SERVER['REQUEST_METHOD'];
        $funcion = $_GET['function'] ?? '';
        $datos   = json_decode(file_get_contents("php://input"), true) ?? [];

        // Derivamos cada petición a su método privado según el tipo de operación
        switch ($metodo) {
            case 'GET':
                $this->manejarGet($funcion);
                break;

            case 'POST':
                $this->manejarPost($funcion, $datos);
                break;

            case 'PUT':
                $this->manejarPut($funcion, $datos);
                break;

            case 'PATCH':
                $this->manejarPatch($funcion, $datos);
                break;

            case 'DELETE':
                $this->manejarDelete($funcion, $datos);
                break;

            default:
                $this->responderJson(["success" => false, "mensaje" => "Método HTTP no permitido"], 405);
        }

        // Cortamos la ejecución para que no se cuele HTML extra después del JSON
        exit();
    }

    // =========================================================================
    // ENRUTADORES POR VERBO HTTP
    // =========================================================================

    // Gestiona todas las consultas de lectura (rellenar las tablas del panel)
    private function manejarGet(string $funcion): void
    {
        switch ($funcion) {
            case 'ingredientes':
                $this->responderJson(IngredienteDAO::getAll());
                break;
            case 'productos':
                $this->responderJson(productoDAO::getAll());
                break;
            case 'usuarios':
                $this->responderJson(UsuarioDAO::getAll());
                break;
            case 'pedidos':
                $this->responderJson(PedidoDAO::getAllConDetalles());
                break;
            default:
                $this->responderJson(["success" => false, "mensaje" => "Endpoint GET no válido"], 400);
        }
    }

    // Gestiona las altas de nuevos registros
    private function manejarPost(string $funcion, array $datos): void
    {
        switch ($funcion) {
            case 'crearIngrediente':
                $this->crearIngrediente($datos);
                break;
            case 'crearProducto':
                $this->crearProducto($datos);
                break;
            case 'crearUsuario':
                $this->crearUsuario($datos);
                break;
            default:
                $this->responderJson(["success" => false, "mensaje" => "Endpoint POST no válido"], 400);
        }
    }

    // Gestiona la edición completa de registros existentes
    private function manejarPut(string $funcion, array $datos): void
    {
        switch ($funcion) {
            case 'editarIngrediente':
                $this->editarIngrediente($datos);
                break;
            case 'editarProducto':
                $this->editarProducto($datos);
                break;
            case 'editarUsuario':
                $this->editarUsuario($datos);
                break;
            default:
                $this->responderJson(["success" => false, "mensaje" => "Endpoint PUT no válido"], 400);
        }
    }

    // Gestiona cambios parciales rápidos (activar/desactivar o cambiar estado del pedido)
    private function manejarPatch(string $funcion, array $datos): void
    {
        switch ($funcion) {
            case 'cambiarEstadoIngrediente':
                $this->cambiarEstadoIngrediente($datos);
                break;
            case 'cambiarEstadoProducto':
                $this->cambiarEstadoProducto($datos);
                break;
            case 'cambiarEstadoUsuario':
                $this->responderJson([
                    "success" => false,
                    "mensaje" => "La tabla usuarios no tiene columna 'activo'; usa Editar para cambiar el rol o Eliminar."
                ]);
                break;
            case 'cambiarEstadoPedido':
                $this->cambiarEstadoPedido($datos);
                break;
            default:
                $this->responderJson(["success" => false, "mensaje" => "Endpoint PATCH no válido"], 400);
        }
    }

    // Gestiona los borrados definitivos de la base de datos
    private function manejarDelete(string $funcion, array $datos): void
    {
        switch ($funcion) {
            case 'eliminarIngrediente':
                $this->eliminarIngrediente($datos);
                break;
            case 'eliminarProducto':
                $this->eliminarProducto($datos);
                break;
            case 'eliminarUsuario':
                $this->eliminarUsuario($datos);
                break;
            case 'eliminarPedido':
                $this->eliminarPedido($datos);
                break;
            default:
                $this->responderJson(["success" => false, "mensaje" => "Endpoint DELETE no válido"], 400);
        }
    }

    // =========================================================================
    // MÉTODOS DE CREACIÓN (POST)
    // =========================================================================

    private function crearIngrediente(array $datos): void
    {
        // Limpiamos espacios y convertimos el precio a decimal
        $nombre = trim($datos['nombre'] ?? '');
        $precio = (float) ($datos['precio_extra'] ?? 0);

        // Validación básica antes de tocar la BBDD
        if ($nombre === '') {
            $this->responderJson(["success" => false, "mensaje" => "El nombre es obligatorio"]);
            return;
        }

        $ok = IngredienteDAO::create($nombre, $precio);
        $this->responderJson([
            "success" => $ok,
            "mensaje" => $ok ? "Ingrediente añadido a la BBDD" : "Error al guardar en BBDD"
        ]);
    }

    private function crearProducto(array $datos): void
    {
        // Recogemos y casteamos los campos del formulario de productos
        $catId       = (int) ($datos['categoria_id'] ?? 1);
        $nombre      = trim($datos['nombre'] ?? '');
        $descripcion = trim($datos['descripcion'] ?? '');
        $precio      = (float) ($datos['precio'] ?? 0);
        $imagen      = trim($datos['imagen'] ?? 'default.png');

        if ($nombre === '' || $precio <= 0) {
            $this->responderJson(["success" => false, "mensaje" => "El nombre y un precio mayor a 0 son obligatorios"]);
            return;
        }

        // Instanciamos el objeto Producto y lo guardamos mediante su DAO
        $prod = productoDAO::crearInstancia(null, $catId, $nombre, $descripcion, $precio, $imagen, 1);
        $ok   = productoDAO::store($prod);

        $this->responderJson([
            "success" => $ok,
            "mensaje" => $ok ? "Producto añadido a la carta" : "Error al guardar el producto"
        ]);
    }

    private function crearUsuario(array $datos): void
    {
        $nombre   = trim($datos['nombre'] ?? '');
        $email    = trim($datos['email'] ?? '');
        $password = trim($datos['password'] ?? '');
        // Nos aseguramos de que el rol solo pueda ser 'admin' o 'usuario'
        $rol      = ($datos['rol'] ?? 'usuario') === 'admin' ? 'admin' : 'usuario';

        if ($nombre === '' || $email === '' || $password === '') {
            $this->responderJson(["success" => false, "mensaje" => "Nombre, email y contraseña son obligatorios"]);
            return;
        }

        $ok = UsuarioDAO::createAdmin($nombre, $email, $password, $rol);
        $this->responderJson([
            "success" => $ok,
            "mensaje" => $ok ? "Usuario creado correctamente" : "Error: puede que ese email ya esté registrado"
        ]);
    }

    // =========================================================================
    // MÉTODOS DE EDICIÓN (PUT)
    // =========================================================================

    private function editarIngrediente(array $datos): void
    {
        $id     = (int) ($datos['id'] ?? 0);
        $nombre = trim($datos['nombre'] ?? '');
        $precio = (float) ($datos['precio_extra'] ?? 0);

        if ($id <= 0 || $nombre === '') {
            $this->responderJson(["success" => false, "mensaje" => "ID o nombre no válidos para editar"]);
            return;
        }

        $ok = IngredienteDAO::update($id, $nombre, $precio);
        $this->responderJson([
            "success" => $ok,
            "mensaje" => $ok ? "Ingrediente actualizado correctamente" : "Error al actualizar en BBDD"
        ]);
    }

    private function editarProducto(array $datos): void
    {
        $id          = (int) ($datos['id'] ?? 0);
        $catId       = (int) ($datos['categoria_id'] ?? 1);
        $nombre      = trim($datos['nombre'] ?? '');
        $descripcion = trim($datos['descripcion'] ?? '');
        $precio      = (float) ($datos['precio'] ?? 0);
        $imagen      = trim($datos['imagen'] ?? 'default.png');
        $activo      = isset($datos['activo']) ? (int) $datos['activo'] : 1;

        if ($id <= 0 || $nombre === '' || $precio <= 0) {
            $this->responderJson(["success" => false, "mensaje" => "Datos incompletos para editar el producto"]);
            return;
        }

        $prod = productoDAO::crearInstancia($id, $catId, $nombre, $descripcion, $precio, $imagen, $activo);
        $ok   = productoDAO::update($prod);

        $this->responderJson([
            "success" => $ok,
            "mensaje" => $ok ? "Producto actualizado correctamente" : "Error al actualizar el producto"
        ]);
    }

    private function editarUsuario(array $datos): void
    {
        $id       = (int) ($datos['id'] ?? 0);
        $nombre   = trim($datos['nombre'] ?? '');
        $email    = trim($datos['email'] ?? '');
        $password = trim($datos['password'] ?? '');
        $rol      = ($datos['rol'] ?? 'usuario') === 'admin' ? 'admin' : 'usuario';

        if ($id <= 0 || $nombre === '' || $email === '') {
            $this->responderJson(["success" => false, "mensaje" => "ID, nombre y email son obligatorios"]);
            return;
        }

        // Si $password va vacío, UsuarioDAO::update mantiene la contraseña actual
        $ok = UsuarioDAO::update($id, $nombre, $email, $password, $rol);
        $this->responderJson([
            "success" => $ok,
            "mensaje" => $ok ? "Usuario actualizado correctamente" : "Error al actualizar (revisa que el email no esté duplicado)"
        ]);
    }

    // =========================================================================
    // MÉTODOS DE CAMBIO DE ESTADO (PATCH)
    // =========================================================================

    private function cambiarEstadoIngrediente(array $datos): void
    {
        $id     = (int) ($datos['id'] ?? 0);
        $activo = (int) ($datos['activo'] ?? 0);

        $ok = IngredienteDAO::cambiarEstado($id, $activo);
        $this->responderJson([
            "success" => $ok,
            "mensaje" => $ok ? "Estado del ingrediente actualizado" : "Error al cambiar estado"
        ]);
    }

    private function cambiarEstadoProducto(array $datos): void
    {
        $id     = (int) ($datos['id'] ?? 0);
        $activo = (int) ($datos['activo'] ?? 0);

        $ok = productoDAO::cambiarEstado($id, $activo);
        $this->responderJson([
            "success" => $ok,
            "mensaje" => $ok ? "Estado del producto actualizado" : "Error al cambiar estado"
        ]);
    }

    private function cambiarEstadoPedido(array $datos): void
    {
        $id     = (int) ($datos['id'] ?? 0);
        $estado = trim($datos['estado'] ?? '');

        $ok = PedidoDAO::cambiarEstado($id, $estado);
        $this->responderJson([
            "success" => $ok,
            "mensaje" => $ok ? "Estado del pedido actualizado a '$estado'" : "Error al cambiar estado del pedido"
        ]);
    }

    // =========================================================================
    // MÉTODOS DE ELIMINACIÓN (DELETE)
    // =========================================================================

    private function eliminarIngrediente(array $datos): void
    {
        $id = (int) ($datos['id'] ?? 0);
        $ok = IngredienteDAO::deleteFisico($id);

        // Si falla es por restricción de clave foránea (Foreign Key) en pedidos ya hechos
        $this->responderJson([
            "success" => $ok,
            "mensaje" => $ok
                ? "Ingrediente eliminado definitivamente"
                : "No se puede eliminar: ya está asociado a platos o pedidos. Desactívalo en su lugar."
        ]);
    }

    private function eliminarProducto(array $datos): void
    {
        $id = (int) ($datos['id'] ?? 0);
        $ok = productoDAO::destroy($id);

        $this->responderJson([
            "success" => $ok,
            "mensaje" => $ok
                ? "Producto eliminado definitivamente"
                : "No se puede eliminar: ya aparece en pedidos realizados. Desactívalo en su lugar."
        ]);
    }

    private function eliminarUsuario(array $datos): void
    {
        $id = (int) ($datos['id'] ?? 0);

        // Evitamos que el admin logueado pueda borrarse a sí mismo por accidente
        if ($id === (int) ($_SESSION['usuario']['id'] ?? 0)) {
            $this->responderJson(["success" => false, "mensaje" => "No puedes eliminar tu propia cuenta de administrador."]);
            return;
        }

        $ok = UsuarioDAO::deleteFisico($id);
        $this->responderJson([
            "success" => $ok,
            "mensaje" => $ok
                ? "Usuario eliminado definitivamente"
                : "No se puede eliminar: este usuario tiene pedidos registrados en la BBDD."
        ]);
    }

    private function eliminarPedido(array $datos): void
    {
        $id = (int) ($datos['id'] ?? 0);
        $ok = PedidoDAO::deleteFisico($id);

        $this->responderJson([
            "success" => $ok,
            "mensaje" => $ok ? "Pedido eliminado correctamente" : "Error al eliminar el pedido"
        ]);
    }

    // =========================================================================
    // MÉTODOS AUXILIARES PRIVADOS
    // =========================================================================

    // Centraliza el envío de respuestas JSON con su código de estado HTTP
    private function responderJson($payload, int $codigoHttp = 200): void
    {
        http_response_code($codigoHttp);
        echo json_encode($payload);
    }

    // Middleware interno para proteger tanto la vista HTML como los endpoints de la API
    private function verificarAdmin(bool $responderConJson = false): void
    {
        $esAdmin = isset($_SESSION['usuario']) && ($_SESSION['usuario']['rol'] ?? '') === 'admin';

        if (!$esAdmin) {
            if ($responderConJson) {
                header('Content-Type: application/json; charset=utf-8');
                $this->responderJson([
                    "success" => false,
                    "mensaje" => "Acceso denegado: se requieren permisos de administrador."
                ], 403);
            } else {
                header("Location: index.php?controller=usuario&action=login");
            }
            exit();
        }
    }
}