// Diccionario para traducir el categoria_id numérico de la BBDD a su nombre legible en la tabla HTML
const CATEGORIAS_MAP = {
    1: "Carnes",
    2: "Bebida",
    3: "Arroces",
    4: "Empanados",
    5: "Especiales"
};

// Cache en memoria para guardar los productos descargados por GET
let listaProductosCache = [];

document.addEventListener("DOMContentLoaded", () => {
    const formProducto = document.getElementById("form-producto");
    if (!formProducto) return;

    // Submit Productos (POST para crear / PUT para editar)
    formProducto.addEventListener("submit", async (e) => {
        e.preventDefault();

        const id = document.getElementById("prod-id").value;
        const categoria_id = document.getElementById("prod-categoria").value;
        const nombre = document.getElementById("prod-nombre").value;
        const descripcion = document.getElementById("prod-descripcion").value;
        const precio = document.getElementById("prod-precio").value;
        const imagen = document.getElementById("prod-imagen").value;
        const activo = document.getElementById("prod-activo").value;
        const mensajeDiv = document.getElementById("mensaje-api-prod");

        const esEdicion = id !== "";
        const url = esEdicion
            ? "index.php?controller=admin&action=api&function=editarProducto"
            : "index.php?controller=admin&action=api&function=crearProducto";
        const metodo = esEdicion ? "PUT" : "POST";

        try {
            const respuesta = await fetch(url, {
                method: metodo,
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id, categoria_id, nombre, descripcion, precio, imagen, activo })
            });

            const resultado = await respuesta.json();

            if (resultado.success) {
                mensajeDiv.style.color = "#7ee0a8";
                mensajeDiv.textContent = "✅ " + resultado.mensaje;
                resetFormProducto();
                cargarProductos();
            } else {
                mensajeDiv.style.color = "#f1948a";
                mensajeDiv.textContent = "❌ " + resultado.mensaje;
            }
        } catch (error) {
            console.error("Error al guardar producto:", error);
        }
    });
});

async function cargarProductos() {
    const tbody = document.getElementById("tabla-productos");
    if (!tbody) return;

    try {
        const respuesta = await fetch("index.php?controller=admin&action=api&function=productos");
        listaProductosCache = await respuesta.json();

        tbody.innerHTML = "";

        listaProductosCache.forEach((prod) => {
            const activo = Number(prod.activo) === 1;
            const badge = activo
                ? `<span class="badge-active">Activo</span>`
                : `<span class="badge-inactive">Inactivo</span>`;
            const textoToggle = activo ? "⏸️ Desactivar" : "▶️ Activar";
            const catNombre = CATEGORIAS_MAP[prod.categoria_id] || prod.categoria_id;

            tbody.innerHTML += `
                <tr>
                    <td>#${prod.id}</td>
                    <td>${catNombre}</td>
                    <td><strong>${prod.nombre}</strong></td>
                    <td class="price-cell">${Number(prod.precio).toFixed(2)} €</td>
                    <td>${badge}</td>
                    <td class="acciones-cell">
                        <button class="btn-sm btn-edit" onclick="prepararEdicionProducto(${prod.id})">✏️ Editar</button>
                        <button class="btn-sm btn-toggle" onclick="cambiarEstadoProducto(${prod.id}, ${activo ? 0 : 1})">${textoToggle}</button>
                        <button class="btn-sm btn-danger" onclick="eliminarProducto(${prod.id})">🗑️</button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error("Error cargando productos:", error);
        tbody.innerHTML = `<tr><td colspan="6">Error al conectar con la API</td></tr>`;
    }
}

function prepararEdicionProducto(id) {
    const prod = listaProductosCache.find((item) => Number(item.id) === Number(id));
    if (!prod) return;

    document.getElementById("prod-id").value = prod.id;
    document.getElementById("prod-categoria").value = prod.categoria_id;
    document.getElementById("prod-nombre").value = prod.nombre;
    document.getElementById("prod-descripcion").value = prod.descripcion || "";
    document.getElementById("prod-precio").value = prod.precio;
    document.getElementById("prod-imagen").value = prod.imagen || "default.png";
    document.getElementById("prod-activo").value = prod.activo;

    document.getElementById("titulo-form-producto").textContent = `✏️ Editando Producto #${prod.id}`;
    document.getElementById("btn-submit-prod").textContent = "Actualizar Producto";
    document.getElementById("btn-cancel-prod").style.display = "block";
    document.getElementById("mensaje-api-prod").textContent = "";
}

function resetFormProducto() {
    document.getElementById("form-producto").reset();
    document.getElementById("prod-id").value = "";
    document.getElementById("prod-activo").value = "1";
    document.getElementById("titulo-form-producto").textContent = "🍲 Añadir Producto";
    document.getElementById("btn-submit-prod").textContent = "Guardar Producto";
    document.getElementById("btn-cancel-prod").style.display = "none";
}

async function cambiarEstadoProducto(id, nuevoEstado) {
    try {
        const respuesta = await fetch("index.php?controller=admin&action=api&function=cambiarEstadoProducto", {
            method: "PATCH",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id, activo: nuevoEstado })
        });

        const resultado = await respuesta.json();
        if (resultado.success) cargarProductos();
    } catch (error) {
        console.error("Error al cambiar estado del producto:", error);
    }
}

async function eliminarProducto(id) {
    if (!confirm("¿Seguro que quieres eliminar definitivamente este producto de la base de datos?")) return;

    try {
        const respuesta = await fetch("index.php?controller=admin&action=api&function=eliminarProducto", {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id })
        });

        const resultado = await respuesta.json();
        if (resultado.success) {
            cargarProductos();
        } else {
            alert(resultado.mensaje);
        }
    } catch (error) {
        console.error("Error al eliminar producto:", error);
    }
}