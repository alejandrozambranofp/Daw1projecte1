// =========================================================================
// BLOQUE DE FUNCIONES PARA PEDIDOS (GET, PATCH, DELETE)
// =========================================================================
async function cargarPedidos() {
    const tbody = document.getElementById("tabla-pedidos");
    if (!tbody) return;

    try {
        const respuesta = await fetch("index.php?controller=admin&action=api&function=pedidos");
        const pedidos = await respuesta.json();

        if (!pedidos.length) {
            tbody.innerHTML = `<tr><td colspan="7">Todavía no hay pedidos registrados en la posada.</td></tr>`;
            return;
        }

        tbody.innerHTML = "";

        pedidos.forEach((ped) => {
            const codigoPedido = "HYR-" + String(ped.id).padStart(4, "0");
            const clienteInfo = ped.usuario_nombre
                ? `<strong>${ped.usuario_nombre}</strong><br><small style="color:#b8c2b9;">${ped.usuario_email}</small>`
                : `Usuario #${ped.usuario_id}`;

            let detallesHTML = "";
            if (ped.detalles && ped.detalles.length > 0) {
                ped.detalles.forEach((det) => {
                    let ingredientesHTML = "";
                    if (det.ingredientes && det.ingredientes.length > 0) {
                        const itemsIng = det.ingredientes.map((ing) => {
                            const extraTxt = Number(ing.precio_extra) > 0
                                ? ` <span style="color:#7ee0a8;">(+${Number(ing.precio_extra).toFixed(2)} €)</span>`
                                : "";
                            return `<li>${ing.nombre_ingrediente} — <em>${ing.accion}</em>${extraTxt}</li>`;
                        }).join("");
                        ingredientesHTML = `<ul class="pedido-ingredientes-list">${itemsIng}</ul>`;
                    }

                    detallesHTML += `
                        <div class="pedido-plato">
                            <strong>${det.cantidad}x ${det.nombre_producto}</strong>
                            <span style="float:right; color:#e6c27a;">${Number(det.subtotal).toFixed(2)} €</span>
                            ${ingredientesHTML}
                        </div>
                    `;
                });
            } else {
                detallesHTML = `<small>Sin líneas registradas</small>`;
            }

            const estados = ["pendiente", "preparando", "entregado", "cancelado"];
            const opcionesEstado = estados.map(est =>
                `<option value="${est}" ${ped.estado === est ? "selected" : ""}>${est.charAt(0).toUpperCase() + est.slice(1)}</option>`
            ).join("");

            tbody.innerHTML += `
                <tr class="fila-pedido"
                    data-id="${ped.id}"
                    data-codigo="${codigoPedido.toLowerCase()}"
                    data-nombre="${(ped.usuario_nombre || '').toLowerCase()}"
                    data-email="${(ped.usuario_email || '').toLowerCase()}"
                    data-estado="${(ped.estado || '').toLowerCase()}">
                    <td><strong>${codigoPedido}</strong></td>
                    <td>${clienteInfo}</td>
                    <td style="white-space:nowrap; font-size:0.85rem;">${ped.fecha_pedido}</td>
                    <td style="min-width:260px;">${detallesHTML}</td>
                    <td class="price-cell">${Number(ped.total).toFixed(2)} €</td>
                    <td>
                        <select class="select-estado-pedido" onchange="cambiarEstadoPedido(${ped.id}, this.value)">
                            ${opcionesEstado}
                        </select>
                    </td>
                    <td class="acciones-cell">
                        <button class="btn-sm btn-danger" onclick="eliminarPedido(${ped.id})" title="Eliminar pedido">🗑️</button>
                    </td>
                </tr>
            `;
        });

        filtrarPedidos();

    } catch (error) {
        console.error("Error cargando pedidos:", error);
        tbody.innerHTML = `<tr><td colspan="7">Error al conectar con la API de pedidos</td></tr>`;
    }

    
}

