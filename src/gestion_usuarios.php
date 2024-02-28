<?php
require_once('conexion_bbdd.php');
require_once("operaciones_bbdd.php");
require_once("html.php");
require_once('funciones_auxiliares.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION["rol"]) && $_SESSION["rol"] != "Administrador") {
    header("Location: index.php");
}

if(isset($_SESSION["datos_usuario"])) {
    unset($_SESSION["datos_usuario"]);
}

if(isset($_SESSION["administrador-crea-usuario"])) {
    unset($_SESSION["administrador-crea-usuario"]);
}

$conexion = conectar_bbdd();
comprobar_cerrar_sesion($conexion);
$datos = obtener_usuarios($conexion);
$accion = validar_login($conexion);
$rankingincidencias = obtener_ranking_incidencias($conexion);
$rankingcomentarios = obtener_ranking_comentarios($conexion);

HTML_inicio();
HTML_encabezado();
HTML_nav("Administrador");
HTML_contenedor_inicio();
verificar_consulta($conexion, $datos);
HTML_aside($accion, $rankingincidencias, $rankingcomentarios);
HTML_contenedor_fin();
HTML_pie_pagina();
HTML_fin();

desconectar_bbdd($conexion);

function verificar_consulta($conexion, $datos) {
    if (isset($_GET['usuario']) && isset($_GET["borrar"])) {
        $usuario = $_GET["usuario"];
        confirmar_borrado_usuario($usuario);
    } else if (isset($_GET["usuario"]) && isset($_GET["confirmar-borrar"])) {
        borrar_usuario($conexion, $_GET["usuario"]);
        $datos = obtener_usuarios($conexion);
        listado_usuarios($datos, true);
    } else {
        listado_usuarios($datos, false);
    }
}
?>