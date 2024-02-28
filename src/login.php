<?php
require('html.php');
require_once "conexion_bbdd.php";

if (session_status() == PHP_SESSION_NONE)
    session_start();

$conexion = conectar_bbdd();
function Validar_datos(){
    
}
?>