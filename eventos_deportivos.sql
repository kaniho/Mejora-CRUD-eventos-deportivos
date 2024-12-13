-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 13-12-2024 a las 19:22:50
-- Versión del servidor: 8.3.0
-- Versión de PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `eventos_deportivos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

DROP TABLE IF EXISTS `eventos`;
CREATE TABLE IF NOT EXISTS `eventos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_evento` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo_deporte` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `ubicacion` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_organizador` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK1` (`id_organizador`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `nombre_evento`, `tipo_deporte`, `fecha`, `hora`, `ubicacion`, `id_organizador`) VALUES
(23, 'Maratón de las Estrellas', 'Atletismo', '2025-03-13', '10:10:00', 'jerez', 29),
(24, 'Desafío Extrem 2025', 'CrossFit', '2025-02-12', '08:00:00', 'Malaga', 30),
(25, 'Campeonato de Voleibol Arena', 'Voleibol', '2025-06-20', '11:10:00', 'Alicante', 31),
(26, 'Desafío All-star', 'Béisbol', '2024-12-13', '20:15:00', 'Tokio', 32),
(27, 'Torneo de Campones', 'Rugby', '2025-08-28', '14:00:00', 'Boston', 33),
(28, 'Cumbre de Esgrima 2025', 'Esgrima', '2025-09-17', '17:20:00', 'Alemania', 34);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `organizadores`
--

DROP TABLE IF EXISTS `organizadores`;
CREATE TABLE IF NOT EXISTS `organizadores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `telefono` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `organizadores`
--

INSERT INTO `organizadores` (`id`, `nombre`, `email`, `telefono`) VALUES
(29, 'Pro Events', 'proevents@gmail.com', '458985783'),
(30, 'Action Planners', 'planners@action.com', '586235498'),
(31, 'Athletic Crews', 'Athletic@crews.net', '67895432'),
(32, 'All-Star Events', 'all-star@gmail.es', '789531057'),
(33, 'GameDay ', 'daygames@hotmail.es', '784301598'),
(34, 'Team Vision', 'vision@team.com', '784015698');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `FK1` FOREIGN KEY (`id_organizador`) REFERENCES `organizadores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
