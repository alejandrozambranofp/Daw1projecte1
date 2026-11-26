<?php
require_once "models/IngredienteDAO.php";

class PedidoDAO
{
    /**
     * Guarda el pedido completo (cabecera, líneas de producto e ingredientes) usando una transacción.
     * Devuelve el ID del pedido creado si todo va bien, o false si ocurre algún error.
     */
    public static function crearPedido($usuarioId, $itemsCarrito, $totalPedido)
    {
        $con = DataBase::connect();

        // Iniciamos una transacción: o se guardan las 3 tablas enteras, o no se guarda nada
        $con->begin_transaction();

        try {
            // Preparamos una consulta para averiguar el precio_extra oficial de cada ingrediente
            $stmtPrecioIng = $con->prepare("SELECT precio_extra FROM ingredientes WHERE id = ?");

            // 1. INSERTAR CABECERA EN 'pedidos'
            $sqlPedido = "INSERT INTO pedidos (usuario_id, subtotal, total, estado_pago) VALUES (?, ?, ?, 'pagado')";
            $stmtPedido = $con->prepare($sqlPedido);
            $stmtPedido->bind_param("idd", $usuarioId, $totalPedido, $totalPedido);
            $stmtPedido->execute();

            // Obtenemos el ID recién generado de la tabla 'pedidos'
            $pedidoId = $con->insert_id;

            // Preparamos las sentencias para los bucles de detalle e ingredientes
            $sqlDetalle = "INSERT INTO pedido_detalle (pedido_id, producto_id, nombre_producto, precio_unitario, cantidad, subtotal)
                           VALUES (?, ?, ?, ?, ?, ?)";
            $stmtDetalle = $con->prepare($sqlDetalle);

            $sqlIngrediente = "INSERT INTO pedido_detalle_ingrediente (pedido_detalle_id, ingrediente_id, nombre_ingrediente, precio_extra, accion)
                               VALUES (?, ?, ?, ?, ?)";
            $stmtIng = $con->prepare($sqlIngrediente);

            // 2. RECORRER CADA PLATO DEL CARRITO E INSERTAR EN 'pedido_detalle'
            foreach ($itemsCarrito as $item) {
                $productoId     = (int) $item['id_producto'];
                $nombreProducto = $item['nombre'];
                $precioUnitario = (float) $item['precio_unitario'];
                $cantidad       = (int) $item['cantidad'];
                $subtotalLinea  = $precioUnitario * $cantidad;

                $stmtDetalle->bind_param("iisdid", $pedidoId, $productoId, $nombreProducto, $precioUnitario, $cantidad, $subtotalLinea);
                $stmtDetalle->execute();

                // Obtenemos el ID recién generado de 'pedido_detalle'
                $pedidoDetalleId = $con->insert_id;

                // Sabemos qué ingredientes venían de serie en este plato para marcar si es 'incluido' o 'extra'
                $ingredientesBase = IngredienteDAO::getIdsPorProducto($productoId);

                // 3. INSERTAR LOS INGREDIENTES DE ESTE PLATO EN 'pedido_detalle_ingrediente'
                if (!empty($item['ingredientes'])) {
                    foreach ($item['ingredientes'] as $ing) {
                        $ingId     = (int) $ing['id'];
                        $ingNombre = $ing['nombre'];
                        $ingCant   = (int) $ing['cantidad'];

                        // Consultamos su precio extra en la BBDD
                        $stmtPrecioIng->bind_param("i", $ingId);
                        $stmtPrecioIng->execute();
                        $resPrecio = $stmtPrecioIng->get_result()->fetch_assoc();
                        $precioExtraUnitario = (float) ($resPrecio['precio_extra'] ?? 0);

                        // Definimos la acción y el coste extra según si venía incluido de serie o es añadido
                        $esBase = in_array($ingId, $ingredientesBase);
                        $accion = $esBase ? ($ingCant > 1 ? "incluido + extra (x$ingCant)" : "incluido") : "extra (x$ingCant)";
                        $unidadesCobrables = max(0, $ingCant - ($esBase ? 1 : 0));
                        $costeExtraTotal = $unidadesCobrables * $precioExtraUnitario;

                        $stmtIng->bind_param("iisds", $pedidoDetalleId, $ingId, $ingNombre, $costeExtraTotal, $accion);
                        $stmtIng->execute();
                    }
                }
            }

            // Si todas las inserciones han ido bien, confirmamos los cambios en MySQL
            $con->commit();
            $con->close();

            return $pedidoId;

        } catch (Throwable $e) {
            // Si algo falló (excepción SQL o error de ejecución), deshacemos todo
            $con->rollback();
            $con->close();
            return false;
        }
    }

