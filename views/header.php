<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Link's Recipe</title>
    <style>
        /* Reset de márgenes para la cabecera */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Contenedor Principal de la Cabecera Superior */
        .header-principal {
            background-color: #eab339;
            padding: 12px 30px 8px 30px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-sizing: border-box;
        }

        .header-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        /* Logo */
        .header-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        .header-logo img {
            height: 55px;
            object-fit: contain;
        }

        /* Buscador */
        .header-search-container {
            flex-grow: 1;
            max-width: 650px;
            position: relative;
        }
        .header-search-input {
            width: 100%;
            padding: 10px 45px 10px 15px;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            outline: none;
            box-sizing: border-box;
            color: #555;
        }
        .header-search-input::placeholder {
            color: #aaa;
        }
        .header-search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            fill: none;
            stroke: #555;
            stroke-width: 2;
            cursor: pointer;
        }

        /* Iconos de Usuario y Cofre/Carrito */
        .header-user-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .header-action-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #000;
        }
        .header-action-btn svg {
            width: 28px;
            height: 28px;
            stroke: #4a3810;
            fill: none;
            stroke-width: 1.5;
        }

        /* Menú de Navegación de Categorías */
        .header-nav {
            display: flex;
            justify-content: center;
            gap: 30px;
        }
        .header-nav a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            transition: opacity 0.2s;
        }
        .header-nav a:hover {
            opacity: 0.8;
        }

        /* Franja Inferior Informativa (Ventajas) */
        .header-ventajas-bar {
            background-color: #ffffff;
            border-bottom: 1px solid #eaeaea;
            padding: 12px 20px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }
        .ventaja-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #111111;
        }
        .ventaja-item svg {
            width: 22px;
            height: 22px;
            fill: currentColor;
        }
    </style>
</head>
<body>

    <!-- CABECERA SUPERIOR (Amarilla) -->
    <header class="header-principal">
        <div class="header-top-row">
            
            <!-- Logo -->
            <a href="index.php" class="header-logo">
                <img src="imagenes/logo-web.svg" alt="The Link's Recipe">
            </a>

            <!-- Buscador -->
            <div class="header-search-container">
                <input type="text" class="header-search-input" placeholder="¡Busca alguna receta de Link! arroz, estofado, tarta...">
                <svg class="header-search-icon" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>

            <!-- Acciones de Usuario / Carrito -->
            <div class="header-user-actions">
                <!-- Icono Usuario -->
                <a href="#" class="header-action-btn" title="Mi Cuenta">
                    <svg viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </a>
                <!-- Icono Cofre / Carrito -->
                <a href="#" class="header-action-btn" title="Carrito">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="10" rx="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        <line x1="12" y1="14" x2="12" y2="17"></line>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Menú de Categorías -->
        <nav class="header-nav">
            <a href="?controller=producto&action=index">Top recetas</a>
            <a href="?controller=producto&action=index">Nuevo</a>
            <a href="?controller=producto&action=index">Carnes</a>
            <a href="?controller=producto&action=verCarta">Carta</a>
            <a href="?controller=producto&action=index">Regiones</a>
            <a href="?controller=producto&action=index">Ofertas</a>
        </nav>
    </header>

    <!-- FRANJA INFERIOR (Blanca con ventajas) -->
    <div class="header-ventajas-bar">
        <div class="ventaja-item">
            <!-- Icono Camión -->
            <svg viewBox="0 0 24 24">
                <path d="M1 3h15v13H1z"></path>
                <path d="M16 8h4l3 3v5h-7V8z"></path>
                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                <circle cx="18.5" cy="18.5" r="2.5"></circle>
            </svg>
            <span>Entrega gratuita a partir de 20,00 €</span>
        </div>

        <div class="ventaja-item">
            <!-- Icono Olla humeante -->
            <svg viewBox="0 0 24 24">
                <path d="M4 10h16v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8z"></path>
                <line x1="2" y1="10" x2="22" y2="10"></line>
                <path d="M8 4c0 2-1 2-1 4"></path>
                <path d="M12 4c0 2-1 2-1 4"></path>
                <path d="M16 4c0 2-1 2-1 4"></path>
            </svg>
            <span>Prueba todas las recetas de Link</span>
        </div>

        <div class="ventaja-item">
            <!-- Icono Regalo -->
            <svg viewBox="0 0 24 24">
                <polyline points="20 12 20 22 4 22 4 12"></polyline>
                <rect x="2" y="7" width="20" height="5"></rect>
                <line x1="12" y1="22" x2="12" y2="7"></line>
                <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
                <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
            </svg>
            <span>Packaging totalmente exclusivo</span>
        </div>
    </div>