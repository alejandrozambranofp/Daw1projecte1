// Esperamos a que todo el árbol HTML (DOM) esté completamente cargado antes de intentar buscar IDs como 'tabla-ingredientes'
document.addEventListener("DOMContentLoaded", () => {

    // Comprobamos si estamos realmente en la página del panel de admin (buscando si existe el formulario)
    // Así evitamos errores en consola si script.js también se carga en la portada o en la carta
    const formIngrediente = document.getElementById("form-ingrediente");
    if (!formIngrediente) return;

    // 1. Nada más entrar al panel, llamamos a estas dos funciones para rellenar las tablas vía API
    cargarIngredientes();
    cargarProductos();

    // 2. Escuchamos el evento 'submit' del formulario de crear ingrediente
    formIngrediente.addEventListener("submit", async (e) => {
        // Evitamos el comportamiento por defecto del navegador (que recargaría toda la página)
        e.preventDefault();

        // Recogemos los valores escritos por el usuario en los inputs del formulario
        const nombre = document.getElementById("nombre").value;
        const precio_extra = document.getElementById("precio_extra").value;
        const mensajeDiv = document.getElementById("mensaje-api");

        try {
            // Hacemos una petición POST asíncrona a nuestra API PHP enviando los datos en el cuerpo (body) como texto JSON
            const respuesta = await fetch("index.php?controller=admin&action=api&function=crearIngrediente", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json" // Avisamos a PHP de que le enviamos JSON
                },
                body: JSON.stringify({ nombre, precio_extra }) // Convertimos el objeto JS a cadena de texto JSON
            });

            // Convertimos la respuesta JSON que devuelve PHP a un objeto manipulable en JavaScript
            const resultado = await respuesta.json();

            if (resultado.success) {
                // Mostramos mensaje de éxito en verde y limpiamos el formulario
                mensajeDiv.style.color = "#7ee0a8";
                mensajeDiv.textContent = "✅ " + resultado.mensaje;
                formIngrediente.reset();

                // Volvemos a pedir los ingredientes a la API para actualizar la tabla al instante sin recargar
                cargarIngredientes();
            } else {
                mensajeDiv.style.color = "#f1948a";
                mensajeDiv.textContent = "❌ " + resultado.mensaje;
            }
        } catch (error) {
            console.error("Error al crear ingrediente:", error);
        }
    });
});

/**
 * Función asíncrona que pide los ingredientes activos por GET y dibuja la tabla
 */
async function cargarIngredientes() {
    const tbody = document.getElementById("tabla-ingredientes");

    try {
        // Llamamos a la URL del endpoint GET de ingredientes
        const respuesta = await fetch("index.php?controller=admin&action=api&function=ingredientes");

        // Parseamos el JSON recibido a un Array de objetos JavaScript
        const ingredientes = await respuesta.json();

        // Vaciamos el mensaje inicial de "Cargando provisiones desde la API..."
        tbody.innerHTML = "";

        // Recorremos cada ingrediente del array y construimos su fila HTML (<tr>)
        ingredientes.forEach((ing) => {
            tbody.innerHTML += `
                <tr>
                    <td>#${ing.id}</td>
                    <td><strong>${ing.nombre}</strong></td>
                    <td class="price-cell">+${Number(ing.precio_extra).toFixed(2)} €</td>
                    <td><span class="badge-active">Activo</span></td>
                    <td>
                        <button class="btn-danger" onclick="eliminarIngrediente(${ing.id})">🗑️ Quitar</button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error("Error cargando ingredientes:", error);
        tbody.innerHTML = `<tr><td colspan="5">Error al conectar con la API</td></tr>`;
    }
}

/**
 * Función asíncrona que pide los productos por GET y dibuja la tabla de la carta
 */
async function cargarProductos() {
    const tbody = document.getElementById("tabla-productos");

    try {
        // Llamamos al endpoint GET de productos
        const respuesta = await fetch("index.php?controller=admin&action=api&function=productos");
        const productos = await respuesta.json();

        tbody.innerHTML = "";

        productos.forEach((prod) => {
            tbody.innerHTML += `
                <tr>
                    <td>#${prod.id}</td>
                    <td><strong>${prod.nombre}</strong></td>
                    <td class="price-cell">${Number(prod.precio).toFixed(2)} €</td>
                </tr>
            `;
        });
    } catch (error) {
        console.error("Error cargando productos:", error);
        tbody.innerHTML = `<tr><td colspan="3">Error al conectar con la API</td></tr>`;
    }
}

/**
 * Función que envía por POST el ID del ingrediente a desactivar (borrado lógico)
 */
async function eliminarIngrediente(id) {
    if (!confirm("¿Seguro que quieres retirar este ingrediente de la posada?")) return;

    try {
        const respuesta = await fetch("index.php?controller=admin&action=api&function=eliminarIngrediente", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id })
        });

        const resultado = await respuesta.json();

        if (resultado.success) {
            // Si PHP confirma el borrado lógico, refrescamos la tabla automáticamente
            cargarIngredientes();
        }
    } catch (error) {
        console.error("Error al eliminar ingrediente:", error);
    }
}