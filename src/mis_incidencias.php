<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['rol'])) {
    $_SESSION['rol'] = 'Visitante';
}

require("./html.php");
require('operaciones_bbdd.php');
require('conexion_bbdd.php');
require_once('funciones_auxiliares.php');


$conexion = conectar_bbdd();
crear_administrador($conexion);
$accion = validar_login($conexion);
comprobar_cerrar_sesion($conexion);

comprobar_inactivo();

//Comprobamos si el usuario a decidido borrar una incidencia

if (isset($_GET['id_incidencia']) && isset($_GET["confirmar-borrar"])) {
    $id_incidencia = $_GET["id_incidencia"];
    borrar_incidencia($conexion, $id_incidencia);
}

//Comprobamos si se ha enviado un comentario
if(isset($_POST['enviar-comentario']) && isset($_GET['id_incidencia'])){
    $comentario = validar_comentario();
    if(empty($comentario["error"])){
        $id_incidencia = $_GET["id_incidencia"];
        insertar_comentario($conexion, $_SESSION['usuario'], $id_incidencia, $comentario["texto"]);

    }
}

validar_valoracion($conexion);

if(isset($_POST['boton-filtro'])){
    $_SESSION['datos_filtro'] = validar_datos_filtro();
    if(isset($_SESSION['datos_filtro']['estado_filtro']) && isset($_SESSION['datos_filtro']['orden_filtro'])){
        if($_SESSION['datos_filtro']['orden_filtro'] == "positivos_neto"){
            $incidencias =  obtener_incidencias_filtro_positivo_neto($conexion, $_SESSION['datos_filtro']['lugar_filtro'],$_SESSION['datos_filtro']['busqueda_filtro'], $_SESSION['datos_filtro']['estado_filtro']);
        }
        else if ($_SESSION['datos_filtro']['orden_filtro'] == "positivos"){
            $incidencias = obtener_incidencias_filtro_positivo($conexion, $_SESSION['datos_filtro']['lugar_filtro'],$_SESSION['datos_filtro']['busqueda_filtro'], $_SESSION['datos_filtro']['estado_filtro']);
        } else {
            $incidencias = obtener_incidencias_filtro_fecha($conexion, $_SESSION['datos_filtro']['lugar_filtro'],$_SESSION['datos_filtro']['busqueda_filtro'], $_SESSION['datos_filtro']['estado_filtro']);
        }
    }{
        $incidencias = obtener_incidencias($conexion);
    }
}else {
    $incidencias = obtener_incidencias($conexion);
    unset($_SESSION['datos_filtro']);
}

$datos = filtro_mis_incidencias($incidencias,$_SESSION['usuario']);

foreach($datos as &$incidencia){
    [$incidencia['Valoraciones_positivas'], $incidencia['Valoraciones_negativas']] = obtener_valoraciones_incidencia($conexion, $incidencia['ID_incidencia']);
}

$rankingincidencias = obtener_ranking_incidencias($conexion);
$rankingcomentarios = obtener_ranking_comentarios($conexion);
$lugares = obtener_lugares($conexion);

HTML_inicio();
HTML_encabezado();
HTML_nav($_SESSION['rol']);
HTML_contenedor_inicio();
HTML_mostrar_incidencia($datos,$conexion, $lugares);
HTML_aside($accion,$rankingincidencias,$rankingcomentarios);
HTML_contenedor_fin();
HTML_pie_pagina();
HTML_fin();

desconectar_bbdd($conexion);
?>