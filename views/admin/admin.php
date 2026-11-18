<style>
    .admin-panel {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1.5rem;
        color: #f4f1ea;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .admin-header {
        text-align: center;
        margin-bottom: 2.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #c5a059;
    }
    .admin-header h1 {
        color: #e6c27a;
        font-size: 2rem;
        margin-bottom: 0.4rem;
    }
    .admin-header p { color: #b8c2b9; }
    .admin-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 1.8rem;
        margin-bottom: 2rem;
    }
    @media (max-width: 900px) { .admin-grid { grid-template-columns: 1fr; } }
    .admin-card {
        background: linear-gradient(145deg, #16241d, #101b16);
        border: 1px solid rgba(197, 160, 89, 0.35);
        border-radius: 12px;
        padding: 1.6rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
    }
    .admin-card h2 {
        color: #e6c27a;
        font-size: 1.25rem;
        margin-bottom: 1.2rem;
        border-bottom: 1px solid rgba(197, 160, 89, 0.2);
        padding-bottom: 0.6rem;
    }
    .admin-form .form-group { margin-bottom: 1.2rem; }
    .admin-form label {
        display: block;
        margin-bottom: 0.4rem;
        color: #d6cfc2;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .admin-form input {
        width: 100%;
        padding: 0.7rem 0.9rem;
        background-color: #0b130f;
        border: 1px solid #3c5447;
        border-radius: 8px;
        color: #ffffff;
        box-sizing: border-box;
    }
    .admin-form input:focus {
        outline: none;
        border-color: #e6c27a;
    }
    .btn-hyrule {
        display: inline-block;
        padding: 0.7rem 1.3rem;
        border-radius: 8px;
        font-weight: 700;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }
    .btn-gold {
        width: 100%;
        background: linear-gradient(135deg, #d4af37, #aa8220);
        color: #101b16;
    }
    .hyrule-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .hyrule-table th {
        background-color: #0b130f;
        color: #e6c27a;
        padding: 0.85rem 1rem;
        font-size: 0.85rem;
        text-transform: uppercase;
        border-bottom: 2px solid #c5a059;
    }
    .hyrule-table td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        color: #e8e4dc;
    }
    .price-cell { color: #7ee0a8; font-weight: 700; }
    .badge-active {
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        font-size: 0.8rem;
        background-color: rgba(46, 125, 91, 0.25);
        color: #7ee0a8;
        border: 1px solid #38966e;
    }
    .btn-danger {
        background: rgba(192, 57, 43, 0.25);
        color: #f1948a;
        border: 1px solid #c0392b;
        padding: 0.35rem 0.7rem;
        border-radius: 6px;
        cursor: pointer;
    }
    #mensaje-api {
        margin-top: 1rem;
        font-weight: bold;
        text-align: center;
    }
</style>

<section class="admin-panel">
    <header class="admin-header">
        <h1>⚔️ Panel de Administración · Posada de Hyrule</h1>
        <p>Gestión dinámica mediante API REST (JSON + Fetch)</p>
    </header>

    <div class="admin-grid">
        <!-- BLOQUE 1: FORMULARIO CREAR INGREDIENTE (vía Fetch POST) -->
        <div class="admin-card">
            <h2>🌿 Añadir Nuevo Ingrediente</h2>
            <form id="form-ingrediente" class="admin-form">
                <div class="form-group">
                    <label for="nombre">Nombre del ingrediente</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ej: Trufa vivaz">
                </div>

                <div class="form-group">
                    <label for="precio_extra">Precio Extra (€)</label>
                    <input type="number" step="0.01" id="precio_extra" name="precio_extra" required value="0.50">
                </div>

                <button type="submit" class="btn-hyrule btn-gold">Guardar Ingrediente</button>
                <div id="mensaje-api"></div>
            </form>
        </div>

        <!-- BLOQUE 2: TABLA DE INGREDIENTES (se rellena con Fetch GET) -->
        <div class="admin-card">
            <h2>🧪 Ingredientes Disponibles</h2>
            <table class="hyrule-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio Extra</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-ingredientes">
                    <tr><td colspan="5">Cargando provisiones desde la API...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- BLOQUE 3: TABLA DE PRODUCTOS (se rellena con Fetch GET) -->
    <div class="admin-card">
        <h2>🍲 Carta de Productos</h2>
        <table class="hyrule-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Precio Base</th>
                </tr>
            </thead>
            <tbody id="tabla-productos">
                <tr><td colspan="3">Cargando platos desde la API...</td></tr>
            </tbody>
        </table>
    </div>
</section>

<script src="js/script.js"></script>