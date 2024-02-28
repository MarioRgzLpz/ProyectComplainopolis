<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

require('html.php');
require('operaciones_bbdd.php');
require('conexion_bbdd.php');
require("funciones_auxiliares.php");

$conexion = conectar_bbdd();

$accion = validar_login($conexion);

comprobar_inactivo();

$rankingincidencias = obtener_ranking_incidencias($conexion);
$rankingcomentarios = obtener_ranking_comentarios($conexion);
comprobar_cerrar_sesion($conexion);

//$tipo_usuario = isset($_SESSION["rol"]) && ($_SESSION["rol"] == "Administrador" || $_SESSION["rol"] == "Colaborador") ? "Administrador" : "Visitante";
if(isset($_SESSION['rol'])){
  $tipo_usuario = $_SESSION['rol'];
}else {
  $tipo_usuario = "Visitante";
}
$errores = "";

if (isset($_POST["registrar"])) {
  $errores = validar_datos_registro_confirmacion($conexion);
}

if (isset($_GET["gestion_usuarios"]) && isset($_GET["add"])) {
  unset($_SESSION["datos_usuario"]);
}

$registrar = "";
if (isset($_POST['confirmar-registro'])) {
  if (!isset($_SESSION["datos_usuario"]["editar"])) {
    insertar_usuario($conexion, $_SESSION['datos_usuario']);
    $registrar = "El usuario se ha registrado correctamente";
    unset($_SESSION['datos_usuario']);
  } else {
    actualizar_usuario($conexion, $_SESSION["datos_usuario"]);
    $registrar = "El usuario se ha modificado correctamente";
    unset($_SESSION["datos_usuario"]["mensaje"]);
  }  
}

if (isset($_SESSION["rol"]) && $_SESSION["rol"] == "Administrador") {
  $_SESSION["administrador_gestiona_usuario"] = true;
  modo_edicion_usuario($conexion);
} else if (isset($_SESSION["rol"]) && $_SESSION["rol"] == "Colaborador"){
  modo_edicion_usuario($conexion);
}

HTML_inicio();
HTML_encabezado();
HTML_nav($tipo_usuario);
HTML_contenedor_inicio();
FORM_registro($errores, $registrar);
HTML_aside($accion, $rankingincidencias, $rankingcomentarios);
HTML_contenedor_fin();
HTML_pie_pagina();
HTML_fin();

desconectar_bbdd($conexion);

function modo_edicion_usuario($conexion) {
  if (isset($_GET["editar"])) {
    $datos = obtener_usuario($conexion, $_GET["usuario"]);
    $_SESSION["datos_usuario"]["id_usuario"] = $_GET["usuario"];
    $_SESSION["datos_usuario"]["editar"] = true;
    $_SESSION["datos_usuario"]["nombre"] = $datos["Nombre"];
    $_SESSION["datos_usuario"]["apellidos"] = $datos["Apellidos"];
    $_SESSION["datos_usuario"]["clave"] = $datos["Clave"];
    $_SESSION["datos_usuario"]["email"] = $datos["Email"];
    $_SESSION["datos_usuario"]["direccion"] = $datos["Direccion"];
    $_SESSION["datos_usuario"]["telefono"] = $datos["Telefono"];
    $_SESSION["datos_usuario"]["rol"] = $datos["Rol"];
    $_SESSION["datos_usuario"]["estado"] = $datos["Estado"];
    $_SESSION["datos_usuario"]["foto_subida"] = $datos["Foto"];
  }
}
?>