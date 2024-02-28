<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }

if (isset($_SESSION["rol"]) && $_SESSION["rol"] != "Administrador") {
  header("Location: index.php");
}

require('html.php');
require('operaciones_bbdd.php');
require('conexion_bbdd.php');
require('funciones_auxiliares.php');


$conexion = conectar_bbdd();
comprobar_cerrar_sesion($conexion);
$datos = obtener_log($conexion);
$accion = validar_login($conexion);

comprobar_inactivo();


$rankingincidencias = obtener_ranking_incidencias($conexion);
$rankingcomentarios = obtener_ranking_comentarios($conexion);

HTML_inicio();
HTML_encabezado();
HTML_nav("Administrador");
HTML_contenedor_inicio();
HTML_log($datos);
HTML_aside($accion, $rankingincidencias, $rankingcomentarios);
HTML_contenedor_fin();
HTML_pie_pagina();
HTML_fin();

desconectar_bbdd($conexion);
?>