async function cambiarEstadoPedido(id, nuevoEstado) {
    const mensajeDiv = document.getElementById("mensaje-api-pedido");
    try {
        const respuesta = await fetch("index.php?controller=admin&action=api&function=cambiarEstadoPedido", {
            method: "PATCH",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id, estado: nuevoEstado })
        });
        const resultado = await respuesta.json();
        mensajeDiv.style.color = resultado.success ? "#7ee0a8" : "#f1948a";
        mensajeDiv.textContent = (resultado.success ? "✅ " : "❌ ") + resultado.mensaje;
    } catch (error) {
        console.error("Error cambiando estado del pedido:", error);
    }
}

async function eliminarPedido(id) {
    if (!confirm(`¿Seguro que quieres eliminar el pedido #${id} y todos sus platos asociados?`)) return;

    const mensajeDiv = document.getElementById("mensaje-api-pedido");
    try {
        const respuesta = await fetch("index.php?controller=admin&action=api&function=eliminarPedido", {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        });
        const resultado = await respuesta.json();
        mensajeDiv.style.color = resultado.success ? "#7ee0a8" : "#f1948a";
        mensajeDiv.textContent = (resultado.success ? "✅ " : "❌ ") + resultado.mensaje;
        if (resultado.success) cargarPedidos();
    } catch (error) {
        console.error("Error eliminando pedido:", error);
    }
}

function normalizarTexto(texto) {
    return (texto || "")
        .toLowerCase()
        .trim()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");
}

function filtrarPedidos() {
    const inputBusqueda = document.getElementById("buscador-pedidos");
    const selectEstado = document.getElementById("filtro-estado-pedidos");
    const contador = document.getElementById("contador-pedidos");
    const tbody = document.getElementById("tabla-pedidos");
    if (!tbody) return;

    const filas = tbody.querySelectorAll("tr.fila-pedido");
    const textoBuscado = inputBusqueda ? normalizarTexto(inputBusqueda.value).replace(/^#/, "") : "";
    const estadoBuscado = selectEstado ? normalizarTexto(selectEstado.value) : "";

    let visibles = 0;

    filas.forEach((fila) => {
        const idPed  = normalizarTexto(fila.dataset.id);
        const codigo = normalizarTexto(fila.dataset.codigo);
        const nombre = normalizarTexto(fila.dataset.nombre);
        const email  = normalizarTexto(fila.dataset.email);
        const estado = normalizarTexto(fila.dataset.estado);

        const coincideTexto = textoBuscado === "" ||
            idPed.includes(textoBuscado) ||
            codigo.includes(textoBuscado) ||
            nombre.includes(textoBuscado) ||
            email.includes(textoBuscado);

        const coincideEstado = estadoBuscado === "" || estado === estadoBuscado;

        if (coincideTexto && coincideEstado) {
            fila.style.display = "";
            visibles++;
        } else {
            fila.style.display = "none";
        }
    });

    let filaVacia = document.getElementById("fila-sin-resultados");
    if (visibles === 0 && filas.length > 0) {
        if (!filaVacia) {
            filaVacia = document.createElement("tr");
            filaVacia.id = "fila-sin-resultados";
            filaVacia.innerHTML = `<td colspan="7" style="text-align:center; padding:16px; color:#b8c2b9;">No hay pedidos que coincidan con los filtros aplicados.</td>`;
            tbody.appendChild(filaVacia);
        }
    } else if (filaVacia) {
        filaVacia.remove();
    }

    if (contador) {
        contador.textContent = `Mostrando ${visibles} de ${filas.length} pedidos`;
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const inputBusqueda = document.getElementById("buscador-pedidos");
    const selectEstado = document.getElementById("filtro-estado-pedidos");
    const btnLimpiar = document.getElementById("btn-limpiar-pedidos");

    if (inputBusqueda) inputBusqueda.addEventListener("input", filtrarPedidos);
    if (selectEstado) selectEstado.addEventListener("change", filtrarPedidos);
    if (btnLimpiar) {
        btnLimpiar.addEventListener("click", () => {
            if (inputBusqueda) inputBusqueda.value = "";
            if (selectEstado) selectEstado.value = "";
            filtrarPedidos();
        });
    }
});