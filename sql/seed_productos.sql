USE `alejandro_db1`;

-- 1. Insertar Categorías
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `activa`) VALUES
(1, 'Platos Principales', 'Recetas calientes y platos fuertes de Hyrule', 1),
(2, 'Especialidades de Arroz y Curry', 'Platos elaborados a base de arroz y especias', 1),
(3, 'Mariscos y Pescados', 'Especialidades del mar y ríos', 1),
(4, 'Postres y Masas', 'Pasteles, pizzas y recetas horneadas', 1);

-- 2. Insertar Productos con imagenes
INSERT INTO `productos` (`id`, `categoria_id`, `nombre`, `descripcion`, `precio`, `imagen`, `activo`) VALUES
(1, 2, 'Curry Rice', 'Arroz tradicional con salsa curry reconfortante', 8.50, 'producto-curry rice.png', 1),
(2, 4, 'Fish Pie', 'Pastel horneado relleno de pescado fresco y verduras', 9.00, 'producto-fish pie.png', 1),
(3, 4, 'Hylian Tomato Pizza', 'Pizza crujiente con tomates hylianos y queso fundido', 11.50, 'producto-hylian tomatoe pizza.png', 1),
(4, 1, 'Meat Curry', 'Carne jugosa cocinada a fuego lento en salsa de curry', 12.00, 'producto-meat curry.png', 1),
(5, 1, 'Meat-Stuffed Pumpkins', 'Calabaza horneada rellena de carne picada sazonada', 10.50, 'producto-Meat-Stuffed Pumpkins.png', 1),
(6, 1, 'Pepper Steak', 'Filete de carne salteado con pimientos picantes', 13.00, 'producto-pepper steak.png', 1),
(7, 3, 'Crab Risotto', 'Risotto cremoso preparado con cangrejo fresco', 14.00, 'productos-crab risoto.png', 1),
(8, 2, 'Sea Food Rice', 'Arroz marinero salteado con marisco variado', 12.50, 'producto-sea food rice.png', 1),
(9, 1, 'Gourmet Cheesy Meat Bowl', 'Bol de carne gourmet cubierta de abundante queso gratinado', 15.00, 'productos-gourmet cheesy meat boul.png', 1),
(10, 2, 'Meaty Rice Balls', 'Bolas de arroz rellenas de carne sazonada', 7.50, 'productos-meaty rice balls.png', 1);