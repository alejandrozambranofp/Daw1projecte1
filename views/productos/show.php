<style>
    .detalle-contenedor {
        max-width: 850px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .detalle-volver {
        display: inline-block;
        margin-bottom: 1.5rem;
        color: #333;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: color 0.2s ease;
    }

    .detalle-volver:hover {
        color: #d8af1d;
    }

    .detalle-tarjeta {
        display: flex;
        flex-wrap: wrap;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .detalle-imagen {
        flex: 1 1 350px;
        background-color: #f9fafb;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .detalle-imagen img {
        width: 100%;
        height: 100%;
        max-height: 400px;
        object-fit: cover;
        display: block;
    }

    .detalle-info {
        flex: 1 1 350px;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .detalle-titulo {
        margin: 0 0 0.75rem 0;
        font-size: 1.8rem;
        color: #111827;
    }

    .detalle-descripcion {
        color: #4b5563;
        font-size: 1rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .detalle-precio {
        font-size: 1.6rem;
        font-weight: 700;
        color: #000000;
        margin-bottom: 1.5rem;
    }

    .detalle-acciones {
        display: flex;
        justify-content: flex-end;
        padding-top: 1rem;
        border-top: 1px solid #f3f4f6;
    }

    .btn-volver-accion {
        display: inline-block;
        padding: 0.6rem 1.25rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: #ffffff;
        background-color: #000000;
        border-radius: 6px;
        text-decoration: none;
        transition: background-color 0.2s ease, transform 0.1s ease;
    }

    .btn-volver-accion:hover {
        background-color: #d8af1d;
        transform: translateY(-1px);
    }

    .error-tarjeta {
        text-align: center;
        padding: 3rem 2rem;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
    }
</style>

<div class="detalle-contenedor">
    <?php if (!empty($producto) && is_object($producto)): ?>
        
        <a href="?controller=producto" class="detalle-volver">← Volver a la carta</a>

        <div class="detalle-tarjeta">
            <div class="detalle-imagen">
                <img src="imagenes/<?= htmlspecialchars($producto->getImagen(), ENT_QUOTES, 'UTF-8') ?>" 
                     alt="<?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="detalle-info">
                <div>
                    <h1 class="detalle-titulo"><?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?></h1>
                    <p class="detalle-descripcion"><?= htmlspecialchars($producto->getDescripcion(), ENT_QUOTES, 'UTF-8') ?></p>
                </div>

                <div>
                    <div class="detalle-precio">
                        <?= number_format((float) $producto->getPrecio(), 2, ',', '.') ?> €
                    </div>

                    <div class="detalle-acciones">
                        <a href="?controller=producto" class="btn-volver-accion">Seguir viendo la carta</a>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="error-tarjeta">
            <h1>Producto no encontrado</h1>
            <p>El producto que intentas consultar no existe o ya no está disponible.</p>
            <br>
            <a href="?controller=producto" class="btn-volver-accion">Volver a la carta</a>
        </div>
    <?php endif; ?>
</div>