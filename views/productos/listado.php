<h1>Listado de Productos</h1>
<table>
    <tr>
        <th>Imagen</th>
        <th>Nombre</th>
        <th>Precio</th>
        <th>Acciones</th>
    </tr>
    <?php if (empty($productos)) { ?><tr>
            <td colspan="4">No hay productos todavía.</td>
        </tr>
    <?php } ?>
    <?php foreach ($productos as $producto) { ?>
        <tr>
            <td>
                <img src="imagenes/<?= htmlspecialchars($producto->getImagen(), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?>" width="70">
            </td>
            <td><?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= number_format((float) $producto->getPrecio(), 2, ',', '.') ?> €</td>
            <td>
                <a href="?controller=producto&action=show&id=<?= (int) $producto->getId() ?>">Ver</a>
            </td>
        </tr>
    <?php } ?>
</table>
