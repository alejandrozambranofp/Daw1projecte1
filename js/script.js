// Diccionario para traducir el categoria_id numérico de la BBDD a su nombre legible en la tabla HTML
const CATEGORIAS_MAP = {
    1: "Carnes",
    2: "Bebida",
    3: "Arroces",
    4: "Empanados",
    5: "Especiales"
};

// Variables globales en memoria para guardar las listas descargadas por GET.
let listaIngredientesCache = [];
let listaProductosCache = [];
let listaUsuariosCache = [];

document.addEventListener("DOMContentLoaded", () => {

    const formIngrediente = document.getElementById("form-ingrediente");
    if (!formIngrediente) return;

    // 1. Nada más entrar al panel, rellenamos las 3 tablas vía API (GET)
    cargarIngredientes();
    cargarProductos();
    cargarUsuarios();

    // 2. Submit Ingredientes (POST / PUT)
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

    // 3. Submit Productos (POST / PUT)
    const formProducto = document.getElementById("form-producto");
    if (formProducto) {
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
    }

    // 4. Submit Usuarios (POST / PUT)
    const formUsuario = document.getElementById("form-usuario");
    if (formUsuario) {
        formUsuario.addEventListener("submit", async (e) => {
            e.preventDefault();

            const id = document.getElementById("usuario-id").value;
            const nombre = document.getElementById("usuario-nombre").value;
            const email = document.getElementById("usuario-email").value;
            const password = document.getElementById("usuario-contrasena").value;
            const rol = document.getElementById("usuario-rol").value;
            const activo = document.getElementById("usuario-activo").value;
            const mensajeDiv = document.getElementById("mensaje-api-usuario");

            const esEdicion = id !== "";
            const url = esEdicion
                ? "index.php?controller=admin&action=api&function=editarUsuario"
                : "index.php?controller=admin&action=api&function=crearUsuario";
            const metodo = esEdicion ? "PUT" : "POST";

            try {
                const respuesta = await fetch(url, {
                    method: metodo,
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id, nombre, email, password, rol, activo })
                });

                const resultado = await respuesta.json();

                if (resultado.success) {
                    mensajeDiv.style.color = "#7ee0a8";
                    mensajeDiv.textContent = "✅ " + resultado.mensaje;
                    resetFormUsuario();
                    cargarUsuarios();
                } else {
                    mensajeDiv.style.color = "#f1948a";
                    mensajeDiv.textContent = "❌ " + resultado.mensaje;
                }
            } catch (error) {
                console.error("Error al guardar usuario:", error);
            }
        });
    }
});

// =========================================================================
// BLOQUE DE FUNCIONES PARA INGREDIENTES (GET, PUT, PATCH, DELETE)
// =========================================================================

async function cargarIngredientes() {
    const tbody = document.getElementById("tabla-ingredientes");

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

// =========================================================================
// BLOQUE DE FUNCIONES PARA PRODUCTOS (GET, PUT, PATCH, DELETE)
// =========================================================================

async function cargarProductos() {
    const tbody = document.getElementById("tabla-productos");

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

// =========================================================================
// BLOQUE DE FUNCIONES PARA USUARIOS (GET, PUT, PATCH, DELETE)
// =========================================================================

async function cargarUsuarios() {
    const tbody = document.getElementById("tabla-usuarios");

    try {
        const respuesta = await fetch("index.php?controller=admin&action=api&function=usuarios");
        listaUsuariosCache = await respuesta.json();

        tbody.innerHTML = "";

        listaUsuariosCache.forEach((usr) => {
            const activo = Number(usr.activo ?? 1) === 1;
            const badge = activo
                ? `<span class="badge-active">Activo</span>`
                : `<span class="badge-inactive">Bloqueado</span>`;
            const textoToggle = activo ? "⏸️ Bloquear" : "▶️ Activar";
            const rolBadge = usr.rol === "admin" ? "👑 Admin" : "🧝 Usuario";

            tbody.innerHTML += `
                <tr>
                    <td>#${usr.id}</td>
                    <td><strong>${usr.nombre}</strong></td>
                    <td>${usr.email}</td>
                    <td>${rolBadge}</td>
                    <td>${badge}</td>
                    <td class="acciones-cell">
                        <button class="btn-sm btn-edit" onclick="prepararEdicionUsuario(${usr.id})">✏️ Editar</button>
                        <button class="btn-sm btn-toggle" onclick="cambiarEstadoUsuario(${usr.id}, ${activo ? 0 : 1})">${textoToggle}</button>
                        <button class="btn-sm btn-danger" onclick="eliminarUsuario(${usr.id})">🗑️</button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error("Error cargando usuarios:", error);
        tbody.innerHTML = `<tr><td colspan="6">Error al conectar con la API</td></tr>`;
    }
}

function prepararEdicionUsuario(id) {
    const usr = listaUsuariosCache.find((item) => Number(item.id) === Number(id));
    if (!usr) return;

    document.getElementById("usuario-id").value = usr.id;
    document.getElementById("usuario-nombre").value = usr.nombre;
    document.getElementById("usuario-email").value = usr.email;
    document.getElementById("usuario-rol").value = usr.rol;
    document.getElementById("usuario-activo").value = usr.activo ?? 1;

    // Al editar, la contraseña deja de ser obligatoria (solo se cambia si escribes una nueva)
    const inputPass = document.getElementById("usuario-contrasena");
    inputPass.value = "";
    inputPass.required = false;

    document.getElementById("titulo-form-usuario").textContent = `✏️ Editando Usuario #${usr.id}`;
    document.getElementById("btn-submit-usuario").textContent = "Actualizar Usuario";
    document.getElementById("btn-cancel-usuario").style.display = "block";
    document.getElementById("mensaje-api-usuario").textContent = "";
}

function resetFormUsuario() {
    document.getElementById("form-usuario").reset();
    document.getElementById("usuario-id").value = "";
    document.getElementById("usuario-activo").value = "1";
    document.getElementById("usuario-contrasena").required = true;
    document.getElementById("titulo-form-usuario").textContent = "👤 Añadir Usuario";
    document.getElementById("btn-submit-usuario").textContent = "Guardar Usuario";
    document.getElementById("btn-cancel-usuario").style.display = "none";
}

async function cambiarEstadoUsuario(id, nuevoEstado) {
    try {
        const respuesta = await fetch("index.php?controller=admin&action=api&function=cambiarEstadoUsuario", {
            method: "PATCH",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id, activo: nuevoEstado })
        });

        const resultado = await respuesta.json();
        if (resultado.success) {
            cargarUsuarios();
        } else {
            alert(resultado.mensaje);
        }
    } catch (error) {
        console.error("Error al cambiar estado del usuario:", error);
    }
}

async function eliminarUsuario(id) {
    if (!confirm("¿Seguro que quieres eliminar definitivamente este usuario de la base de datos?")) return;

    try {
        const respuesta = await fetch("index.php?controller=admin&action=api&function=eliminarUsuario", {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id })
        });

        const resultado = await respuesta.json();
        if (resultado.success) {
            cargarUsuarios();
        } else {
            alert(resultado.mensaje);
        }
    } catch (error) {
        console.error("Error al eliminar usuario:", error);
    }
}