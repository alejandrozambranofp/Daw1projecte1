<h1>Detalle del producto</h1>
<p><a href="?controller=producto">Volver al listado</a></p>

<p><strong>Nombre:</strong> <?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?></p>
<p><strong>Descripcion:</strong> <?= htmlspecialchars($producto->getDescripcion(), ENT_QUOTES, 'UTF-8') ?></p>
<p><strong>Precio:</strong> <?= number_format((float) $producto->getPrecio(), 2, ',', '.') ?> €</p>
<p>
    <img src="imagenes/<?= htmlspecialchars($producto->getImagen(), ENT_QUOTES, 'UTF-8') ?>" 
         alt="<?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?>" 
         width="200">
</p>