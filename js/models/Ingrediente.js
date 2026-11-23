// Clase modelo para guardar los datos de cada ingrediente en JavaScript
export class Ingrediente {

    // Constructor que recibe el objeto del JSON y guarda cada campo
    constructor(datos) {
        this.id = datos.id;
        this.nombre = datos.nombre;
        this.precioExtra = parseFloat(datos.precio_extra);
        this.activo = datos.activo;
    }

    // Método que construye el HTML de la fila de la tabla para este ingrediente
    crearFilaHTML(esFavorito) {
        // Preparamos el precio con 2 decimales y coma en vez de punto
        let precioTexto = this.precioExtra.toFixed(2).replace(".", ",") + " €";

        // Comprobamos si está marcado como favorito en localStorage para poner la estrella
        let estrella = "☆";
        let claseFav = "";
        if (esFavorito) {
            estrella = "★";
            claseFav = "activo";
        }

        // Devolvemos la fila <tr> montada con los datos del ingrediente
        return `
            <tr>
                <td>#${this.id}</td>
                <td>${this.nombre}</td>
                <td class="price-cell">+${precioTexto}</td>
                <td><span class="badge-active">Disponible</span></td>
                <td>
                    <button type="button" class="btn-fav ${claseFav}" data-id="${this.id}">${estrella}</button>
                    <button type="button" class="btn-danger btn-eliminar-ing" data-id="${this.id}">Eliminar</button>
                </td>
            </tr>
        `;
    }
}