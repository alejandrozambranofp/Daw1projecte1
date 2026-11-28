// Cache en memoria para guardar los ingredientes descargados por GET
let listaIngredientesCache = [];

document.addEventListener("DOMContentLoaded", () => {
    const formIngrediente = document.getElementById("form-ingrediente");
    if (!formIngrediente) return;

    // Submit Ingredientes (POST para crear / PUT para editar)
    formIngrediente.addEventListener("submit", async (e) => {
        e.preventDefault();

        const id = document.getElementById("ing-id").value;
        const nombre = document.getElementById("ing-nombre").value;
        const precio_extra = document.getElementById("ing-precio").value;
        const mensajeDiv = document.getElementById("mensaje-api-ing");

        const esEdicion = id !== "";
        const url = esEdicion
            ? "index.php?controller=admin&action=api&function=editarIngrediente"
            : "index.php?controller=admin&action=api&function=crearIngrediente";
        const metodo = esEdicion ? "PUT" : "POST";

        try {
            const respuesta = await fetch(url, {
                method: metodo,
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id, nombre, precio_extra })
            });

            const resultado = await respuesta.json();

            if (resultado.success) {
                mensajeDiv.style.color = "#7ee0a8";
                mensajeDiv.textContent = "✅ " + resultado.mensaje;
                resetFormIngrediente();
                cargarIngredientes();
            } else {
                mensajeDiv.style.color = "#f1948a";
                mensajeDiv.textContent = "❌ " + resultado.mensaje;
            }
        } catch (error) {
            console.error("Error al guardar ingrediente:", error);
        }
    });
});

async function cargarIngredientes() {
    const tbody = document.getElementById("tabla-ingredientes");
    if (!tbody) return;

    try {
        const respuesta = await fetch("index.php?controller=admin&action=api&function=ingredientes");
        listaIngredientesCache = await respuesta.json();

        tbody.innerHTML = "";

        listaIngredientesCache.forEach((ing) => {
            const activo = Number(ing.activo) === 1;
            const badge = activo
                ? `<span class="badge-active">Activo</span>`
                : `<span class="badge-inactive">Inactivo</span>`;
            const textoToggle = activo ? "⏸️ Desactivar" : "▶️ Activar";

            tbody.innerHTML += `
                <tr>
                    <td>#${ing.id}</td>
                    <td><strong>${ing.nombre}</strong></td>
                    <td class="price-cell">+${Number(ing.precio_extra).toFixed(2)} €</td>
                    <td>${badge}</td>
                    <td class="acciones-cell">
                        <button class="btn-sm btn-edit" onclick="prepararEdicionIngrediente(${ing.id})">✏️ Editar</button>
                        <button class="btn-sm btn-toggle" onclick="cambiarEstadoIngrediente(${ing.id}, ${activo ? 0 : 1})">${textoToggle}</button>
                        <button class="btn-sm btn-danger" onclick="eliminarIngrediente(${ing.id})">🗑️</button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error("Error cargando ingredientes:", error);
        tbody.innerHTML = `<tr><td colspan="5">Error al conectar con la API</td></tr>`;
    }
}

function prepararEdicionIngrediente(id) {
    const ing = listaIngredientesCache.find((item) => Number(item.id) === Number(id));
    if (!ing) return;

    document.getElementById("ing-id").value = ing.id;
    document.getElementById("ing-nombre").value = ing.nombre;
    document.getElementById("ing-precio").value = ing.precio_extra;

    document.getElementById("titulo-form-ingrediente").textContent = `✏️ Editando Ingrediente #${ing.id}`;
    document.getElementById("btn-submit-ing").textContent = "Actualizar Ingrediente";
    document.getElementById("btn-cancel-ing").style.display = "block";
    document.getElementById("mensaje-api-ing").textContent = "";
}

function resetFormIngrediente() {
    document.getElementById("form-ingrediente").reset();
    document.getElementById("ing-id").value = "";
    document.getElementById("titulo-form-ingrediente").textContent = "🌿 Añadir Ingrediente";
    document.getElementById("btn-submit-ing").textContent = "Guardar Ingrediente";
    document.getElementById("btn-cancel-ing").style.display = "none";
}

async function cambiarEstadoIngrediente(id, nuevoEstado) {
    try {
        const respuesta = await fetch("index.php?controller=admin&action=api&function=cambiarEstadoIngrediente", {
            method: "PATCH",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id, activo: nuevoEstado })
        });

        const resultado = await respuesta.json();
        if (resultado.success) cargarIngredientes();
    } catch (error) {
        console.error("Error al cambiar estado del ingrediente:", error);
    }
}

async function eliminarIngrediente(id) {
    if (!confirm("¿Seguro que quieres eliminar definitivamente este ingrediente de la base de datos?")) return;

    try {
        const respuesta = await fetch("index.php?controller=admin&action=api&function=eliminarIngrediente", {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id })
        });

        const resultado = await respuesta.json();
        if (resultado.success) {
            cargarIngredientes();
        } else {
            alert(resultado.mensaje);
        }
    } catch (error) {
        console.error("Error al eliminar ingrediente:", error);
    }
}