    /**
     * Devuelve todos los pedidos de un usuario ordenados del más reciente al más antiguo
     */
    public static function getByUsuario($usuarioId)
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha_pedido DESC");
        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $pedidos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $pedidos[] = $fila;
        }
        $con->close();
        return $pedidos;
    }

    /**
     * GET (Admin): Devuelve todos los pedidos con los datos del usuario, sus líneas (pedido_detalle)
     * y los ingredientes/extras de cada línea (pedido_detalle_ingrediente)
     */
    public static function getAllConDetalles()
    {
        $con = DataBase::connect();

        // 1. Cabecera de pedidos + nombre y email del cliente
        $sqlPedidos = "SELECT p.*, u.nombre AS usuario_nombre, u.email AS usuario_email
                       FROM pedidos p
                       LEFT JOIN usuarios u ON p.usuario_id = u.id
                       ORDER BY p.fecha_pedido DESC, p.id DESC";
        $resPedidos = $con->query($sqlPedidos);

        $stmtDetalles = $con->prepare("SELECT * FROM pedido_detalle WHERE pedido_id = ?");
        $stmtIngredientes = $con->prepare("SELECT * FROM pedido_detalle_ingrediente WHERE pedido_detalle_id = ?");

        $pedidos = [];
        while ($pedido = $resPedidos->fetch_assoc()) {
            $pedidoId = (int) $pedido['id'];

            // 2. Líneas de platos del pedido
            $stmtDetalles->bind_param("i", $pedidoId);
            $stmtDetalles->execute();
            $resDet = $stmtDetalles->get_result();

            $detalles = [];
            while ($det = $resDet->fetch_assoc()) {
                $detalleId = (int) $det['id'];

                // 3. Ingredientes de cada plato
                $stmtIngredientes->bind_param("i", $detalleId);
                $stmtIngredientes->execute();
                $resIng = $stmtIngredientes->get_result();

                $ingredientes = [];
                while ($ing = $resIng->fetch_assoc()) {
                    $ingredientes[] = $ing;
                }

                $det['ingredientes'] = $ingredientes;
                $detalles[] = $det;
            }

            $pedido['detalles'] = $detalles;
            $pedidos[] = $pedido;
        }

        $con->close();
        return $pedidos;
    }

    /**
     * PATCH: Cambia el estado de un pedido ('pendiente', 'preparando', 'entregado', 'cancelado')
     */
    public static function cambiarEstado($id, $estado)
    {
        $estadosValidos = ['pendiente', 'preparando', 'entregado', 'cancelado'];
        if (!in_array($estado, $estadosValidos)) {
            return false;
        }

        $con = DataBase::connect();
        $stmt = $con->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
        $stmt->bind_param("si", $estado, $id);
        $ok = $stmt->execute();

        $stmt->close();
        $con->close();
        return $ok;
    }

    /**
     * DELETE: Elimina un pedido y sus detalles/ingredientes en transacción
     */
    public static function deleteFisico($id)
    {
        $con = DataBase::connect();
        $con->begin_transaction();

        try {
            $stmtIng = $con->prepare("DELETE pdi FROM pedido_detalle_ingrediente pdi
                                      INNER JOIN pedido_detalle pd ON pdi.pedido_detalle_id = pd.id
                                      WHERE pd.pedido_id = ?");
            $stmtIng->bind_param("i", $id);
            $stmtIng->execute();

            $stmtDet = $con->prepare("DELETE FROM pedido_detalle WHERE pedido_id = ?");
            $stmtDet->bind_param("i", $id);
            $stmtDet->execute();

            $stmtPed = $con->prepare("DELETE FROM pedidos WHERE id = ?");
            $stmtPed->bind_param("i", $id);
            $stmtPed->execute();

            $con->commit();
            $con->close();
            return true;
        } catch (Throwable $e) {
            $con->rollback();
            $con->close();
            return false;
        }
    }

}