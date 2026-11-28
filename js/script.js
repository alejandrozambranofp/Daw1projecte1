document.addEventListener("DOMContentLoaded", () => {
    // Comprobamos que estamos dentro del panel de administración
    const panelAdmin = document.getElementById("tabla-ingredientes");
    if (!panelAdmin) return;

    // 1. Al entrar al panel, cargamos todas las tablas vía API (GET)
    cargarIngredientes();
    cargarProductos();
    cargarUsuarios();
    cargarPedidos();
});

// =========================================================================
// CAMBIO DE PESTAÑAS EN EL PANEL ADMIN
// =========================================================================
function cambiarPestanaAdmin(idPestana, botonPulsado) {
    document.querySelectorAll(".admin-tab-content").forEach(sec => sec.classList.remove("active"));
    document.querySelectorAll(".tab-btn").forEach(btn => btn.classList.remove("active"));

    document.getElementById(idPestana).classList.add("active");
    botonPulsado.classList.add("active");

    // Si entramos en la pestaña de pedidos, refrescamos la lista por si entró alguno nuevo
    if (idPestana === "tab-pedidos") {
        cargarPedidos();
    }
}