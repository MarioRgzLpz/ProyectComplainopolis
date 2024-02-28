<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

require('html.php');
require('operaciones_bbdd.php');
require('conexion_bbdd.php');
require("funciones_auxiliares.php");

$conexion = conectar_bbdd();
$rankingincidencias = obtener_ranking_incidencias($conexion);
$rankingcomentarios = obtener_ranking_comentarios($conexion);

$tipo_usuario = isset($_SESSION["rol"]) && $_SESSION["rol"] == "Administrador" ? "Administrador" : "Visitante";

if (isset($_SESSION["rol"]) && $_SESSION["rol"] == "Colaborador") {
  header("Location: index.php");
}


HTML_inicio();
HTML_encabezado();
HTML_nav($tipo_usuario);
HTML_contenedor_inicio();

$archivo = obtener_fichero_bbdd($conexion);
HTML_basededatos($archivo);

$accion = validar_login($conexion);

comprobar_inactivo();

HTML_aside($accion,$rankingincidencias,$rankingcomentarios);

HTML_contenedor_fin();
HTML_pie_pagina();
HTML_fin();

comprobar_cerrar_sesion($conexion);
desconectar_bbdd($conexion);

function obtener_fichero_bbdd($conexion){
    // Obtener nombres de tablas
    $tables = array();
    $result = mysqli_query($conexion, "SHOW TABLES");
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }
    $sql = "";
    foreach ($tables as $table) {
        $result = mysqli_query($conexion, "SHOW CREATE TABLE $table");
        $row = mysqli_fetch_row($result);
        $sql .= $row[1] . ";\n\n";
    }
    foreach ($tables as $table) {
      $result = mysqli_query($conexion, "SELECT * FROM $table");
      while ($row = mysqli_fetch_assoc($result)) {
          $row = array_map('addslashes', $row);
          $sql .= "INSERT INTO $table VALUES ('" . implode("', '", $row) . "');\n";
      }
      $sql .= "\n";
    }
    $archivo = 'fichero_bbdd.sql';
    file_put_contents($archivo, $sql);
    return $archivo;
}

?>

