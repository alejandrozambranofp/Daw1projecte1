<?php
require_once "config/dataBase.php";
require_once "models/Usuario.php";

class UsuarioDAO
{
    /**
     * Registra un nuevo usuario solo con nombre, email y contraseña encriptada.
     */
    public static function registrar($nombre, $email, $passwordPlano)
    {
        $con = DataBase::connect();
        $passwordHash = password_hash($passwordPlano, PASSWORD_DEFAULT);

        try {
            $stmt = $con->prepare(
                "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'usuario')"
            );
            $stmt->bind_param("sss", $nombre, $email, $passwordHash);
            $ok = $stmt->execute();

            $stmt->close();
            $con->close();
            return $ok;

        } catch (mysqli_sql_exception $e) {
            // Si el email ya existe (UNIQUE), devolvemos false sin romper la página
            $con->close();
            return false;
        }
    }

    /**
     * Busca un usuario por su email para comprobar su contraseña al iniciar sesión.
     */
    public static function getByEmail($email)
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $usuario = null;

        if ($fila = $resultado->fetch_assoc()) {
            $usuario = new Usuario();
            $usuario->setId($fila['id']);
            $usuario->setNombre($fila['nombre']);
            $usuario->setEmail($fila['email']);
            $usuario->setPassword($fila['password']);
            $usuario->setRol($fila['rol']);
        }

        $stmt->close();
        $con->close();
        return $usuario;
    }
}