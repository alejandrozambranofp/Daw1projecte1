<style>
    .ticket-contenedor {
        max-width: 700px;
        margin: 2.5rem auto;
        padding: 0 1rem;
    }

    .ticket-tarjeta {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 2.5rem 2rem;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        text-align: center;
    }

    .ticket-icono {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem auto;
        background-color: #dcfce7;
        color: #16a34a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: bold;
    }

    .ticket-titulo {
        margin: 0 0 0.5rem 0;
        font-size: 1.8rem;
        color: #111827;
    }

    .ticket-subtitulo {
        color: #4b5563;
        margin-bottom: 1.5rem;
        font-size: 1rem;
    }

    .ticket-meta {
        display: flex;
        justify-content: space-between;
        background: #f9fafb;
        padding: 0.85rem 1.2rem;
        border-radius: 8px;
        font-size: 0.9rem;
        color: #374151;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }

    .ticket-lista {
        text-align: left;
        border-top: 1px dashed #d1d5db;
        border-bottom: 1px dashed #d1d5db;
        padding: 1rem 0;
        margin-bottom: 1.5rem;
    }

    .ticket-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.6rem 0;
    }

    .ticket-item-ing {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 0.2rem;
    }

    .ticket-total {
        display: flex;
        justify-content: space-between;
        font-size: 1.35rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 2rem;
    }

    .btn-volver-carta {
        display: inline-block;
        padding: 0.8rem 1.8rem;
        background-color: #000000;
        color: #ffffff;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: background-color 0.2s ease;
    }

    .btn-volver-carta:hover {
        background-color: #d8af1d;
        color: #000000;
    }
</style>

<div class="ticket-contenedor">
    <?php if (!empty($resumenPedido)): ?>
        <div class="ticket-tarjeta">
            <div class="ticket-icono">✓</div>
            <h1 class="ticket-titulo">¡Pedido confirmado en la Posada!</h1>
            <p class="ticket-subtitulo">Nuestro cocinero ya ha encendido los fogones en Hyrule para preparar tu comanda.</p>

            <!-- Cabecera del ticket con número de pedido y fecha -->
            <div class="ticket-meta">
                <span><strong>Nº Pedido:</strong> <?= htmlspecialchars($resumenPedido['numero'], ENT_QUOTES, 'UTF-8') ?></span>
                <span><strong>Fecha:</strong> <?= htmlspecialchars($resumenPedido['fecha'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>

            <!-- Desglose de los platos comprados y sus ingredientes -->
            <div class="ticket-lista">
                <?php foreach ($resumenPedido['items'] as $item): ?>
                    <div class="ticket-item">
                        <div>
                            <strong><?= htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <?php if (!empty($item['ingredientes'])): ?>
                                <div class="ticket-item-ing">
                                    <?php
                                        // Creamos un array de textos tipo "Carne (x2)" y los unimos con comas
                                        $nombresIng = [];
                                        foreach ($item['ingredientes'] as $ing) {
                                            $nombresIng[] = htmlspecialchars($ing['nombre'], ENT_QUOTES, 'UTF-8') . " (x" . (int)$ing['cantidad'] . ")";
                                        }
                                        echo implode(", ", $nombresIng);
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <strong><?= number_format($item['precio_unitario'] * $item['cantidad'], 2, ',', '.') ?> €</strong>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="ticket-total">
                <span>Total pagado</span>
                <span><?= number_format($resumenPedido['total'], 2, ',', '.') ?> €</span>
            </div>

            <a href="?controller=producto" class="btn-volver-carta">Volver a la carta</a>
        </div>
    <?php else: ?>
        <div class="ticket-tarjeta">
            <h2>No hay ningún pedido reciente</h2>
            <br>
            <a href="?controller=producto" class="btn-volver-carta">Ir a la carta</a>
        </div>
    <?php endif; ?>
</div>