-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql
-- Tiempo de generación: 28-02-2024 a las 18:19:12
-- Versión del servidor: 8.3.0
-- Versión de PHP: 8.2.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `complainopolis`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentario`
--

CREATE TABLE `comentario` (
  `ID_comentario` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `ID_usuario` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT 'Anonimo',
  `ID_incidencia` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Descripcion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagen_incidencia`
--

CREATE TABLE `imagen_incidencia` (
  `ID_imagen` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `ID_incidencia` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `Ruta` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidencia`
--

CREATE TABLE `incidencia` (
  `ID_incidencia` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `Titulo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Descripcion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Palabras_clave` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ID_usuario` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `Estado` char(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT 'Pendiente',
  `Lugar` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log`
--

CREATE TABLE `log` (
  `ID_log` int NOT NULL,
  `Fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `Descripcion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

--
-- Volcado de datos para la tabla `log`
--

INSERT INTO `log` (`ID_log`, `Fecha`, `Descripcion`) VALUES
(119, '2024-02-28 18:18:48', 'INFO: Intento de identificación erróneo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `ID_usuario` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `Nombre` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Apellidos` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Clave` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Direccion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Telefono` int DEFAULT NULL,
  `Rol` char(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT 'Colaborador',
  `Estado` char(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT 'Inactivo',
  `Foto` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

--
-- Volcado de datos para la tabla `usuario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `valoracion`
--

CREATE TABLE `valoracion` (
  `ID_incidencia` int NOT NULL,
  `ID_usuario` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `Valoracion` smallint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD PRIMARY KEY (`ID_comentario`);

--
-- Indices de la tabla `imagen_incidencia`
--
ALTER TABLE `imagen_incidencia`
  ADD PRIMARY KEY (`ID_imagen`,`ID_incidencia`);

--
-- Indices de la tabla `incidencia`
--
ALTER TABLE `incidencia`
  ADD PRIMARY KEY (`ID_incidencia`);

--
-- Indices de la tabla `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`ID_log`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`ID_usuario`);

--
-- Indices de la tabla `valoracion`
--
ALTER TABLE `valoracion`
  ADD PRIMARY KEY (`ID_incidencia`,`ID_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `log`
--
ALTER TABLE `log`
  MODIFY `ID_log` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
