-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-04-2021 a las 05:11:32
-- Versión del servidor: 10.4.13-MariaDB
-- Versión de PHP: 7.4.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ichiban`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `idcliente` int(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `cuit` varchar(255) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `desactivado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`idcliente`, `nombre`, `cuit`, `direccion`, `desactivado`) VALUES
(1, 'Ichiban', '123', '0', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egreso`
--

CREATE TABLE `egreso` (
  `idegreso` int(255) NOT NULL,
  `idegresoxproducto` int(255) NOT NULL,
  `idegresoxstock` int(255) NOT NULL,
  `patente` varchar(255) NOT NULL,
  `chofer` varchar(255) NOT NULL,
  `idhistorial` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egresoxproducto`
--

CREATE TABLE `egresoxproducto` (
  `idegresoxproducto` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egresoxstock`
--

CREATE TABLE `egresoxstock` (
  `idegresoxstock` int(255) NOT NULL,
  `idegreso` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habilidad`
--

CREATE TABLE `habilidad` (
  `idhabilidad` int(255) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial`
--

CREATE TABLE `historial` (
  `idhistorial` int(255) NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `dia` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso`
--

CREATE TABLE `ingreso` (
  `idingreso` int(255) NOT NULL,
  `idingresoxproducto` int(255) NOT NULL,
  `idingresoxstock` int(255) NOT NULL,
  `idusuario` int(255) NOT NULL,
  `idhistorial` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresoxproducto`
--

CREATE TABLE `ingresoxproducto` (
  `idingreso` int(255) NOT NULL,
  `idproducto` int(255) NOT NULL,
  `idingresoxproducto` int(255) NOT NULL,
  `cantidad` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresoxstock`
--

CREATE TABLE `ingresoxstock` (
  `idingresoxstock` int(255) NOT NULL,
  `idstock` int(255) NOT NULL,
  `idingreso` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `idproducto` int(255) NOT NULL,
  `precioFresco` varchar(255) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `precioCongelado` varchar(255) NOT NULL,
  `stockFresco` varchar(255) NOT NULL,
  `stockCongelado` varchar(255) NOT NULL,
  `desactivado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`idproducto`, `precioFresco`, `nombre`, `precioCongelado`, `stockFresco`, `stockCongelado`, `desactivado`) VALUES
(1, '1232', 'qwe', '1232', '-582', '1232', 0),
(2, '234', 'Testing', '1500', '2244', '222', 0),
(3, '202', 'Testing', '202', '202', '2022', 0),
(4, '231', 'qwe', '1234', '123', '5', 0),
(5, '202', 'Testing', '202', '202', '202', 0),
(6, '123', 'qwe', '123', '123', '123', 0),
(7, '2342', 'Testinge', '1500', '2244', '2', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productosxstock`
--

CREATE TABLE `productosxstock` (
  `idproductosxstock` int(255) NOT NULL,
  `idproducto` int(255) NOT NULL,
  `idstock` int(255) NOT NULL,
  `cantidad` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock`
--

CREATE TABLE `stock` (
  `idstock` int(255) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `stock`
--

INSERT INTO `stock` (`idstock`, `nombre`) VALUES
(1, 'fresco'),
(2, 'congelado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idusuario` int(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuariosxhabilidad`
--

CREATE TABLE `usuariosxhabilidad` (
  `idhabilidadxusuario` int(255) NOT NULL,
  `idusuario` int(255) NOT NULL,
  `idhabilidad` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`idcliente`);

--
-- Indices de la tabla `egreso`
--
ALTER TABLE `egreso`
  ADD PRIMARY KEY (`idegreso`);

--
-- Indices de la tabla `egresoxproducto`
--
ALTER TABLE `egresoxproducto`
  ADD PRIMARY KEY (`idegresoxproducto`);

--
-- Indices de la tabla `egresoxstock`
--
ALTER TABLE `egresoxstock`
  ADD PRIMARY KEY (`idegresoxstock`);

--
-- Indices de la tabla `habilidad`
--
ALTER TABLE `habilidad`
  ADD PRIMARY KEY (`idhabilidad`);

--
-- Indices de la tabla `historial`
--
ALTER TABLE `historial`
  ADD PRIMARY KEY (`idhistorial`);

--
-- Indices de la tabla `ingreso`
--
ALTER TABLE `ingreso`
  ADD PRIMARY KEY (`idingreso`);

--
-- Indices de la tabla `ingresoxproducto`
--
ALTER TABLE `ingresoxproducto`
  ADD PRIMARY KEY (`idingresoxproducto`);

--
-- Indices de la tabla `ingresoxstock`
--
ALTER TABLE `ingresoxstock`
  ADD PRIMARY KEY (`idingresoxstock`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`idproducto`);

--
-- Indices de la tabla `productosxstock`
--
ALTER TABLE `productosxstock`
  ADD PRIMARY KEY (`idproductosxstock`);

--
-- Indices de la tabla `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`idstock`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idusuario`);

--
-- Indices de la tabla `usuariosxhabilidad`
--
ALTER TABLE `usuariosxhabilidad`
  ADD PRIMARY KEY (`idhabilidadxusuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `idcliente` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `egreso`
--
ALTER TABLE `egreso`
  MODIFY `idegreso` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `egresoxproducto`
--
ALTER TABLE `egresoxproducto`
  MODIFY `idegresoxproducto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `egresoxstock`
--
ALTER TABLE `egresoxstock`
  MODIFY `idegresoxstock` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `habilidad`
--
ALTER TABLE `habilidad`
  MODIFY `idhabilidad` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial`
--
ALTER TABLE `historial`
  MODIFY `idhistorial` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingreso`
--
ALTER TABLE `ingreso`
  MODIFY `idingreso` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingresoxproducto`
--
ALTER TABLE `ingresoxproducto`
  MODIFY `idingresoxproducto` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingresoxstock`
--
ALTER TABLE `ingresoxstock`
  MODIFY `idingresoxstock` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `idproducto` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `productosxstock`
--
ALTER TABLE `productosxstock`
  MODIFY `idproductosxstock` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `stock`
--
ALTER TABLE `stock`
  MODIFY `idstock` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idusuario` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuariosxhabilidad`
--
ALTER TABLE `usuariosxhabilidad`
  MODIFY `idhabilidadxusuario` int(255) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
