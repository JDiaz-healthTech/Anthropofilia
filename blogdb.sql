-- ===================================================================
-- SISTEMA ANTHROPOFILIA: SCRIPT DE GENERACIÓN COMPLETO V1.2
-- Crea la estructura, inserta los datos iniciales y aplica mejoras.
-- Ejecutar sobre una base de datos vacía.
-- ===================================================================

-- Configuración inicial del motor de la base de datos
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
ALTER DATABASE `blogDB` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------
--                  CREACIÓN DE ESTRUCTURA
-- --------------------------------------------------------

--
-- Tabla: usuarios
--
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contrasena_hash` varchar(255) NOT NULL,
  `rol` enum('administrador','autor','usuario') NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tabla: categorias
--
CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tabla: paginas
--
CREATE TABLE `paginas` (
  `id_pagina` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `contenido` longtext NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tabla: posts
--
CREATE TABLE `posts` (
  `id_post` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `contenido` text NOT NULL,
  `imagen_destacada_url` varchar(255) DEFAULT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `id_usuario` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tabla: etiquetas
--
CREATE TABLE `etiquetas` (
  `id_etiqueta` int(11) NOT NULL,
  `nombre_etiqueta` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tabla: post_etiquetas
--
CREATE TABLE `post_etiquetas` (
  `id_post` int(11) NOT NULL,
  `id_etiqueta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------
--                   INSERCIÓN DE DATOS
-- --------------------------------------------------------

--
-- Volcado de datos para la tabla `usuarios`
--
INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `email`, `contrasena_hash`, `rol`, `fecha_registro`) VALUES
(1, 'AnaLopezS1963', 'analosampedro@gmail.com', '$2a$12$WUXJ5GTyRboQKeSfTMpQbuufBECVSM8/npfvg3VZZ1X2stCXHYoXe', 'autor', '2025-07-14 21:23:19');

--
-- Volcado de datos para la tabla `categorias`
--
INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`, `slug`) VALUES
(1, 'Antropología Biológica', 'antropologia-biologica'),
(2, 'Antropología Social y Cultural', 'antropologia-social-y-cultural'),
(3, 'Arqueología', 'arqueologia'),
(4, 'Prehistoria', 'prehistoria'),
(5, 'Evolución Humana', 'evolucion-humana');

--
-- Volcado de datos para la tabla `posts`
--
INSERT INTO `posts` (`id_post`, `titulo`, `contenido`, `imagen_destacada_url`, `fecha_publicacion`, `id_usuario`, `id_categoria`) VALUES
(1, 'Respuesta inmunitaria y sus costes: un enfoque desde la ecología del comportamiento', 'El sistema inmunitario es el conjunto de células, teijdos y moléculas que nos defienden de los patógenos. Su función es crucial para la supervivencia, pero mantenerlo y activarlo tiene costes energéticos y nutricionales. Hoy en día sabemos que la activación inmunitaria no es un proceso que ocurra de forma aislada, sino que interacciona con otros procesos fisiológicos como el crecimiento, la reproducción o el mantenimiento somático. Para optimizar la eficacia biológica, los organismos deben distribuir los recursos limitados entre estos procesos que compiten entre sí. Este reparto de recursos se conoce como trade-off y la ecología del comportamiento es el marco teórico que lo estudia...', 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhLgZ9L0M5i-963_T5w727d2W-c_bC9t_pG_aH8W8s3Q/w400-h266/inmune.jpg', '2024-07-12 19:00:00', 1, 1),
(2, 'Post Prueba de Surf', 'Esto es un tipo de post nuevo que vamos a intentar subir a la página web nueva para comprobar cómo se escribe cómo se plasma dentro de lo que es la arquitectura habitual de la web que estoy diseñando para ver cómo lo plasma. \r\n\r\nDe alguna forma el contenido de la web para nada va a ser lo que estoy narrando ni lo que estoy titulando en este post pero es sencillamente una muestra de un texto de dos párrafos con una imagen asociada para poder juzgar como simplemente que corregir y que está bien en el diseño. \r\n\r\nEscrito desde as veigas a día 27 de julio de 2025 por el petardo de la familia.', 'uploads/1753650574_smoothed_image.jpg', '2025-07-27 21:09:34', 1, 5);


-- --------------------------------------------------------
--         CONFIGURACIÓN DE ÍNDICES Y AUTO_INCREMENT
-- --------------------------------------------------------

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `uq_nombre_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `uq_email` (`email`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `uq_nombre_categoria` (`nombre_categoria`),
  ADD UNIQUE KEY `uq_slug_categoria` (`slug`);

--
-- Indices de la tabla `paginas`
--
ALTER TABLE `paginas`
  ADD PRIMARY KEY (`id_pagina`),
  ADD UNIQUE KEY `uq_slug_pagina` (`slug`);

--
-- Indices de la tabla `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id_post`),
  ADD KEY `fk_post_usuario` (`id_usuario`),
  ADD KEY `fk_post_categoria` (`id_categoria`),
  ADD KEY `idx_posts_fecha_publicacion` (`fecha_publicacion` DESC),
  ADD KEY `idx_posts_cat_fecha` (`id_categoria`,`fecha_publicacion` DESC);

--
-- Indices de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  ADD PRIMARY KEY (`id_etiqueta`),
  ADD UNIQUE KEY `uq_nombre_etiqueta` (`nombre_etiqueta`);

--
-- Indices de la tabla `post_etiquetas`
--
ALTER TABLE `post_etiquetas`
  ADD PRIMARY KEY (`id_post`,`id_etiqueta`),
  ADD KEY `fk_pivote_etiqueta` (`id_etiqueta`);

--
-- AUTO_INCREMENT de las tablas
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `paginas`
  MODIFY `id_pagina` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `posts`
  MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `etiquetas`
  MODIFY `id_etiqueta` int(11) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------
--           RESTRICCIONES (FOREIGN KEYS)
-- --------------------------------------------------------

ALTER TABLE `posts`
  ADD CONSTRAINT `fk_post_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_post_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `post_etiquetas`
  ADD CONSTRAINT `fk_pivote_etiqueta` FOREIGN KEY (`id_etiqueta`) REFERENCES `etiquetas` (`id_etiqueta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pivote_post` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id_post`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;
