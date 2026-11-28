// Cache en memoria para guardar los usuarios descargados por GET
let listaUsuariosCache = [];

document.addEventListener("DOMContentLoaded", () => {
    const formUsuario = document.getElementById("form-usuario");
    if (!formUsuario) return;

    // Submit Usuarios (POST para crear / PUT para editar)
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
});

async function cargarUsuarios() {
    const tbody = document.getElementById("tabla-usuarios");
    if (!tbody) return;

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