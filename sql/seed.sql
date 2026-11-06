USE `alejandro_db1`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `pedido_detalle_ingrediente`;
DROP TABLE IF EXISTS `pedido_detalle`;
DROP TABLE IF EXISTS `pedidos`;
DROP TABLE IF EXISTS `oferta_producto`;
DROP TABLE IF EXISTS `ofertas`;
DROP TABLE IF EXISTS `producto_ingrediente`;
DROP TABLE IF EXISTS `ingredientes`;
DROP TABLE IF EXISTS `productos`;
DROP TABLE IF EXISTS `categorias`;
DROP TABLE IF EXISTS `usuarios`;

SET FOREIGN_KEY_CHECKS = 1;

-- ========================================================
-- TABLAS PRINCIPALES
-- ========================================================

CREATE TABLE `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `telefono` VARCHAR(20),
  `direccion` VARCHAR(255),
  `fecha_nacimiento` DATE,
  `rol` VARCHAR(20) NOT NULL DEFAULT 'usuario',
  `fecha_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `categorias` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL UNIQUE, -- Coincide 1:1 con la clase PHP
  `descripcion` TEXT,
  `activa` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `productos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `categoria_id` INT NOT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` TEXT,
  `precio` DECIMAL(10,2) NOT NULL,
  `imagen` VARCHAR(255),
  `parametros` JSON, -- Atributos específicos de la subclase
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ingredientes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL UNIQUE,
  `precio_extra` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `activo` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `producto_ingrediente` (
  `producto_id` INT NOT NULL,
  `ingrediente_id` INT NOT NULL,
  `cantidad` DECIMAL(10,2) NOT NULL DEFAULT 1.00,
  PRIMARY KEY (`producto_id`, `ingrediente_id`),
  CONSTRAINT `fk_pi_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pi_ingrediente` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ofertas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` TEXT,
  `tipo` VARCHAR(20) NOT NULL,
  `valor` DECIMAL(10,2),
  `codigo` VARCHAR(50) UNIQUE,
  `fecha_inicio` DATETIME NOT NULL,
  `fecha_fin` DATETIME,
  `activa` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `oferta_producto` (
  `oferta_id` INT NOT NULL,
  `producto_id` INT NOT NULL,
  PRIMARY KEY (`oferta_id`, `producto_id`),
  CONSTRAINT `fk_op_oferta` FOREIGN KEY (`oferta_id`) REFERENCES `ofertas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_op_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pedidos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL,
  `estado` VARCHAR(20) NOT NULL DEFAULT 'pendiente',
  `fecha_pedido` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subtotal` DECIMAL(10,2) NOT NULL,
  `descuento` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total` DECIMAL(10,2) NOT NULL,
  `metodo_pago` VARCHAR(50) NOT NULL DEFAULT 'simulado',
  `estado_pago` VARCHAR(20) NOT NULL DEFAULT 'pendiente',
  CONSTRAINT `fk_pedidos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pedido_detalle` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pedido_id` INT NOT NULL,
  `producto_id` INT,
  `nombre_producto` VARCHAR(150) NOT NULL,
  `precio_unitario` DECIMAL(10,2) NOT NULL,
  `cantidad` INT NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  CONSTRAINT `fk_pd_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pd_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `chk_pedido_detalle_val` CHECK (`cantidad` > 0 AND `precio_unitario` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pedido_detalle_ingrediente` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pedido_detalle_id` INT NOT NULL,
  `ingrediente_id` INT,
  `nombre_ingrediente` VARCHAR(100) NOT NULL,
  `precio_extra` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `accion` VARCHAR(20) NOT NULL,
  CONSTRAINT `fk_pdi_detalle` FOREIGN KEY (`pedido_detalle_id`) REFERENCES `pedido_detalle` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pdi_ingrediente` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- SEED DE DATOS CORREGIDO Y UNIFICADO
-- ========================================================

-- Nombres coincidiendo 1:1 con tus clases PHP
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Carnes', 'Cortes y platos principales a base de carne'),
(2, 'Bebida', 'Pociones, elixires y refrescos'),
(3, 'Arroces', 'Platos elaborados con base de arroz'),
(4, 'Empanados', 'Rebozados y fritos tradicionales'),
(5, 'Especiales', 'Recetas legendarias de Hyrule');

-- Productos reales insertados con la categoría adecuada para la instanciación dinámica PHP:
-- ID 1 (Carnes), ID 2 (Bebida), ID 3 (Arroces), ID 4 (Empanados), ID 5 (Especiales)
INSERT INTO `productos` (`id`, `categoria_id`, `nombre`, `descripcion`, `precio`, `imagen`, `activo`) VALUES
(1, 3, 'Curry Rice', 'Arroz tradicional con salsa curry reconfortante', 8.50, 'producto-curry rice.png', 1),
(2, 4, 'Fish Pie', 'Pastel horneado relleno de pescado fresco y verduras', 9.00, 'producto-fish pie.png', 1),
(3, 5, 'Hylian Tomato Pizza', 'Pizza crujiente con tomates hylianos y queso fundido', 11.50, 'producto-hylian tomatoe pizza.png', 1),
(4, 1, 'Meat Curry', 'Carne jugosa cocinada a fuego lento en salsa de curry', 12.00, 'producto-meat curry.png', 1),
(5, 1, 'Meat-Stuffed Pumpkins', 'Calabaza horneada rellena de carne picada sazonada', 10.50, 'producto-Meat-Stuffed Pumpkins.png', 1),
(6, 1, 'Pepper Steak', 'Filete de carne salteado con pimientos picantes', 13.00, 'producto-pepper steak.png', 1),
(7, 3, 'Crab Risotto', 'Risotto cremoso preparado con cangrejo fresco', 14.00, 'productos-crab risoto.png', 1),
(8, 3, 'Sea Food Rice', 'Arroz marinero salteado con marisco variado', 12.50, 'producto-sea food rice.png', 1),
(9, 1, 'Gourmet Cheesy Meat Bowl', 'Bol de carne gourmet cubierta de abundante queso gratinado', 15.00, 'productos-gourmet cheesy meat boul.png', 1),
(10, 3, 'Meaty Rice Balls', 'Bolas de arroz rellenas de carne sazonada', 7.50, 'productos-meaty rice balls.png', 1);