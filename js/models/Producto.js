// Exportamos la clase Producto para usarla en el DAO y en admin.js
export class Producto {

    // Constructor que recibe los datos de cada plato desde el JSON de PHP
    constructor(datos) {
        this.id = datos.id;
        this.nombre = datos.nombre;
        this.descripcion = datos.descripcion;
        this.precioBase = parseFloat(datos.precio_base);
        this.imagen = datos.imagen;
        this.categoriaId = datos.categoria_id;
    }

    // Método que devuelve el HTML de la fila de la tabla para este plato
    crearFilaHTML() {
        // Formateamos el precio base con 2 decimales y coma en vez de punto para mostrarlo correctamente en la tabla
        let precioTexto = this.precioBase.toFixed(2).replace(".", ",") + " €";
        // Si el plato no tiene imagen en BBDD, ponemos una ruta por defecto para que no salga rota 
        let rutaImagen = this.imagen;
        if (!rutaImagen || rutaImagen === "") {
            rutaImagen = "assets/img/default.png";
        }

        // Devolvemos la fila <tr> lista para insertar en la tabla de platos
        return `
            <tr>
                <td>#${this.id}</td>
                <td><img src="${rutaImagen}" alt="${this.nombre}" width="50"></td>
                <td>${this.nombre}</td>
                <td class="price-cell">${precioTexto}</td>
                <td>
                    <a href="index.php?controller=producto&action=show&id=${this.id}" class="btn-secondary">
                        Ver plato
                    </a>
                </td>
            </tr>
        `;
    }
}