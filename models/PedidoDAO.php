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
            // (estado='pendiente', fecha_pedido=CURRENT_TIMESTAMP, descuento=0.00, metodo_pago='simulado', estado_pago='pendiente' van por defecto)
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

        } catch (Exception $e) {
            // Si algo falló, deshacemos todo para no dejar datos corruptos en MySQL
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
}