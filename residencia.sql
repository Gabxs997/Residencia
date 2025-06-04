-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-04-2025 a las 14:52:12
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `residencia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `id` int(10) NOT NULL,
  `Usuario` varchar(30) NOT NULL,
  `Contrasena` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`id`, `Usuario`, `Contrasena`) VALUES
(1, 'Gabriel', '123');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulos`
--

CREATE TABLE `articulos` (
  `id` int(11) NOT NULL,
  `ur` varchar(50) NOT NULL,
  `no_inventario` varchar(50) NOT NULL,
  `cabm` varchar(50) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `descripcion_detalle` varchar(255) NOT NULL,
  `partida_presupuestal` varchar(50) NOT NULL,
  `partida_contable` varchar(50) NOT NULL,
  `fecha_alta` date NOT NULL,
  `fecha_documento` date NOT NULL,
  `tipo_bien` varchar(100) NOT NULL,
  `no_contrato` varchar(50) NOT NULL,
  `no_factura` varchar(50) NOT NULL,
  `proveedor` varchar(100) NOT NULL,
  `serie` varchar(50) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `estado_bien` enum('Bueno','Regular','Malo') NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `rfc_responsable` varchar(20) NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `origen` varchar(100) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `articulos`
--

INSERT INTO `articulos` (`id`, `ur`, `no_inventario`, `cabm`, `descripcion`, `descripcion_detalle`, `partida_presupuestal`, `partida_contable`, `fecha_alta`, `fecha_documento`, `tipo_bien`, `no_contrato`, `no_factura`, `proveedor`, `serie`, `modelo`, `marca`, `estado_bien`, `ubicacion`, `rfc_responsable`, `importe`, `observaciones`, `origen`, `fecha_registro`) VALUES

(7, '038 H.R. \"B\" MERIDA (P)', '331498', 'I450400124', 'ESCRITORIO DE METAL', 'ESCRITORIO DE METAL', '5101', '51101', '1998-09-30', '1998-09-30', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'Anatomía - Patología', 'Pendiente', '1115.50', '', 'N/A', '2025-04-09 17:06:43'),

(9, '038 H.R. \"B\" MERIDA (P)', '1338127', 'I060200342', 'KIMO-INSUFLADOR', 'KIOMO INSUFLADOR LAPAROSCOPIO', '5401', '56902', '2006-12-19', '2002-12-19', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 'MGB', 'Malo', 'Anatomía - Patología', 'Pendiente', '1.00', '', 'N/A', '2025-04-09 17:21:06'),

(10, '038 H.R. \"B\" MERIDA (P)', '1731289', 'I450600048', 'BURO', 'BURO', '5102', '51101', '2007-12-03', '2007-12-18', 'BIEN', '14942007', 'S/F', 'DISTRIBUCIONES MEDICAS DEL SURESTE, S.A. DE C.V.', 'S/N', 'S/M', 'CIIASA', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '1215.00', '', 'N/A', '2025-04-10 17:46:47'),

(11, '038 H.R. \"B\" MERIDA (P)', '1481714', 'I450400320', 'SILLON', 'SILLON EJECUTIVO CON BRAZOS TAPIZADOS', '5101', '51101', '2006-01-11', '2005-01-01', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'STEELE', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '1.00', '', 'N/A', '2025-04-10 17:58:24'),

(12, '038 H.R. \"B\" MERIDA (P)', '1481361', 'I450400120', 'ESCRITORIO', 'ESCRITORIO SECRETARIAL TIPO L 1.65 X .75', '5101', '51101', '2006-01-11', '2005-01-01', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'STEELE', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '6948.50', '', 'N/A', '2025-04-10 18:23:19'),
(13, '038 H.R. \"B\" MERIDA (P)', '332560', 'I450400318', 'SILLA DE METAL', 'SILLA DE METAL', '5101', '51101', '1998-09-30', '1998-09-30', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '142.59', '', 'N/A', '2025-04-10 18:44:17'),
(14, '038 H.R. \"B\" MERIDA (P)', '1707266', 'I060400428', 'MICROSCOPIO', 'MICROSCOPIO TRIOCULAR P/MICROFOTOGRAFIA', '5402', '53101', '2007-12-20', '2007-12-20', 'BIEN', '504832007', 'S/F', 'ASESORIA Y PROVEEDORA DE EQUIPOS PARA LABORATORIOS S.A. DE C.V.', 'S/N', 'S/M', 'LEICA', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '138823.60', '', 'N/A', '2025-04-10 18:46:21'),
(15, '038 H.R. \"B\" MERIDA (P)', '1697840', 'I450400018', 'ARCHIVERO GUARDA VISIBLE', 'ARCHIVERO GUARDA VISIBLE', '5101', '51101', '2007-12-12', '2007-12-11', 'BIEN', '504082007', 'S/F', 'GRUPO INTERNACIONAL DE DISEÐO Y FABRICACION DE MUEB. ESP. S.A. DE C.V.', 'S/N', 'S/M', 'GID', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '1899.00', '', 'N/A', '2025-04-10 18:48:12'),
(16, '038 H.R. \"B\" MERIDA (P)', '1697841', 'I450400018', 'ARCHIVERO GUARDA VISIBLE', 'ARCHIVERO GUARDA VISIBLE', '5101', '51101', '2007-12-12', '2007-12-11', 'BIEN', '504082007', 'S/F', 'GRUPO INTERNACIONAL DE DISEÐO Y FABRICACION DE MUEB. ESP. S.A. DE C.V.', 'S/N', 'S/M', 'GID', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '1899.00', '', 'N/A', '2025-04-10 18:50:53'),
(17, '038 H.R. \"B\" MERIDA (P)', '1563498', 'I060200172', 'CENTRIFUGA', 'CENTRIFUGA', '5401', '53101', '2006-01-30', '2005-10-31', 'BIEN', '7972005', 'S/F', 'TORRES PENICHE JOSE GUSTAVO', 'S/N', 'S/M', 'LW SCIENTIFIC', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '7990.00', '', 'N/A', '2025-04-10 18:53:05'),
(18, '038 H.R. \"B\" MERIDA (P)', '328415', 'I450600048', 'BURO', 'BURO', '5102', '51101', '1998-09-30', '1989-07-14', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '7.50', '', 'N/A', '2025-04-10 18:54:53'),
(19, '038 H.R. \"B\" MERIDA (P)', '331932', 'I450400014', 'ARCHIVERO DE MADERA', 'ARCHIVERO DE MADERA', '5101', '51101', '1998-09-30', '1966-12-31', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '496.15', '', 'N/A', '2025-04-10 19:01:45'),
(20, '038 H.R. \"B\" MERIDA (P)', '331918', 'I450400146', 'GABINETE UNIVERSAL', 'GABINETE UNIVERSAL', '5101', '51101', '1998-09-30', '1966-12-31', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '329.25', '', 'N/A', '2025-04-10 19:03:16'),
(21, '038 H.R. \"B\" MERIDA (P)', '329929', 'I450400146', 'GABINETE UNIVERSAL', 'GABINETE UNIVERSAL', '5101', '51101', '1998-09-30', '1991-02-15', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '478.40', '', 'N/A', '2025-04-10 20:20:58'),
(22, '038 H.R. \"B\" MERIDA (P)', '1720554', 'I450400122', 'ESCRITORIO DE MADERA', 'ESCRITORIO DE MADERA 180 X 120 80 X 75', '5101', '51101', '2007-12-30', '2007-12-27', 'BIEN', '505922007', 'S/F', 'INDUSTRIA TRANSFORMADORA ALLIANCE MEXICANA, S.A. DE C.V.', 'S/N', 'S/M', 'ITAM', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '9478.26', '', 'N/A', '2025-04-11 15:53:23'),
(23, '038 H.R. \"B\" MERIDA (P)', '331931', 'I450400174', 'LOCKER', 'LOCKER', '5101', '51101', '1998-09-30', '1991-08-22', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '126.50', '', 'N/A', '2025-04-11 16:02:14'),
(24, '038 H.R. \"B\" MERIDA (P)', '331930', 'I450400174', 'LOCKER', 'LOCKER', '5101', '51101', '1998-09-30', '1980-12-31', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '1.25', '', 'N/A', '2025-04-11 16:03:43'),
(25, '038 H.R. \"B\" MERIDA (P)', '331924', 'I450400210', 'MAQUINA ESCRIBIR MECANICA', 'MAQUINA ESCRIBIR MECANICA', '5102', '51901', '1998-09-30', '1991-07-12', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'OLIVETTI', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '680.45', '', 'N/A', '2025-04-11 16:05:32'),
(26, '038 H.R. \"B\" MERIDA (P)', '1763502', 'I450400142', 'GABINETE PARA ARCHIVO', 'GABINETE PARA ARCHIVO', '5101', '51101', '2008-09-17', '2007-12-13', 'BIEN', '138172007', 'S/F', 'LG DISTRIBUCIONES, S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '3900.00', '', 'N/A', '2025-04-11 16:07:06'),
(27, '038 H.R. \"B\" MERIDA (P)', '1763506', 'I450400142', 'GABINETE PARA ARCHIVO', 'GABINETE PARA ARCHIVO', '5101', '51101', '2008-09-17', '2007-12-13', 'BIEN', '138172007', 'S/F', 'LG DISTRIBUCIONES, S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '3900.00', '', 'N/A', '2025-04-11 16:08:50'),
(28, '038 H.R. \"B\" MERIDA (P)', '1571991', 'I450400016', 'ARCHIVERO DE METAL', 'ARCHIVERO DE METAL', '5101', '51101', '2006-03-22', '2005-11-28', 'BIEN', '56892005', 'S/F', 'REACTIVOS Y EQUIPOS DEL SURESTE, S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '2100.00', '', 'N/A', '2025-04-11 16:19:58'),
(29, '038 H.R. \"B\" MERIDA (P)', '1571986', 'I450400016', 'ARCHIVERO DE METAL', 'ARCHIVERO DE METAL', '5101', '51101', '2006-03-22', '2005-11-28', 'BIEN', '56902005', 'S/F', 'REACTIVOS Y EQUIPOS DEL SURESTE, S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '2100.00', '', 'N/A', '2025-04-11 16:34:22'),
(30, '038 H.R. \"B\" MERIDA (P)', '1710938', 'I090000058', 'BANQUETA DE ALTURA', 'BANQUETA ALTURA', '5401', '51101', '2007-12-24', '2007-12-24', 'BIEN', '505172007', 'S/F', 'CONSORCIO INDUSTRIAL DIAZ HERMANOS, S.A. DE C.V.', 's/n', 's/n', 'PACSA', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '400.00', '', 'N/A', '2025-04-11 16:38:52'),
(31, '038 H.R. \"B\" MERIDA (P)', '1731497', 'I450400318', 'SILLA DE METAL', 'SILLA DE METAL', '5101', '51101', '2007-12-31', '2007-12-31', 'BIEN', '138162007', 'S/F', 'LG DISTRIBUCIONES, S.A. DE C.V.', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '425.00', '', 'N/A', '2025-04-11 16:41:21'),
(32, '038 H.R. \"B\" MERIDA (P)', '1729551', 'I210000056', 'GABINETE SANITARIO', 'BOTE SANITARIO CON PERDAL 26 X 26 X 60', '5102', '51101', '2007-12-31', '2007-12-31', 'BIEN', '506732007', 'S/F', 'GRUPO INTERNACIONAL DE DISEÐO Y FABRICACION DE MUEB. ESP. S.A. DE C.V.', 's/n', 's/n', 'GID', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '750.00', '', 'N/A', '2025-04-11 16:43:33'),
(33, '038 H.R. \"B\" MERIDA (P)', '328457', 'I090000058', 'BANQUETA DE ALTURA', 'BANQUETA ALTURA', '5401', '51101', '1998-09-30', '1992-11-23', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '64.25', '', 'N/A', '2025-04-11 16:50:50'),
(34, '038 H.R. \"B\" MERIDA (P)', '329150', 'I450400008', 'ANAQUEL', 'ANAQUEL MOVIL', '5101', '51101', '1998-09-30', '1966-12-31', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '4.50', '', 'N/A', '2025-04-11 17:07:05'),
(35, '038 H.R. \"B\" MERIDA (P)', '1571987', 'I450400016', 'ARCHIVERO DE METAL', 'ARCHIVERO DE METAL', '5101', '51101', '2006-03-22', '2005-11-28', 'BIEN', '56902005', 'S/F', 'REACTIVOS Y EQUIPOS DEL SURESTE, S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '2100.00', '', 'N/A', '2025-04-11 17:10:40'),
(36, '038 H.R. \"B\" MERIDA (P)', '1571988', 'I450400016', 'ARCHIVERO DE METAL', 'ARCHIVERO DE METAL', '5101', '51101', '2006-03-22', '2005-11-28', 'BIEN', '56902005', 'S/F', 'REACTIVOS Y EQUIPOS DEL SURESTE, S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '2100.00', '', 'N/A', '2025-04-11 17:12:24'),
(37, '038 H.R. \"B\" MERIDA (P)', '1571992', 'I450400016', 'ARCHIVERO DE METAL', 'ARCHIVERO DE METAL', '5101', '51101', '2006-03-22', '2005-11-28', 'BIEN', '56892005', 'S/F', 'REACTIVOS Y EQUIPOS DEL SURESTE, S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '2100.00', '', 'N/A', '2025-04-11 17:20:08'),
(38, '038 H.R. \"B\" MERIDA (P)', '1731365', 'I090000058', 'BANQUETA DE ALTURA', 'BANQUETA ALTURA', '5401', '51101', '2007-12-31', '2007-12-18', 'BIEN', '14942007', 'S/F', 'DISTRIBUCIONES MEDICAS DEL SURESTE, S.A. DE C.V.', 's/n', 's/n', 'AG', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '350.00', '', 'N/A', '2025-04-11 17:29:06'),
(39, '038 H.R. \"B\" MERIDA (P)', '1481715', 'I450400320', 'SILLON', 'SILLON EJECUTIVO CON BRAZOS TAPIZADOS', '5101', '51101', '2006-01-11', '2005-01-01', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 'STEELE', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '1.00', '', 'N/A', '2025-04-11 17:31:38'),
(40, '038 H.R. \"B\" MERIDA (P)', '329823', 'I450400014', 'ARCHIVERO DE MADERA', 'ARCHIVERO DE MADERA', '5101', '51101', '1998-09-30', '1989-12-05', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '1.00', '', 'N/A', '2025-04-11 17:37:06'),
(41, '038 H.R. \"B\" MERIDA (P)', '330614', 'I450400314', 'SILLA', 'SILLA', '5101', '51101', '1998-09-30', '1980-12-31', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '1.70', '', 'N/A', '2025-04-11 17:41:35'),
(42, '038 H.R. \"B\" MERIDA (P)', '1706324', 'I450400314', 'SILLA', 'SILLA', '5101', '51101', '2007-12-20', '2007-12-18', 'BIEN', '504802007', 'S/F', 'MUEBLES TUBULARES BETA  S.A. DE C.V.', 's/n', 's/n', 'MTB', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '223.00', '', 'N/A', '2025-04-11 17:43:19'),
(43, '038 H.R. \"B\" MERIDA (P)', '1480990', 'I450400120', 'ESCRITORIO', 'ESCRITORIO SECRETARIAL TIPO L 1.65 X .75', '5101', '51101', '2006-01-11', '2005-01-01', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 'STEELE', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '6948.50', '', 'N/A', '2025-04-11 17:45:18'),
(44, '038 H.R. \"B\" MERIDA (P)', '2443964', 'I180000162', 'IMPRESORA LASER', 'IMPRESORA A COLOR. ACC. DE DIGITALIZADOR', '5206', '51501', '2022-12-20', '2022-12-10', 'BIEN', '220123EM2022', 'S/F', 'CONSORCIO HERMES, S.A. DE C.V.', '000028-PR8', 'APEOS PRINT C325', 'FUJIFILM', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '1.00', '', 'N/A', '2025-04-11 17:46:59'),
(45, '038 H.R. \"B\" MERIDA (P)', '329977', 'I450400314', 'SILLA', 'SILLA', '5101', '51101', '1981-12-31', '1981-12-31', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '1.00', '', 'N/A', '2025-04-11 17:48:26'),
(46, '038 H.R. \"B\" MERIDA (P)', '1571989', 'I450400016', 'ARCHIVERO DE METAL', 'ARCHIVERO DE METAL', '5101', '51101', '2006-03-22', '2005-11-28', 'BIEN', '56902005', 'S/F', 'REACTIVOS Y EQUIPOS DEL SURESTE, S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '2100.00', '', 'N/A', '2025-04-11 17:48:41'),
(47, '038 H.R. \"B\" MERIDA (P)', '331087', 'I450400314', 'SILLA', 'SILLA', '5101', '51101', '1991-09-30', '1991-10-03', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '25.55', '', 'N/A', '2025-04-11 17:49:51'),
(48, '038 H.R. \"B\" MERIDA (P)', '1571990', 'I450400016', 'ARCHIVERO DE METAL', 'ARCHIVERO DE METAL', '5101', '51101', '2006-03-22', '2005-11-28', 'BIEN', '56902005', 'S/F', 'REACTIVOS Y EQUIPOS DEL SURESTE, S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '2100.00', '', 'N/A', '2025-04-11 17:50:45'),
(49, '038 H.R. \"B\" MERIDA (P)', '330600', 'I450400174', 'LOCKER', 'LOCKER', '5101', '51101', '1991-09-30', '1991-08-20', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '126.50', '', 'N/A', '2025-04-11 17:51:31'),
(50, '038 H.R. \"B\" MERIDA (P)', '331913', 'I450400252', 'MESA DE TRABAJO DE METAL', 'MESA DE TRABAJO DE METAL', '5101', '51101', '1998-09-30', '1992-10-19', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'ATO', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '1365.75', '', 'N/A', '2025-04-11 17:52:15'),
(51, '038 H.R. \"B\" MERIDA (P)', '1727236', 'I090001300', 'LAMPARA DE CHICOTE', 'LAMPARA DE PIE RODABLE', '5401', '53101', '2007-12-31', '2007-12-31', 'BIEN', '0058MA2007', '050665/2007', 'GOVAL INTERNACIONAL  S.A. DE C.V.', 'S/N', 'S/M', 'HERLIS', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '560.00', '', 'N/A', '2025-04-11 17:55:12'),
(52, '038 H.R. \"B\" MERIDA (P)', '1710926', 'I090000058', 'BANQUETA DE ALTURA', 'BANQUETA DE ALTURA', '5401', '51101', '2007-12-24', '2007-12-20', 'BIEN', '505172007', 'S/F', 'CONSORCIO INDUSTRIAL DIAZ HERMANOS, S.A. DE C.V.', 'S/N', 'S/M', 'PACSA', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '400.00', '', 'N/A', '2025-04-11 18:12:36'),
(53, '038 H.R. \"B\" MERIDA (P)', '2443967', 'I180000188', 'IMPRESORA DE CODIGO DE BARRAS', 'IMPRESORA DE ETIQUETAS. ACC. DE DIGITALIZADOR', '5102', '51901', '2022-12-30', '2022-12-30', 'ACCESORIO DE EQUIPO MÉDICO', '220123EM2022', 'S/F', 'CONSORCIO HERMES, S.A. DE C.V.', 'D5N220200586', '2D220t', 'ZEBRA', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '1.00', '', 'N/A', '2025-04-11 18:24:56'),
(54, '038 H.R. \"B\" MERIDA (P)', '2443969', 'I180000188', 'IMPRESORA DE CODIGO DE BARRAS', 'IMPRESORA DE ETIQUETAS. ACC. DE DIGITALIZADOR', '5102', '51901', '2022-12-30', '2022-12-30', 'ACCESORIO DE EQUIPO MÉDICO', '220123EM2022', 'S/F', 'CONSORCIO HERMES, S.A. DE C.V.', 'D5N220200585', 'ZD220t', 'ZEBRA', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '1.00', '', 'N/A', '2025-04-11 18:32:09'),
(55, '038 H.R. \"B\" MERIDA (P)', '2443968', 'I180000188', 'IMPRESORA DE CODIGO DE BARRAS', 'IMPRESORA DE ETIQUETAS. ACC. DE DIGITALIZADOR', '5102', '51901', '2022-12-30', '2022-12-30', 'ACCESORIO DE EQUIPO MÉDICO', '220123EM2022', 'S/F', 'CONSORCIO HERMES, S.A. DE C.V.', 'D5J214807376', 'ZD220t', 'ZEBRA', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '1.00', '', 'N/A', '2025-04-11 18:33:42'),
(56, '038 H.R. \"B\" MERIDA (P)', '331920', 'I090000238', 'GABINETE INSTRUMENTAL QUIRURGICO', 'GABINETE INSTRUMENTAL QUIRURGICO', '5401', '53101', '1998-09-30', '1980-12-31', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '5.20', '', 'N/A', '2025-04-11 18:49:05'),
(57, '038 H.R. \"B\" MERIDA (P)', '330601', 'I450400174', 'LOCKER', 'LOCKER', '5101', '51101', '1998-09-30', '1998-11-29', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '96.90', '', 'N/A', '2025-04-11 18:49:42'),
(58, '038 H.R. \"B\" MERIDA (P)', '330602', 'I450400174', 'LOCKER', 'LOCKER', '5101', '51101', '1998-09-30', '1991-08-20', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '126.50', '', 'N/A', '2025-04-11 18:51:36'),
(59, '038 H.R. \"B\" MERIDA (P)', '330603', 'I450400174', 'LOCKER', 'LOCKER', '5101', '51101', '1998-09-30', '1991-08-20', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '126.50', '', 'N/A', '2025-04-11 18:59:15'),
(60, '038 H.R. \"B\" MERIDA (P)', '1763501', 'I450400142', 'GABINETE PARA ARCHIVO', 'GABINETE PARA ARCHIVO', '5101', '51101', '2008-09-17', '2007-12-13', 'BIEN', '138172007', 'S/F', 'LG DISTRIBUCIONES, S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '3900.00', '', 'N/A', '2025-04-15 16:01:46'),
(61, '038 H.R. \"B\" MERIDA (P)', '330603', 'I450400174', 'LOCKER', 'LOCKER', '5101', '51101', '1998-09-30', '1991-08-20', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '126.50', '', 'N/A', '2025-04-15 16:16:29'),
(62, '038 H.R. \"B\" MERIDA (P)', '331945', 'I450400248', 'MESA DE TRABAJO', 'MESA DE TRABAJO', '5101', '51101', '1998-09-30', '1989-11-04', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '835.80', '', 'N/A', '2025-04-15 16:21:09'),
(63, '038 H.R. \"B\" MERIDA (P)', '330771', 'I450400174', 'LOCKER', 'LOCKER', '5101', '51101', '1998-09-30', '1966-12-31', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '1.00', '', 'N/A', '2025-04-15 16:26:39'),
(64, '038 H.R. \"B\" MERIDA (P)', '1706326', 'I450400314', 'SILLA', 'SILLA FIJA', '5101', '51101', '2007-12-20', '2007-12-18', 'BIEN', '504802007', 'S/F', 'MUEBLES TUBULARES BETA  S.A. DE C.V.', 's/n', 's/n', 'MTB', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '223.00', '', 'N/A', '2025-04-15 16:32:25'),
(65, '038 H.R. \"B\" MERIDA (P)', '330625', 'I450400124', 'ESCRITORIO DE METAL', 'ESCRITORIO DE METAL', '5101', '51101', '1998-09-30', '1991-01-15', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '1115.50', '', 'N/A', '2025-04-15 16:34:35'),
(66, '038 H.R. \"B\" MERIDA (P)', '1709899', 'I090000050', 'BAÑO MARIA (UTENSILIO)', 'BANO PARA FLOTACION CON MOVIMIENTO CIRCU', '5401', '53101', '2007-12-24', '2007-12-20', 'BIEN', '505132007', 'S/F', 'HOSPITECNICA S.A. DE C.V.', 'S/N', 'S/M', 'THERMO SHANDON', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '13000.00', '', 'N/A', '2025-04-15 16:49:25'),
(67, '038 H.R. \"B\" MERIDA (P)', '2452470', 'I060600048', 'ESFIGMOMANOMETRO O BAUMANOMETRO', 'ESFIGMOMANOMETRO PEDESTAL', '5401', '53101', '2023-11-09', '2023-11-09', 'BIEN', '230014IM2023', '50107/2023', 'DIMSA INTERPRETACION MEDICA, S.A. DE C.V.', 's/n', 'MASTERMED C', 'KAWE', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '6842.00', '', 'N/A', '2025-04-15 16:56:35'),
(68, '038 H.R. \"B\" MERIDA (P)', '2399281', 'I090001404', 'EQUIPO DE TINCION DE TEJIDOS', 'UNIDAD PARA INCLUIR TEJDIOS EN PARAFINA', '5401', '53101', '2022-12-20', '2022-12-20', 'EQUIPO MÉDICO', '220046EM2022', '50156/2022', 'SERVICIO Y VENTA DE INSUMOS MEDICOS ESPECIALIZADOS, S.A. DE C.V.', '51080861/51090840', 'TISSUE TEK-TEC 6', 'SAKURA', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '330860.00', '', 'N/A', '2025-04-15 16:56:39'),
(69, '038 H.R. \"B\" MERIDA (P)', '1763503', 'I450400142', 'GABINETE PARA ARCHIVO', 'GABINETE PARA ARCHIVO', '5101', '51101', '2008-09-17', '2007-12-13', 'BIEN', '138172007', 'S/F', 'LG DISTRIBUCIONES, S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '3900.00', '', 'N/A', '2025-04-15 17:23:34'),
(70, '038 H.R. \"B\" MERIDA (P)', '330607', 'I450600048', 'BURO', 'BURO', '5102', '51101', '1998-09-30', '1991-09-23', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '91.45', '', 'N/A', '2025-04-15 18:16:34'),
(71, '038 H.R. \"B\" MERIDA (P)', '1715804', 'I090000064', 'BASCULA ESTADIMETRO', 'BASCULA ESTADIMETRO CAPACIDAD 140 KG', '5401', '53101', '2007-12-29', '2007-12-26', 'BIEN', '505522007', 'S/F', 'ASESORIA AVANZADA EN BASCULAS  S.A. DE C.V.', 's/n', 's/n', 'TECNOCOR', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '975.00', '', 'N/A', '2025-04-15 18:19:02'),
(72, '038 H.R. \"B\" MERIDA (P)', '1729552', 'I210000056', 'GABINETE SANITARIO', 'BOTE SANITARIO CON PERDAL 26 X 26 X 60', '5102', '51101', '2007-12-31', '2007-12-31', 'BIEN', '506732007', 'S/F', 'GRUPO INTERNACIONAL DE DISEÐO Y FABRICACION DE MUEB. ESP. S.A. DE C.V.', 's/n', 's/n', 'GID', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '750.00', '', 'N/A', '2025-04-15 19:15:21'),
(73, '038 H.R. \"B\" MERIDA (P)', '329787', 'I450600048', 'BURO', 'BURO', '5102', '51101', '1998-09-30', '1991-09-23', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '91.45', '', 'N/A', '2025-04-15 19:17:30'),
(74, '038 H.R. \"B\" MERIDA (P)', '2363313', 'I450400314', 'SILLA', 'SILLA FIJA SIN DESCANSABRAZOS', '5101', '51101', '2022-08-17', '2022-08-17', 'BIEN', '220007MA2022', '50050/2022', 'BR MARCAMM SAPI S.A. DE C.V.', 's/n', 'NOVAISO SIN BRAZO', 'OFFIHO', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '1410.00', '', 'N/A', '2025-04-15 19:20:46'),
(75, '038 H.R. \"B\" MERIDA (P)', '330612', 'I450600048', 'BURO', 'BURO', '5102', '51101', '1998-09-30', '1991-09-23', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '91.45', '', 'N/A', '2025-04-15 19:25:45'),
(76, '038 H.R. \"B\" MERIDA (P)', '1731264', 'I450600048', 'BURO', 'BURO', '5102', '51101', '2007-12-31', '2007-12-18', 'BIEN', '14942007', 'S/F', 'DISTRIBUCIONES MEDICAS DEL SURESTE, S.A. DE C.V.', 's/n', 's/n', 'CIIASA', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '1215.00', '', 'N/A', '2025-04-15 19:29:13'),
(78, '038 H.R. \"B\" MERIDA (P)', '330593', 'I450400252', 'MESA DE TRABAJO DE METAL', 'MESA DE TRABAJO DE METAL', '5101', '51101', '1998-09-30', '1989-01-01', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '69.25', '', 'N/A', '2025-04-15 19:36:11'),
(79, '038 H.R. \"B\" MERIDA (P)', '330278', 'I450400228', 'MESA', 'MESA', '5101', '51101', '1998-09-30', '1986-12-31', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '35.05', '', 'N/A', '2025-04-15 19:38:46'),
(80, '038 H.R. \"B\" MERIDA (P)', '1763505', 'I450400142', 'GABINETE PARA ARCHIVO', 'GABINETE PARA ARCHIVO', '5101', '51101', '2008-09-17', '2007-12-13', 'BIEN', '138172007', 'S/F', 'LG DISTRIBUCIONES, S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '3900.00', '', 'N/A', '2025-04-16 17:00:26'),
(81, '038 H.R. \"B\" MERIDA (P)', '1763510', 'I450400142', 'GABINETE PARA ARCHIVO', 'GABINETE PARA ARCHIVO', '5101', '51101', '2008-09-17', '2007-12-13', 'BIEN', '138172007', 'S/F', 'LG DISTRIBUCIONES, S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '3900.00', '', 'N/A', '2025-04-16 17:02:19'),
(82, '038 H.R. \"B\" MERIDA (P)', '1763509', 'I450400142', 'GABINETE PARA ARCHIVO', 'GABINETE PARA ARCHIVO', '5101', '51101', '2008-09-17', '2007-12-13', 'BIEN', '138172007', 'S/F', 'LG DISTRIBUCIONES, S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '3900.00', '', 'N/A', '2025-04-16 17:04:49'),
(83, '038 H.R. \"B\" MERIDA (P)', '1715127', 'I090000308', 'MESA PASTEUR', 'MESA PASTEUR C/CUBIERTA DE A.INOX.', '5401', '53101', '2007-12-29', '2007-12-24', 'BIEN', '505452007', 'S/F', 'GUILLERMO LOPEZ DAVILA', 'S/N', 'S/M', 'LEINAD', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '1580.00', '', 'N/A', '2025-04-16 17:07:47'),
(84, '038 H.R. \"B\" MERIDA (P)', '1763508', 'I450400142', 'GABINETE PARA ARCHIVO', 'GABINETE PARA ARCHIVO', '5101', '51101', '2008-07-19', '2007-12-13', 'BIEN', '138172007', 'S/F', 'LG DISTRIBUCIONES, S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '3900.00', '', 'N/A', '2025-04-16 18:18:36'),
(85, '038 H.R. \"B\" MERIDA (P)', '331962', 'I450400314', 'SILLA', 'SILLA', '5101', '51101', '1998-09-30', '1990-11-12', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '301.60', '', 'N/A', '2025-04-16 18:20:57'),
(86, '038 H.R. \"B\" MERIDA (P)', '1563489', 'I060200470', 'UNIDAD ULTRASONICA', 'UNIDAD ULTRASONICA', '5401', '53101', '2005-01-30', '2005-11-14', 'EQUIPO MÉDICO', '209672005', '2896', 'ELECTRONICA Y MEDICINA S.A.', 'L3295884', 'LOGIQ 3', 'GENERAL ELECTRIC', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '559901.18', '', 'N/A', '2025-04-16 18:28:32'),
(87, '038 H.R. \"B\" MERIDA (P)', '1689972', 'I060200360', 'MICROTOMO', 'MICROTOMO PARA CORTES DE PARAFINA', '5401', '53101', '2007-11-30', '2007-11-29', 'BIEN', '502952007', 'S/F', 'ASESORIA Y PROVEEDORA DE EQUIPOS PARA LABORATORIOS S.A. DE C.V.', 'S/N', 'S/M', 'LEICA', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '195235.15', '', 'N/A', '2025-04-16 18:30:25'),
(88, '038 H.R. \"B\" MERIDA (P)', '331937', 'I060200360', 'MICROTOMO', 'MICROTOMO', '5401', '53101', '1998-09-30', '1994-09-28', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'YUNG HISTO', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '29498.00', '', 'N/A', '2025-04-16 18:32:45'),
(89, '038 H.R. \"B\" MERIDA (P)', '1694963', 'I060000000', 'APARATOS E INSTRUMENTOS CIENTIFICOS Y DE', 'TINCION AUTOMATICA DE TEJIDOS', '5401', '53201', '2007-12-10', '2007-12-05', 'BIEN', '503692007', 'S/F', 'ASESORIA Y PROVEEDORA DE EQUIPOS PARA LABORATORIOS S.A. DE C.V.', 'S/N', 'S/M', 'LEICA', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '297969.60', '', 'N/A', '2025-04-16 18:37:13'),
(90, '038 H.R. \"B\" MERIDA (P)', '1694995', 'I180000072', 'MONITOR', 'MONITOR TINCION AUTOMATIZADA', '5206', '51501', '2007-12-10', '2007-12-05', 'BIEN', '503692007', 'S/F', 'ASESORIA Y PROVEEDORA DE EQUIPOS PARA LABORATORIOS S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '1.00', '', 'N/A', '2025-04-16 18:41:26'),
(91, '038 H.R. \"B\" MERIDA (P)', '330597', 'I450400248', 'MESA DE TRABAJO', 'MESA DE TRABAJO', '5101', '51101', '1998-09-30', '1991-10-21', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 's/n', 's/n', 's/n', 'Bueno', 'BANCO DE SANGRE', 'Pendiente', '1127.70', '', 'N/A', '2025-04-16 18:42:53'),
(92, '038 H.R. \"B\" MERIDA (P)', '1695027', 'I330000100', 'UPS PARA EQUIPO MEDICO', 'REGULADOR DE TINCION AUTOMATIZADA', '5205', '56601', '2007-12-10', '2007-12-05', 'BIEN', '503692007', 'S/F', 'ASESORIA Y PROVEEDORA DE EQUIPOS PARA LABORATORIOS S.A. DE C.V.', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '1.00', '', 'N/A', '2025-04-16 18:42:57'),
(93, '038 H.R. \"B\" MERIDA (P)', '331935', 'I450400228', 'MESA', 'MESA', '5101', '51101', '1998-09-30', '1991-02-21', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '1640.10', '', 'N/A', '2025-04-16 18:55:25'),
(94, '038 H.R. \"B\" MERIDA (P)', '331934', 'I450400252', 'MESA DE TRABAJO DE METAL', 'MESA DE TRABAJO DE METAL', '5101', '51101', '1994-09-30', '1994-10-14', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '747.96', '', 'N/A', '2025-04-16 18:58:06'),
(95, '038 H.R. \"B\" MERIDA (P)', '2448245', 'I060200698', 'PROCESADOR AUTOM. TEJIDOS', 'PROCESADOR AUTOMATICO DE TEJIDO', '5401', '53101', '2023-06-29', '2023-06-29', 'EQUIPO MÉDICO', '220095EM2022/ EM230004', '50044/2023', 'SERVICIO Y VENTA DE INSUMOS MEDICOS ESPECIALIZADOS, S.A. DE C.V.', '60401215-0323', 'TISSUE TEK VIP 6AI', 'SAKURA', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '999900.00', '', 'N/A', '2025-04-16 19:01:54'),
(96, '038 H.R. \"B\" MERIDA (P)', '1707339', 'I060600370', 'MICROSCOPIO BINOCULAR', 'MICROSCOPIO P/TRABAJO DE RUTINA CAMPO CL', '5401', '53101', '2007-12-20', '2007-12-18', 'BIEN', '504842007', 'S/F', 'ASESORIA Y PROVEEDORA DE EQUIPOS PARA LABORATORIOS S.A. DE C.V.', 'S/N', 'S/M', 'LEICA', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '59937.20', '', 'N/A', '2025-04-16 19:07:38'),
(97, '038 H.R. \"B\" MERIDA (P)', '331936', 'I450400248', 'MESA DE TRABAJO', 'MESA DE TRABAJO', '5101', '51101', '1998-09-30', '1991-10-24', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '780.85', '', 'N/A', '2025-04-16 19:09:40'),
(98, '038 H.R. \"B\" MERIDA (P)', '328065', 'I450205002', 'BANCOS COMBINADOS (MADERA Y METAL)', 'BANCOS COMBINADOS MADERA Y METAL', '5101', '51101', '1998-09-30', '1991-01-01', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '12.00', '', 'N/A', '2025-04-16 19:11:24'),
(99, '038 H.R. \"B\" MERIDA (P)', '331952', 'I090000198', 'ESTERILIZADORES', 'ESTERILIZADORES', '5401', '53101', '1998-09-30', '1990-01-01', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '126.20', '', 'N/A', '2025-04-16 19:13:56'),
(100, '038 H.R. \"B\" MERIDA (P)', '331956', 'I450400252', 'MESA DE TRABAJO DE METAL', 'MESA DE TRABAJO DE METAL', '5101', '51101', '1998-09-30', '1984-01-01', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '25.00', '', 'N/A', '2025-04-16 19:16:25'),
(101, '038 H.R. \"B\" MERIDA (P)', '331927', 'I450400124', 'ESCRITORIO DE METAL', 'ESCRITORIO DE METAL', '5101', '51101', '1998-09-30', '1989-01-01', 'BIEN', 'MIGRACION', 'S/F', 'MIGRACION', 'S/N', 'S/M', 'S/M', 'Bueno', 'PATOLOGIA', 'PENDIENTE ', '15.90', '', 'N/A', '2025-04-16 19:17:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos_proveedores`
--

CREATE TABLE `contactos_proveedores` (
  `id` int(11) NOT NULL,
  `proveedor_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono1` varchar(20) DEFAULT NULL,
  `telefono2` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidas`
--

CREATE TABLE `partidas` (
  `id` int(11) NOT NULL,
  `numero_partida` varchar(50) NOT NULL,
  `nombre_partida` varchar(100) NOT NULL,
  `numero_subpartida` varchar(50) NOT NULL,
  `nombre_subpartida` varchar(100) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `razon_social` varchar(100) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `telefono1` varchar(20) NOT NULL,
  `telefono2` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicaciones`
--

CREATE TABLE `ubicaciones` (
  `id` int(11) NOT NULL,
  `nombre_ubicacion` varchar(100) NOT NULL,
  `nombre_area` varchar(100) NOT NULL,
  `nombre_familia` varchar(100) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ubicaciones`
--

INSERT INTO `ubicaciones` (`id`, `nombre_ubicacion`, `nombre_area`, `nombre_familia`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Hospital ISSSTE Pensiones', 'Medicina Interna', 'Cirugía', '2025-04-04 01:16:13', '2025-04-04 01:16:13'),
(2, 'Hospital ISSSTE Pensiones', 'Medicina Interna', 'Cirugía', '2025-04-04 01:34:15', '2025-04-04 01:34:15');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_no_inventario` (`no_inventario`),
  ADD KEY `idx_proveedor` (`proveedor`),
  ADD KEY `idx_ubicacion` (`ubicacion`);

--
-- Indices de la tabla `contactos_proveedores`
--
ALTER TABLE `contactos_proveedores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proveedor_id` (`proveedor_id`);

--
-- Indices de la tabla `partidas`
--
ALTER TABLE `partidas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_partida_unica` (`numero_partida`,`numero_subpartida`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `articulos`
--
ALTER TABLE `articulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT de la tabla `contactos_proveedores`
--
ALTER TABLE `contactos_proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `partidas`
--
ALTER TABLE `partidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `contactos_proveedores`
--
ALTER TABLE `contactos_proveedores`
  ADD CONSTRAINT `contactos_proveedores_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
