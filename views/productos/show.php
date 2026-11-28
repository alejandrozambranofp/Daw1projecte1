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
        margin-bottom: 1.2rem;
    }

    /* Estilos para la lista de ingredientes */
    .ingredientes-box {
        border-top: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
        padding: 1rem 0;
        margin-bottom: 1.2rem;
    }

    .ingredientes-box h3 {
        margin: 0 0 0.8rem 0;
        font-size: 1.05rem;
        color: #111827;
    }

    .ingrediente-fila {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.45rem 0;
        border-bottom: 1px dashed #f3f4f6;
    }

    .ingrediente-fila:last-child {
        border-bottom: none;
    }

    .ingrediente-nombre {
        font-weight: 500;
        color: #1f2937;
        font-size: 0.95rem;
    }

    .ingrediente-tag {
        font-size: 0.75rem;
        padding: 0.15rem 0.45rem;
        border-radius: 4px;
        margin-left: 0.4rem;
    }

    .tag-incluido {
        background: #e0f2fe;
        color: #0369a1;
    }

    .tag-extra {
        background: #fef3c7;
        color: #92400e;
    }

    .ingrediente-controles {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-qty {
        width: 28px;
        height: 28px;
        border: 1px solid #d1d5db;
        background: #f9fafb;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.15s;
    }

    .btn-qty:hover {
        background: #e5e7eb;
    }

    .qty-valor {
        min-width: 20px;
        text-align: center;
        font-weight: 600;
    }

    .detalle-precio {
        font-size: 1.6rem;
        font-weight: 700;
        color: #000000;
        margin-bottom: 1.2rem;
    }

    .detalle-acciones {
        display: flex;
        gap: 0.8rem;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 1px solid #f3f4f6;
    }

    .btn-volver-accion {
        display: inline-block;
        padding: 0.65rem 1.1rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: #111827;
        background-color: #f3f4f6;
        border-radius: 6px;
        text-decoration: none;
        transition: background-color 0.2s ease;
    }

    .btn-volver-accion:hover {
        background-color: #e5e7eb;
    }

    .btn-carrito-accion {
        padding: 0.7rem 1.3rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: #ffffff;
        background-color: #000000;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.1s ease;
    }

    .btn-carrito-accion:hover {
        background-color: #d8af1d;
        color: #000000;
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

                    <!-- FORMULARIO POST PARA ENVIAR EL PLATO PERSONALIZADO AL CARRITO -->
                    <form action="?controller=carrito&action=add" method="POST" id="form-personalizar">
                        <!-- Enviamos el ID del producto de forma oculta -->
                        <input type="hidden" name="id_producto" value="<?= (int) $producto->getId() ?>">

                        <?php if (!empty($todosLosIngredientes)): ?>
                            <div class="ingredientes-box">
                                <h3>Personaliza tus ingredientes</h3>

                                <?php foreach ($todosLosIngredientes as $ing): ?>
                                    <?php
                                        $idIng = (int) $ing->getId();
                                        // Comprobamos si este ingrediente viene incluido de serie en este plato
                                        $esBase = in_array($idIng, $ingredientesDelPlato ?? []);
                                        // Si viene de serie empieza en 1; si es un extra opcional empieza en 0
                                        $cantidadInicial = $esBase ? 1 : 0;
                                    ?>
                                    <div class="ingrediente-fila" 
                                         data-precio="<?= (float) $ing->getPrecioExtra() ?>" 
                                         data-base="<?= $esBase ? 1 : 0 ?>">
                                        <div>
                                            <span class="ingrediente-nombre"><?= htmlspecialchars($ing->getNombre(), ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php if ($esBase): ?>
                                                <span class="ingrediente-tag tag-incluido">Incluido (+<?= number_format($ing->getPrecioExtra(), 2, ',', '.') ?> € extra)</span>
                                            <?php else: ?>
                                                <span class="ingrediente-tag tag-extra">+<?= number_format($ing->getPrecioExtra(), 2, ',', '.') ?> €</span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="ingrediente-controles">
                                            <!-- Botones type="button" para que NO envíen el formulario al hacer clic -->
                                            <button type="button" class="btn-qty" onclick="cambiarCantidad(<?= $idIng ?>, -1)">-</button>
                                            
                                            <!-- Span visible para el usuario -->
                                            <span class="qty-valor" id="vista-qty-<?= $idIng ?>"><?= $cantidadInicial ?></span>
                                            
                                            <button type="button" class="btn-qty" onclick="cambiarCantidad(<?= $idIng ?>, 1)">+</button>

                                            <!-- Input oculto que realmente viaja en $_POST['ingredientes'][ID] -->
                                            <input type="hidden" 
                                                   name="ingredientes[<?= $idIng ?>]" 
                                                   id="input-qty-<?= $idIng ?>" 
                                                   value="<?= $cantidadInicial ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Precio total dinámico (guardamos el precio base en data-base para calcular en JS) -->
                        <div class="detalle-precio">
                            Total: <span id="precio-total" data-base="<?= (float) $producto->getPrecio() ?>">
                                <?= number_format((float) $producto->getPrecio(), 2, ',', '.') ?>
                            </span> €
                        </div>

                        <div class="detalle-acciones">
                            <a href="?controller=producto" class="btn-volver-accion">Seguir viendo</a>
                            <button type="submit" class="btn-carrito-accion">Añadir al carrito</button>
                        </div>
                    </form>
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

<script src="js/producto-detalle.js"></script>