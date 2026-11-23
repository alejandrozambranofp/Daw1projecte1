<style>
    .carrito-contenedor {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .carrito-titulo {
        font-size: 1.8rem;
        color: #111827;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #d8af1d;
        padding-bottom: 0.5rem;
    }

    .carrito-tabla {
        width: 100%;
        border-collapse: collapse;
        background: #ffffff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .carrito-tabla th {
        background-color: #111827;
        color: #ffffff;
        text-align: left;
        padding: 1rem;
        font-size: 0.9rem;
        text-transform: uppercase;
    }

    .carrito-tabla td {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .carrito-producto-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .carrito-producto-info img {
        width: 65px;
        height: 65px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .carrito-ingredientes-lista {
        margin: 0.35rem 0 0 0;
        padding-left: 1.1rem;
        font-size: 0.85rem;
        color: #4b5563;
    }

    .btn-quitar {
        display: inline-block;
        padding: 0.45rem 0.8rem;
        background-color: #fee2e2;
        color: #b91c1c;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
        transition: background-color 0.2s;
    }

    .btn-quitar:hover {
        background-color: #fecaca;
    }

    .carrito-resumen {
        margin-top: 1.5rem;
        background: #ffffff;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .carrito-total-texto {
        font-size: 1.4rem;
        font-weight: 700;
        color: #111827;
    }

    .carrito-botones {
        display: flex;
        gap: 0.8rem;
        flex-wrap: wrap;
    }

    .btn-secundario {
        padding: 0.7rem 1.2rem;
        background-color: #f3f4f6;
        color: #111827;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .btn-secundario:hover {
        background-color: #e5e7eb;
    }

    .btn-vaciar {
        padding: 0.7rem 1.2rem;
        background-color: #ef4444;
        color: #ffffff;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .btn-vaciar:hover {
        background-color: #dc2626;
    }

    .btn-confirmar {
        padding: 0.7rem 1.5rem;
        background-color: #000000;
        color: #ffffff;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: background-color 0.2s;
    }

    .btn-confirmar:hover {
        background-color: #d8af1d;
        color: #000000;
    }

    .carrito-vacio {
        text-align: center;
        padding: 3rem 2rem;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
</style>

<div class="carrito-contenedor">
    <h1 class="carrito-titulo">Tu Alforja de Pedido</h1>

    <?php
        // Recuperamos el array del carrito desde la sesión (si no existe, usamos un array vacío)
        $carrito = $_SESSION['carrito'] ?? [];
        // Inicializamos el acumulador del precio total de todo el pedido a 0
        $totalGeneral = 0;
    ?>

    <?php if (!empty($carrito)): ?>
        <table class="carrito-tabla">
            <thead>
                <tr>
                    <th>Plato y personalización</th>
                    <th>Precio</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrito as $indice => $item): ?>
                    <?php
                        // Calculamos el subtotal de esta línea (precio unitario * cantidad) y lo sumamos al total general
                        $subtotal = $item['precio_unitario'] * $item['cantidad'];
                        $totalGeneral += $subtotal;
                    ?>
                    <tr>
                        <td>
                            <div class="carrito-producto-info">
                                <img src="imagenes/<?= htmlspecialchars($item['imagen'], ENT_QUOTES, 'UTF-8') ?>" 
                                     alt="<?= htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                                <div>
                                    <strong><?= htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8') ?></strong>

                                    <!-- Si el plato lleva ingredientes seleccionados, los listamos debajo de su nombre -->
                                    <?php if (!empty($item['ingredientes'])): ?>
                                        <ul class="carrito-ingredientes-lista">
                                            <?php foreach ($item['ingredientes'] as $ing): ?>
                                                <li>
                                                    <?= htmlspecialchars($ing['nombre'], ENT_QUOTES, 'UTF-8') ?> 
                                                    (x<?= (int) $ing['cantidad'] ?>)
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p style="margin: 0.3rem 0 0 0; font-size: 0.85rem; color: #6b7280;">Sin ingredientes seleccionados</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>

                        <td>
                            <strong><?= number_format($subtotal, 2, ',', '.') ?> €</strong>
                        </td>

                        <td>
                            <!-- Pasamos el $indice exacto en la URL para saber qué fila borrar de $_SESSION['carrito'] -->
                            <a href="?controller=carrito&action=remove&index=<?= $indice ?>" class="btn-quitar">Quitar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="carrito-resumen">
            <div class="carrito-total-texto">
                Total a pagar: <?= number_format($totalGeneral, 2, ',', '.') ?> €
            </div>

            <div class="carrito-botones">
                <a href="?controller=producto" class="btn-secundario">Seguir pidiendo</a>
                <a href="?controller=carrito&action=deleteAll" class="btn-vaciar">Vaciar alforja</a>
                <a href="?controller=carrito&action=confirmar" class="btn-confirmar">Confirmar pedido</a>
            </div>
        </div>

    <?php else: ?>
        <div class="carrito-vacio">
            <h2>Tu alforja está vacía</h2>
            <p>Aún no has añadido ningún manjar de la posada a tu pedido.</p>
            <br>
            <a href="?controller=producto" class="btn-confirmar">Ir a la carta</a>
        </div>
    <?php endif; ?>
</div>