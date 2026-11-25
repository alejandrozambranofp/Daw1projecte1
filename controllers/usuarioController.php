<?php
require_once "models/UsuarioDAO.php";

class usuarioController
{
    /**
     * Muestra el formulario de registro y procesa el alta si llega por POST
     */
    public function registro()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre   = trim($_POST['nombre'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($nombre !== '' && $email !== '' && $password !== '') {
                $registrado = UsuarioDAO::registrar($nombre, $email, $password);

                if ($registrado) {
                    // Si se registra bien, lo mandamos directo al login con un aviso
                    header("Location: index.php?controller=usuario&action=login&registrado=1");
                    exit();
                } else {
                    $error = "Ese correo electrónico ya está registrado en la posada.";
                }
            } else {
                $error = "Todos los campos son obligatorios.";
            }
        }

        $view = "views/usuario/registro.php";
        include_once "views/main.php";
    }

    /**
     * Muestra el formulario de login y valida credenciales con password_verify()
     */
    public function login()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $usuario = UsuarioDAO::getByEmail($email);

            // Comprobamos que exista el usuario y que la contraseña coincida con el hash guardado
            if ($usuario && password_verify($password, $usuario->getPassword())) {
                // Guardamos en sesión solo los datos útiles (nunca la contraseña)
                $_SESSION['usuario'] = [
                    "id"     => $usuario->getId(),
                    "nombre" => $usuario->getNombre(),
                    "email"  => $usuario->getEmail(),
                    "rol"    => $usuario->getRol()
                ];

                // Si es admin lo llevamos a su panel; si es cliente normal, a la carta
                if ($usuario->getRol() === 'admin') {
                    header("Location: index.php?controller=admin&action=index");
                } else {
                    header("Location: index.php?controller=producto&action=index");
                }
                exit();
            } else {
                $error = "Correo o contraseña incorrectos.";
            }
        }

        $view = "views/usuario/login.php";
        include_once "views/main.php";
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout()
    {
        unset($_SESSION['usuario']);
        header("Location: index.php?controller=producto&action=index");
        exit();
    }
    /**
     * Acción por defecto si entran a ?controller=usuario sin especificar &action=
     */
    public function index()
    {
        if (isset($_SESSION['usuario'])) {
            header("Location: index.php?controller=producto&action=index");
            exit();
        }
        $this->login();
    }
}