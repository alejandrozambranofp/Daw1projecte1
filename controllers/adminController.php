<?php
require_once "models/productoDAO.php";
require_once "models/IngredienteDAO.php";
require_once "models/UsuarioDAO.php";

class adminController
{
    public function index()
    {
        $this->verificarAdmin(false);
        $view = "views/admin/admin.php";
        include_once "views/main.php";
    }

    public function api()
    {
        $this->verificarAdmin(true);
        header('Content-Type: application/json; charset=utf-8');
        $metodo = $_SERVER['REQUEST_METHOD'];
        $funcion = $_GET['function'] ?? '';
        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        // =========================================================================
        // 1. MÉTODO GET
        // =========================================================================
        if ($metodo === "GET") {

            if ($funcion === "ingredientes") {
                echo json_encode(IngredienteDAO::getAll());

            } elseif ($funcion === "productos") {
                echo json_encode(productoDAO::getAll());

            } elseif ($funcion === "usuarios") {
                echo json_encode(UsuarioDAO::getAll());

            } else {
                http_response_code(400);
                echo json_encode(["success" => false, "mensaje" => "Endpoint GET no válido"]);
            }

        // =========================================================================
        // 2. MÉTODO POST
        // =========================================================================
        } elseif ($metodo === "POST") {

            if ($funcion === "crearIngrediente") {
                $nombre = trim($datos['nombre'] ?? '');
                $precio = (float) ($datos['precio_extra'] ?? 0);

                if ($nombre !== '') {
                    $ok = IngredienteDAO::create($nombre, $precio);
                    echo json_encode([
                        "success" => $ok,
                        "mensaje" => $ok ? "Ingrediente añadido a la BBDD" : "Error al guardar en BBDD"
                    ]);
                } else {
                    echo json_encode(["success" => false, "mensaje" => "El nombre es obligatorio"]);
                }

            } elseif ($funcion === "crearProducto") {
                $catId       = (int) ($datos['categoria_id'] ?? 1);
                $nombre      = trim($datos['nombre'] ?? '');
                $descripcion = trim($datos['descripcion'] ?? '');
                $precio      = (float) ($datos['precio'] ?? 0);
                $imagen      = trim($datos['imagen'] ?? 'default.png');

                if ($nombre !== '' && $precio > 0) {
                    $prod = productoDAO::crearInstancia(null, $catId, $nombre, $descripcion, $precio, $imagen, 1);
                    $ok = productoDAO::store($prod);

                    echo json_encode([
                        "success" => $ok,
                        "mensaje" => $ok ? "Producto añadido a la carta" : "Error al guardar el producto"
                    ]);
                } else {
                    echo json_encode(["success" => false, "mensaje" => "El nombre y un precio mayor a 0 son obligatorios"]);
                }

            } elseif ($funcion === "crearUsuario") {
                $nombre   = trim($datos['nombre'] ?? '');
                $email    = trim($datos['email'] ?? '');
                $password = trim($datos['password'] ?? '');
                $rol      = ($datos['rol'] ?? 'usuario') === 'admin' ? 'admin' : 'usuario';

                if ($nombre !== '' && $email !== '' && $password !== '') {
                    $ok = UsuarioDAO::createAdmin($nombre, $email, $password, $rol);
                    echo json_encode([
                        "success" => $ok,
                        "mensaje" => $ok ? "Usuario creado correctamente" : "Error: puede que ese email ya esté registrado"
                    ]);
                } else {
                    echo json_encode(["success" => false, "mensaje" => "Nombre, email y contraseña son obligatorios"]);
                }
            }

        // =========================================================================
        // 3. MÉTODO PUT
        // =========================================================================
        } elseif ($metodo === "PUT") {

            if ($funcion === "editarIngrediente") {
                $id     = (int) ($datos['id'] ?? 0);
                $nombre = trim($datos['nombre'] ?? '');
                $precio = (float) ($datos['precio_extra'] ?? 0);

                if ($id > 0 && $nombre !== '') {
                    $ok = IngredienteDAO::update($id, $nombre, $precio);
                    echo json_encode([
                        "success" => $ok,
                        "mensaje" => $ok ? "Ingrediente actualizado correctamente" : "Error al actualizar en BBDD"
                    ]);
                } else {
                    echo json_encode(["success" => false, "mensaje" => "ID o nombre no válidos para editar"]);
                }

            } elseif ($funcion === "editarProducto") {
                $id          = (int) ($datos['id'] ?? 0);
                $catId       = (int) ($datos['categoria_id'] ?? 1);
                $nombre      = trim($datos['nombre'] ?? '');
                $descripcion = trim($datos['descripcion'] ?? '');
                $precio      = (float) ($datos['precio'] ?? 0);
                $imagen      = trim($datos['imagen'] ?? 'default.png');
                $activo      = isset($datos['activo']) ? (int) $datos['activo'] : 1;

                if ($id > 0 && $nombre !== '' && $precio > 0) {
                    $prod = productoDAO::crearInstancia($id, $catId, $nombre, $descripcion, $precio, $imagen, $activo);
                    $ok = productoDAO::update($prod);

                    echo json_encode([
                        "success" => $ok,
                        "mensaje" => $ok ? "Producto actualizado correctamente" : "Error al actualizar el producto"
                    ]);
                } else {
                    echo json_encode(["success" => false, "mensaje" => "Datos incompletos para editar el producto"]);
                }

            } elseif ($funcion === "editarUsuario") {
                $id       = (int) ($datos['id'] ?? 0);
                $nombre   = trim($datos['nombre'] ?? '');
                $email    = trim($datos['email'] ?? '');
                $password = trim($datos['password'] ?? '');
                $rol      = ($datos['rol'] ?? 'usuario') === 'admin' ? 'admin' : 'usuario';

                if ($id > 0 && $nombre !== '' && $email !== '') {
                    $ok = UsuarioDAO::update($id, $nombre, $email, $password, $rol);
                    echo json_encode([
                        "success" => $ok,
                        "mensaje" => $ok ? "Usuario actualizado correctamente" : "Error al actualizar (revisa que el email no esté duplicado)"
                    ]);
                } else {
                    echo json_encode(["success" => false, "mensaje" => "ID, nombre y email son obligatorios"]);
                }
            }

        // =========================================================================
        // 4. MÉTODO PATCH
        // =========================================================================
        } elseif ($metodo === "PATCH") {

            if ($funcion === "cambiarEstadoIngrediente") {
                $id     = (int) ($datos['id'] ?? 0);
                $activo = (int) ($datos['activo'] ?? 0);

                $ok = IngredienteDAO::cambiarEstado($id, $activo);
                echo json_encode([
                    "success" => $ok,
                    "mensaje" => $ok ? "Estado del ingrediente actualizado" : "Error al cambiar estado"
                ]);

            } elseif ($funcion === "cambiarEstadoProducto") {
                $id     = (int) ($datos['id'] ?? 0);
                $activo = (int) ($datos['activo'] ?? 0);

                $ok = productoDAO::cambiarEstado($id, $activo);
                echo json_encode([
                    "success" => $ok,
                    "mensaje" => $ok ? "Estado del producto actualizado" : "Error al cambiar estado"
                ]);

            } elseif ($funcion === "cambiarEstadoUsuario") {
                echo json_encode([
                    "success" => false,
                    "mensaje" => "La tabla usuarios no tiene columna 'activo'; usa Editar para cambiar el rol o Eliminar."
                ]);
            }

        // =========================================================================
        // 5. MÉTODO DELETE
        // =========================================================================
        } elseif ($metodo === "DELETE") {

            if ($funcion === "eliminarIngrediente") {
                $id = (int) ($datos['id'] ?? 0);
                $ok = IngredienteDAO::deleteFisico($id);
                echo json_encode([
                    "success" => $ok,
                    "mensaje" => $ok
                        ? "Ingrediente eliminado definitivamente"
                        : "No se puede eliminar: ya está asociado a platos o pedidos. Desactívalo en su lugar."
                ]);

            } elseif ($funcion === "eliminarProducto") {
                $id = (int) ($datos['id'] ?? 0);
                $ok = productoDAO::destroy($id);
                echo json_encode([
                    "success" => $ok,
                    "mensaje" => $ok
                        ? "Producto eliminado definitivamente"
                        : "No se puede eliminar: ya aparece en pedidos realizados. Desactívalo en su lugar."
                ]);

            } elseif ($funcion === "eliminarUsuario") {
                $id = (int) ($datos['id'] ?? 0);

                if ($id === (int) ($_SESSION['usuario']['id'] ?? 0)) {
                    echo json_encode(["success" => false, "mensaje" => "No puedes eliminar tu propia cuenta de administrador."]);
                } else {
                    $ok = UsuarioDAO::deleteFisico($id);
                    echo json_encode([
                        "success" => $ok,
                        "mensaje" => $ok
                            ? "Usuario eliminado definitivamente"
                            : "No se puede eliminar: este usuario tiene pedidos registrados en la BBDD."
                    ]);
                }
            }

        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "mensaje" => "Método HTTP no permitido"]);
        }

        exit();
    }

    private function verificarAdmin($responderConJson = false)
    {
        $esAdmin = isset($_SESSION['usuario']) && ($_SESSION['usuario']['rol'] ?? '') === 'admin';

        if (!$esAdmin) {
            if ($responderConJson) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(403);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Acceso denegado: se requieren permisos de administrador."
                ]);
            } else {
                header("Location: index.php?controller=usuario&action=login");
            }
            exit();
        }
    }
}