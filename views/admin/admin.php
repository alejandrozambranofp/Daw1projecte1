<style>
    .admin-panel {
        max-width: 1280px;
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
    .admin-header h1 { color: #e6c27a; font-size: 2rem; margin-bottom: 0.4rem; }
    .admin-header p { color: #b8c2b9; }
    .admin-grid {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 1.8rem;
        margin-bottom: 2.5rem;
        align-items: start;
    }
    @media (max-width: 960px) { .admin-grid { grid-template-columns: 1fr; } }
    .admin-card {
        background: linear-gradient(145deg, #16241d, #101b16);
        border: 1px solid rgba(197, 160, 89, 0.35);
        border-radius: 12px;
        padding: 1.6rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
        overflow-x: auto;
    }
    .admin-card h2 {
        color: #e6c27a;
        font-size: 1.25rem;
        margin-bottom: 1.2rem;
        border-bottom: 1px solid rgba(197, 160, 89, 0.2);
        padding-bottom: 0.6rem;
    }
    .admin-form .form-group { margin-bottom: 1rem; }
    .admin-form label {
        display: block;
        margin-bottom: 0.35rem;
        color: #d6cfc2;
        font-weight: 600;
        font-size: 0.88rem;
    }
    .admin-form input, .admin-form select, .admin-form textarea {
        width: 100%;
        padding: 0.65rem 0.85rem;
        background-color: #0b130f;
        border: 1px solid #3c5447;
        border-radius: 8px;
        color: #ffffff;
        box-sizing: border-box;
        font-family: inherit;
    }
    .admin-form input:focus, .admin-form select:focus, .admin-form textarea:focus {
        outline: none;
        border-color: #e6c27a;
    }
    .btn-hyrule {
        display: inline-block;
        padding: 0.7rem 1.2rem;
        border-radius: 8px;
        font-weight: 700;
        border: none;
        cursor: pointer;
    }
    .btn-gold {
        width: 100%;
        background: linear-gradient(135deg, #d4af37, #aa8220);
        color: #101b16;
    }
    .btn-cancel {
        width: 100%;
        margin-top: 0.5rem;
        background: #2c3e35;
        color: #d6cfc2;
        display: none;
    }
    .hyrule-table { width: 100%; border-collapse: collapse; text-align: left; }
    .hyrule-table th {
        background-color: #0b130f;
        color: #e6c27a;
        padding: 0.8rem;
        font-size: 0.82rem;
        text-transform: uppercase;
        border-bottom: 2px solid #c5a059;
    }
    .hyrule-table td {
        padding: 0.75rem 0.8rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        color: #e8e4dc;
        vertical-align: middle;
    }
    .price-cell { color: #7ee0a8; font-weight: 700; white-space: nowrap; }
    .badge-active {
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.78rem;
        background-color: rgba(46, 125, 91, 0.25);
        color: #7ee0a8;
        border: 1px solid #38966e;
    }
    .badge-inactive {
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.78rem;
        background-color: rgba(149, 165, 166, 0.2);
        color: #bdc3c7;
        border: 1px solid #7f8c8d;
    }
    .acciones-cell { display: flex; gap: 0.4rem; flex-wrap: nowrap; }
    .btn-sm {
        padding: 0.35rem 0.6rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.82rem;
        border: 1px solid transparent;
        white-space: nowrap;
    }
    .btn-edit { background: rgba(230, 194, 122, 0.2); color: #e6c27a; border-color: #c5a059; }
    .btn-toggle { background: rgba(52, 152, 219, 0.2); color: #85c1e9; border-color: #2980b9; }
    .btn-danger { background: rgba(192, 57, 43, 0.25); color: #f1948a; border-color: #c0392b; }
    .msg-api { margin-top: 0.8rem; font-weight: bold; text-align: center; font-size: 0.9rem; }
</style>

<section class="admin-panel">
    <header class="admin-header">
        <h1>⚔️ Panel de Administración · Posada de Hyrule</h1>
        <p>CRUD completo mediante API REST (GET · POST · PUT · PATCH · DELETE)</p>
    </header>

    <!-- SECCIÓN 1: GESTIÓN DE INGREDIENTES -->
    <div class="admin-grid">
        <div class="admin-card">
            <h2 id="titulo-form-ingrediente">🌿 Añadir Ingrediente</h2>
            <form id="form-ingrediente" class="admin-form">
                <input type="hidden" id="ing-id" value="">

                <div class="form-group">
                    <label for="ing-nombre">Nombre del ingrediente</label>
                    <input type="text" id="ing-nombre" required placeholder="Ej: Trufa vivaz">
                </div>

                <div class="form-group">
                    <label for="ing-precio">Precio Extra (€)</label>
                    <input type="number" step="0.01" min="0" id="ing-precio" required value="0.50">
                </div>

                <button type="submit" id="btn-submit-ing" class="btn-hyrule btn-gold">Guardar Ingrediente</button>
                <button type="button" id="btn-cancel-ing" class="btn-hyrule btn-cancel" onclick="resetFormIngrediente()">Cancelar edición</button>
                <div id="mensaje-api-ing" class="msg-api"></div>
            </form>
        </div>

        <div class="admin-card">
            <h2>🧪 Listado de Ingredientes</h2>
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
                    <tr><td colspan="5">Cargando provisiones...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SECCIÓN 2: GESTIÓN DE PRODUCTOS -->
    <div class="admin-grid">
        <div class="admin-card">
            <h2 id="titulo-form-producto">🍲 Añadir Producto</h2>
            <form id="form-producto" class="admin-form">
                <input type="hidden" id="prod-id" value="">
                <input type="hidden" id="prod-activo" value="1">

                <div class="form-group">
                    <label for="prod-categoria">Categoría</label>
                    <select id="prod-categoria" required>
                        <option value="1">1 - Carnes</option>
                        <option value="2">2 - Bebida</option>
                        <option value="3">3 - Arroces</option>
                        <option value="4">4 - Empanados</option>
                        <option value="5">5 - Especiales</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="prod-nombre">Nombre del plato</label>
                    <input type="text" id="prod-nombre" required placeholder="Ej: Estofado de Kolog">
                </div>

                <div class="form-group">
                    <label for="prod-descripcion">Descripción</label>
                    <textarea id="prod-descripcion" rows="2" placeholder="Descripción breve del plato"></textarea>
                </div>

                <div class="form-group">
                    <label for="prod-precio">Precio Base (€)</label>
                    <input type="number" step="0.01" min="0.01" id="prod-precio" required value="8.50">
                </div>

                <div class="form-group">
                    <label for="prod-imagen">Archivo de imagen</label>
                    <input type="text" id="prod-imagen" placeholder="Ej: carne_hyliana.png" value="default.png">
                </div>

                <button type="submit" id="btn-submit-prod" class="btn-hyrule btn-gold">Guardar Producto</button>
                <button type="button" id="btn-cancel-prod" class="btn-hyrule btn-cancel" onclick="resetFormProducto()">Cancelar edición</button>
                <div id="mensaje-api-prod" class="msg-api"></div>
            </form>
        </div>

        <div class="admin-card">
            <h2>📜 Carta de Productos</h2>
            <table class="hyrule-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cat.</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-productos">
                    <tr><td colspan="6">Cargando carta...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<!-- SECCIÓN 3: GESTIÓN DE USUARIOS -->
    <div class="admin-grid">
        <div class="admin-card">
            <h2 id="titulo-form-usuario">👤 Añadir Usuario</h2>
            <form id="form-usuario" class="admin-form">
                <input type="hidden" id="usuario-id" value="">
                <input type="hidden" id="usuario-activo" value="1">   

                <div class="form-group">
                    <label for="usuario-nombre">Nombre</label>
                    <input type="text" id="usuario-nombre" required placeholder="Nombre del usuario">
                </div>

                <div class="form-group">
                    <label for="usuario-email">Email</label>
                    <input type="email" id="usuario-email" required placeholder="correo@hyrule.com">
                </div>

                <div class="form-group">
                    <label for="usuario-contrasena">Contraseña</label>
                    <input type="password" id="usuario-contrasena" required placeholder="Contraseña (dejar en blanco al editar si no cambia)">
                </div>

                <div class="form-group">
                    <label for="usuario-rol">Rol</label>
                    <select id="usuario-rol" required>
                        <option value="usuario">usuario</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit" id="btn-submit-usuario" class="btn-hyrule btn-gold">Guardar Usuario</button>
                <button type="button" id="btn-cancel-usuario" class="btn-hyrule btn-cancel" onclick="resetFormUsuario()">Cancelar edición</button>
                <div id="mensaje-api-usuario" class="msg-api"></div>
            </form>
        </div>

        <div class="admin-card">
            <h2>🛡️ Listado de Usuarios</h2>
            <table class="hyrule-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-usuarios">
                    <tr><td colspan="6">Cargando viajeros...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script src="js/script.js"></script>