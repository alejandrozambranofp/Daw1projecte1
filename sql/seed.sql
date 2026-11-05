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
  `nombre` VARCHAR(100) NOT NULL UNIQUE,
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