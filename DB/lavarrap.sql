-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 31-07-2018 a las 03:17:21
-- Versión del servidor: 10.1.28-MariaDB
-- Versión de PHP: 5.6.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `lavarrap`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--

CREATE TABLE `caja` (
  `codigo` bigint(20) NOT NULL,
  `monto` double NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `codigoSucursal` bigint(20) NOT NULL,
  `observaciones` varchar(500) COLLATE utf8_spanish_ci NOT NULL,
  `esSaldoInicialMes` tinyint(1) NOT NULL DEFAULT '0',
  `conDebito` tinyint(3) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `codigo` bigint(20) UNSIGNED NOT NULL,
  `nombres` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `apellido` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `direccion` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `telefono` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fechaNacimiento` date DEFAULT NULL,
  `codigoSucursal` bigint(20) DEFAULT NULL,
  `activo` tinyint(4) NOT NULL DEFAULT '1',
  `tieneCuentaCorriente` tinyint(4) NOT NULL DEFAULT '0',
  `email` varchar(200) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentaCorriente`
--

CREATE TABLE `cuentaCorriente` (
  `codigo` bigint(20) UNSIGNED NOT NULL,
  `codigoCliente` bigint(20) UNSIGNED DEFAULT NULL,
  `codigoPedido` bigint(20) UNSIGNED DEFAULT NULL,
  `monto` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE `estados` (
  `codigo` bigint(20) NOT NULL,
  `descripcion` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `activo` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos`
--

CREATE TABLE `insumos` (
  `codigo` bigint(20) NOT NULL,
  `descripcion` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `activo` tinyint(4) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumosIngresos`
--

CREATE TABLE `insumosIngresos` (
  `codigo` bigint(20) NOT NULL,
  `codigoInsumo` bigint(20) DEFAULT NULL,
  `cantidad` double DEFAULT NULL,
  `codigoSucursal` bigint(20) DEFAULT NULL,
  `fechaIngreso` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumosServicios`
--

CREATE TABLE `insumosServicios` (
  `codigo` bigint(20) NOT NULL,
  `codigoInsumo` bigint(20) DEFAULT NULL,
  `codigoServicio` bigint(20) DEFAULT NULL,
  `cantidad` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log4php_log`
--

CREATE TABLE `log4php_log` (
  `timestamp` datetime DEFAULT NULL,
  `logger` varchar(256) DEFAULT NULL,
  `level` varchar(32) DEFAULT NULL,
  `message` varchar(4000) DEFAULT NULL,
  `thread` int(11) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `line` varchar(10) DEFAULT NULL,
  `id` bigint(20) UNSIGNED NOT NULL,
  `sucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log4php_pedidos`
--

CREATE TABLE `log4php_pedidos` (
  `timestamp` datetime DEFAULT NULL,
  `logger` varchar(256) DEFAULT NULL,
  `level` varchar(32) DEFAULT NULL,
  `message` varchar(4000) DEFAULT NULL,
  `thread` int(11) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `line` varchar(10) DEFAULT NULL,
  `id` bigint(20) UNSIGNED NOT NULL,
  `sucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `codigo` bigint(20) UNSIGNED NOT NULL,
  `codigoTalon` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fechaPedido` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fechaRetiro` date DEFAULT NULL,
  `codigoCliente` bigint(20) DEFAULT NULL,
  `codigoSucursal` int(11) DEFAULT NULL,
  `anticipo` int(11) NOT NULL DEFAULT '0',
  `entregado` tinyint(4) NOT NULL DEFAULT '0',
  `activo` tinyint(4) NOT NULL DEFAULT '1',
  `aCobrar` double NOT NULL DEFAULT '0',
  `observaciones` varchar(400) COLLATE utf8_spanish_ci NOT NULL DEFAULT '""'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidosCaja`
--

CREATE TABLE `pedidosCaja` (
  `codigoPedido` bigint(20) NOT NULL,
  `codigoCaja` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `codigo` bigint(20) NOT NULL,
  `descripcion` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `esSucursal` tinyint(4) DEFAULT NULL,
  `prefijoCodigo` varchar(3) COLLATE utf8_spanish_ci DEFAULT NULL,
  `activo` tinyint(4) NOT NULL DEFAULT '1',
  `direccion` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `zona` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `telefono` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `valor` double DEFAULT NULL,
  `codigoSucursal` bigint(20) DEFAULT NULL,
  `activo` tinyint(4) NOT NULL DEFAULT '1',
  `fechaVigencia` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `serviciosPedidos`
--

CREATE TABLE `serviciosPedidos` (
  `codigo` bigint(20) UNSIGNED NOT NULL,
  `codigoPedido` bigint(20) DEFAULT NULL,
  `codigoServicio` bigint(20) DEFAULT NULL,
  `cantidad` double DEFAULT NULL,
  `codigoProveedor` bigint(20) DEFAULT NULL,
  `codigoEstado` tinyint(4) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `codigo` bigint(20) NOT NULL,
  `nombre` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `clave` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codigoSucursal` bigint(20) DEFAULT NULL,
  `esAdministrador` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `caja`
--
ALTER TABLE `caja`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `idx_observacioones` (`observaciones`(255));

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `cuentaCorriente`
--
ALTER TABLE `cuentaCorriente`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `insumos`
--
ALTER TABLE `insumos`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `insumosIngresos`
--
ALTER TABLE `insumosIngresos`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `insumosServicios`
--
ALTER TABLE `insumosServicios`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `log4php_log`
--
ALTER TABLE `log4php_log`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `log4php_pedidos`
--
ALTER TABLE `log4php_pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `fechaPedido` (`fechaPedido`),
  ADD KEY `codigoCliente` (`codigoCliente`),
  ADD KEY `codigoTalon` (`codigoTalon`);

--
-- Indices de la tabla `pedidosCaja`
--
ALTER TABLE `pedidosCaja`
  ADD PRIMARY KEY (`codigoPedido`,`codigoCaja`),
  ADD KEY `codigoPedido` (`codigoPedido`,`codigoCaja`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `serviciosPedidos`
--
ALTER TABLE `serviciosPedidos`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `codigoPedido` (`codigoPedido`),
  ADD KEY `codigoServicio` (`codigoServicio`),
  ADD KEY `codigoProveedor` (`codigoProveedor`),
  ADD KEY `codigoEstado` (`codigoEstado`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`codigo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `caja`
--
ALTER TABLE `caja`
  MODIFY `codigo` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90379;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `codigo` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17586;

--
-- AUTO_INCREMENT de la tabla `cuentaCorriente`
--
ALTER TABLE `cuentaCorriente`
  MODIFY `codigo` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1158;

--
-- AUTO_INCREMENT de la tabla `log4php_log`
--
ALTER TABLE `log4php_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `log4php_pedidos`
--
ALTER TABLE `log4php_pedidos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `codigo` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79205;

--
-- AUTO_INCREMENT de la tabla `serviciosPedidos`
--
ALTER TABLE `serviciosPedidos`
  MODIFY `codigo` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118517;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
