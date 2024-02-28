<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['rol'])) {
    $_SESSION['rol'] = 'Visitante';
}

if (!($_SESSION["rol"] == "Colaborador" || $_SESSION["rol"] == "Administrador")) {
    header("Location: index.php");
}

require_once("html.php");
require_once("conexion_bbdd.php");
require_once("funciones_auxiliares.php");

$conexion = conectar_bbdd();

$accion = validar_login($conexion);

comprobar_inactivo();

$rankingincidencias = obtener_ranking_incidencias($conexion);
$rankingcomentarios = obtener_ranking_comentarios($conexion);

if (isset($_SERVER['HTTP_REFERER']) && ($_SERVER['HTTP_REFERER'] !== $_SERVER['PHP_SELF']) && !isset($_POST["confirmar-incidencia"]) && !isset($_POST["enviar-incidencia-2"]) && !isset($_POST["confirmar-incidencia-2"])){
    // Borrar todas las variables de sesión
    unset($_SESSION["datos_incidencia"]);
    unset($_SESSION["errores_incidencia"]);
}

if (isset($_POST["enviar-incidencia"]) || isset($_POST["enviar-incidencia-2"])) {
    [$_SESSION["datos_incidencia"], $_SESSION["errores_incidencia"]] = validar_datos_incidencia();
}

if (isset($_POST["enviar-incidencia-2"])){
    $_SESSION["datos_incidencia"]["enviar_2"] = true;
}


if (isset($_POST["confirmar-incidencia"])) {
    $_SESSION["datos_incidencia"]["id_incidencia"] = insertar_incidencia($conexion, $_SESSION["datos_incidencia"], $_SESSION["usuario"]);
    $_SESSION["datos_incidencia"]["estado_incidencia"] = "Pendiente";
    unset($_SESSION["datos_incidencia"]["confirmar"]);
    unset($_SESSION["errores_incidencia"]);
}

if(isset($_GET['editar-fuera']) || isset($_SESSION["datos_incidencia"]['editando'])){
    modo_edicion_incidencia($conexion);
}


HTML_inicio();
HTML_encabezado();
HTML_nav($_SESSION["rol"]);
HTML_contenedor_inicio();
if (isset($_POST["confirmar-incidencia"]) ||  isset($_SESSION["datos_incidencia"]["enviar_2"]) || isset($_POST['confirmar-incidencia-2']) || isset($_GET['editar-fuera'])) {
    if(isset($_POST['confirmar-incidencia-2'])) {
        actualizar_incidencia($conexion, $_SESSION['datos_incidencia']);
        if(isset($_SESSION["datos_incidencia"]["fotos_incidencia"])){
            foreach ($_SESSION["datos_incidencia"]["fotos_incidencia"] as $foto){
                insertar_foto_incidencia($conexion, $_SESSION["id_incidencia"], $foto);
            }
        }
        unset($_SESSION["datos_incidencia"]);
        unset($_SESSION["errores_incidencia"]);
        header("Location: nueva_incidencia.php");
    }
    FORM_incidencia('editar');
}
else{
    FORM_incidencia('normal');
}
HTML_aside($accion, $rankingincidencias, $rankingcomentarios);
HTML_contenedor_fin();
HTML_pie_pagina();
HTML_fin();

comprobar_cerrar_sesion($conexion);
desconectar_bbdd($conexion);

function modo_edicion_incidencia($conexion) {
    if (isset($_GET["id_incidencia"])) {
      $datos = obtener_incidencia($conexion, $_GET["id_incidencia"]);
      $_SESSION["datos_incidencia"]["id_incidencia"] = $_GET["id_incidencia"];
      $_SESSION["datos_incidencia"]["titulo"] = $datos["Titulo"];
      $_SESSION["datos_incidencia"]["descripcion"] = $datos["Descripcion"];
      $_SESSION["datos_incidencia"]["palabras_clave"] = $datos["Palabras_clave"];
      $_SESSION["datos_incidencia"]["lugar"] = $datos["Lugar"];
      $_SESSION["datos_incidencia"]["estado_incidencia"] = $datos["Estado"];
      $_SESSION["datos_incidencia"]["fotos_incidencia"] = obtener_fotos($conexion, $_GET["id_incidencia"]);
      $_SESSION["datos_incidencia"]['editando'] = true;

    }
  }

?>