<style>
    /* Reset local y estilos generales */
    .contenedor-principal {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f7f7f7;
        color: #333;
        padding: 20px;
    }

    /* Breadcrumbs y Cabecera */
    .breadcrumbs {
        font-size: 12px;
        color: #777;
        margin-bottom: 15px;
    }
    
    .cabecera-carta {
        background: #fff;
        padding: 25px 30px;
        border-radius: 4px;
        margin-bottom: 30px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .cabecera-carta h1 {
        margin: 0 0 10px 0;
        font-size: 28px;
        font-weight: 800;
    }
    .cabecera-carta p {
        margin: 0;
        color: #555;
        font-size: 14px;
    }

    /* Layout Principal (Sidebar + Grid) */
    .layout-catalogo {
        display: flex;
        gap: 25px;
    }

    /* Sidebar de Filtros (Solo visual) */
    .sidebar-filtros {
        width: 240px;
        flex-shrink: 0;
    }
    .sidebar-filtros .conteo-resultados {
        font-size: 13px;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .sidebar-filtros h3 {
        font-size: 14px;
        margin-bottom: 15px;
    }
    .campo-filtro {
        margin-bottom: 12px;
    }
    .campo-filtro select, 
    .campo-filtro input[type="text"] {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 13px;
        background-color: #fff;
    }
    .btn-ofertas-banner {
        background-color: #eab339;
        color: #fff;
        padding: 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: bold;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .opcion-checkbox {
        font-size: 12px;
        color: #666;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 15px;
    }
    .btn-aplicar-filtro {
        width: 100%;
        background-color: #eab339;
        border: none;
        color: #fff;
        padding: 10px;
        font-weight: bold;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }

    /* Grid de Productos */
    .grid-productos {
        flex-grow: 1;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    /* Tarjeta Banner Promocional */
    .tarjeta-banner-promo {
        background-color: #eab339;
        color: #fff;
        border-radius: 6px;
        padding: 25px 20px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
    }
    .tarjeta-banner-promo h2 {
        font-size: 22px;
        line-height: 1.2;
        margin-bottom: 15px;
    }
    .tarjeta-banner-promo p {
        font-size: 13px;
        line-height: 1.4;
        opacity: 0.9;
    }

    /* Tarjeta de Producto */
    .tarjeta-producto {
        background: #fff;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .imagen-wrapper {
        position: relative;
        width: 100%;
        background-color: #eee;
    }
    .imagen-wrapper img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }
    .badge-nuevo {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: #000;
        color: #fff;
        font-size: 10px;
        text-align: center;
        padding: 3px 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .info-producto {
        padding: 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .categoria-tag {
        font-size: 10px;
        color: #888;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    .titulo-producto {
        font-size: 16px;
        font-weight: bold;
        margin: 0 0 10px 0;
    }
    .label-ingredientes {
        font-size: 9px;
        font-weight: bold;
        color: #999;
        margin-bottom: 3px;
    }
    .descripcion-producto {
        font-size: 10px;
        color: #555;
        text-transform: uppercase;
        line-height: 1.3;
        margin-bottom: 15px;
        flex-grow: 1;
    }
    .footer-tarjeta {
        border-top: 1px solid #eee;
        padding-top: 10px;
    }
    .uds-info {
        font-size: 10px;
        color: #aaa;
    }
    .precio-info {
        font-size: 12px;
        font-weight: bold;
        color: #111;
    }

    .tarjeta-acciones {
        display: flex;
        justify-content: flex-end; /* Alinea el botón a la derecha */
        align-items: center;
        margin-top: auto; /* Si la tarjeta usa flex column, empuja el botón al fondo */
        padding-top: 0.5rem;
    }

    .btn-ver {
        display: inline-block;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: #ffffff;
        background-color: #000000;
        border-radius: 6px;
        text-decoration: none;
        text-align: center; 
        transition: background-color 0.2s ease, transform 0.1s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-ver:hover {
        background-color: #d8af1d;
        transform: translateY(-1px);
    }
</style>

<div class="contenedor-principal">
    
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        Inicio / Menú / Ver todos los productos
    </div>

    <!-- Cabecera Informativa -->
    <div class="cabecera-carta">
        <h1>Ver todos los productos de The Link's Recipe</h1>
        <p>Explora todo el catálogo de recetas de Link recreadas en la vida real. ¡Disfruta de envío gratuito para pedidos de más de 20,000 €.</p>
    </div>

    <div class="layout-catalogo">

        <!-- Sidebar de Filtros (Estático / Solo diseño) -->
        <aside class="sidebar-filtros">
            <div class="conteo-resultados">
                <?= count($productos) ?> Resultados
            </div>
            <h3>Filtrar y ordenar</h3>

            <div class="campo-filtro">
                <select disabled>
                    <option>Mejores resultados</option>
                </select>
            </div>

            <div class="btn-ofertas-banner">
                <input type="checkbox" disabled checked>
                <span>Mostrar solo los productos que contengan: Ofertas</span>
            </div>

            <div class="opcion-checkbox">
                <input type="checkbox" disabled>
                <span>Ocultar los productos agotados</span>
            </div>

            <div class="campo-filtro">
                <select disabled><option>Precio</option></select>
            </div>
            <div class="campo-filtro">
                <select disabled><option>Región</option></select>
            </div>
            <div class="campo-filtro">
                <select disabled><option>Tipo de receta</option></select>
            </div>
            <div class="campo-filtro">
                <select disabled><option>Ingredientes</option></select>
            </div>
            <div class="campo-filtro">
                <select disabled><option>Valoraciones</option></select>
            </div>

            <button class="btn-aplicar-filtro" type="button">APLICAR Y VER</button>
        </aside>

        <!-- Rejilla de Productos -->
        <main class="grid-productos">

            <?php if (empty($productos)): ?>
                <p>No hay productos disponibles.</p>
            <?php else: ?>

                <?php 
                $contador = 0;
                foreach ($productos as $producto): 
                    $contador++;
                ?>
                    
                    <!-- Tarjeta Promocional en la posición 2 del Grid -->
                    <?php if ($contador === 2): ?>
                        <div class="tarjeta-banner-promo">
                            <h2>¡Descubre el sabor de las recetas de Link!</h2>
                            <p>¡Disfruta de el sabor real de cada una de las recetas que prepara Link en el juego!</p>
                        </div>
                    <?php endif; ?>

                    <!-- Tarjeta de Producto Dinámica -->
                    <div class="tarjeta-producto">
                        <div class="imagen-wrapper">
                            <img src="imagenes/<?= htmlspecialchars($producto->getImagen(), ENT_QUOTES, 'UTF-8') ?>" 
                                 alt="<?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?>">
                            <div class="badge-nuevo">NUEVO</div>
                        </div>

                        <div class="info-producto">
                            <span class="categoria-tag">Receta</span>
                            <h3 class="titulo-producto"><?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?></h3>
                            
                            <div class="label-ingredientes">INGREDIENTES:</div>
                            <div class="descripcion-producto">
                                <?= htmlspecialchars($producto->getDescripcion(), ENT_QUOTES, 'UTF-8') ?>
                            </div>

                            <div class="footer-tarjeta">
                                <div class="uds-info">1 UDS</div>
                                <div class="precio-info">
                                    <?= number_format((float)$producto->getPrecio(), 2, ',', '.') ?>€ <span style="font-size: 9px; font-weight: normal; color: #777;">(IVA INCLUIDO)</span>
                                </div>
                                <div class="tarjeta-acciones">
                                    <a href="?controller=producto&action=show&id=<?= (int) $producto->getId() ?>" class="btn-ver">
                                        Ver
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </main>
    </div>
</div>