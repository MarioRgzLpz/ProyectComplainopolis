<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

require('html.php');
require('operaciones_bbdd.php');
require('conexion_bbdd.php');
require("funciones_auxiliares.php");

$conexion = conectar_bbdd();

$tipo_usuario = isset($_SESSION["rol"]) && $_SESSION["rol"] == "Administrador" ? "Administrador" : "Visitante";

comprobar_cerrar_sesion($conexion);


HTML_inicio();
HTML_encabezado();

HTML_inactivo();

HTML_pie_pagina();
HTML_fin();

desconectar_bbdd($conexion);

?>