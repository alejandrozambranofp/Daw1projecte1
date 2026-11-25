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

    // =========================================================================
    // MÉTODOS PARA EL PANEL DE ADMINISTRACIÓN (API REST)
    // =========================================================================

    public static function getAll()
    {
        $con = DataBase::connect();
        // No pedimos 'password' para no enviarlo nunca al navegador en el JSON
        $stmt = $con->prepare("SELECT id, nombre, email, rol FROM usuarios ORDER BY rol ASC, id ASC");
        $stmt->execute();
        $resultado = $stmt->get_result();

        $usuarios = [];
        while ($fila = $resultado->fetch_assoc()) {
            // Añadimos activo = 1 por defecto para que el badge de la tabla se pinte bien
            $fila['activo'] = 1;
            $usuarios[] = $fila;
        }

        $stmt->close();
        $con->close();
        return $usuarios;
    }

    public static function createAdmin($nombre, $email, $passwordPlano, $rol)
    {
        $con = DataBase::connect();
        $passwordHash = password_hash($passwordPlano, PASSWORD_DEFAULT);

        try {
            $stmt = $con->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $email, $passwordHash, $rol);
            $ok = $stmt->execute();

            $stmt->close();
            $con->close();
            return $ok;
        } catch (mysqli_sql_exception $e) {
            $con->close();
            return false;
        }
    }

    public static function update($id, $nombre, $email, $passwordPlano, $rol)
    {
        $con = DataBase::connect();

        try {
            // Si el admin escribió contraseña nueva, la ciframos y actualizamos; si no, mantenemos la antigua
            if ($passwordPlano !== '') {
                $passwordHash = password_hash($passwordPlano, PASSWORD_DEFAULT);
                $stmt = $con->prepare("UPDATE usuarios SET nombre = ?, email = ?, password = ?, rol = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $nombre, $email, $passwordHash, $rol, $id);
            } else {
                $stmt = $con->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?");
                $stmt->bind_param("sssi", $nombre, $email, $rol, $id);
            }

            $ok = $stmt->execute();
            $stmt->close();
            $con->close();
            return $ok;
        } catch (mysqli_sql_exception $e) {
            $con->close();
            return false;
        }
    }

    public static function deleteFisico($id)
    {
        $con = DataBase::connect();
        try {
            $stmt = $con->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->bind_param("i", $id);
            $ok = $stmt->execute();
            $stmt->close();
            $con->close();
            return $ok;
        } catch (mysqli_sql_exception $e) {
            $con->close();
            return false;
        }
    }
}