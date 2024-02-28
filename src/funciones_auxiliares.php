<?php
require_once("html.php");
require_once("operaciones_bbdd.php");



// Función que comprueba si se ha pulsado el boton de cerrar sesión y en tal caso la cierra
function comprobar_cerrar_sesion($conexion){
  if (isset($_POST['cerrar-sesion'])) {
    $evento = "INFO: El usuario " . $_SESSION['usuario'] . " se ha desconectado";
    session_destroy();
    insertar_log($conexion, $evento);
    header("Location: index.php");
  }
}
// Función par validar todos los datos introducidos en el formulario de nueva incidencia y si hay alguno mal devolver error
function validar_datos_incidencia() {
    $errores = array();
    $nueva_incidencia = array();
    if (empty($_POST["titulo-incidencia"])) {
        $errores["titulo"] = "El título es obligatorio";
    } else {
        $nueva_incidencia["titulo"] = validar_datos($_POST["titulo-incidencia"]);
    }

    if (empty($_POST["descripcion-incidencia"])) {
        $errores["descripcion"] = "La descripción es obligatoria";
    } else {
        $nueva_incidencia["descripcion"] = validar_datos($_POST["descripcion-incidencia"]);
    }

    if (empty($_POST["lugar-incidencia"])) {
        $errores["lugar"] = "El lugar es obligatorio";
    } else {
        $nueva_incidencia["lugar"] = validar_datos($_POST["lugar-incidencia"]);
    }

    if (empty($_POST["palabras-incidencia"])) {
        $errores["palabras_clave"] = "Las palabras clave son obligatorias";
    } else {
        $nueva_incidencia["palabras_clave"] = validar_datos($_POST["palabras-incidencia"]);
    }
    
    if (!empty($_POST["estado-incidencia"])) {
      $nueva_incidencia["estado_incidencia"] = $_POST["estado-incidencia"];
    }

    if (isset($_FILES['fotos-incidencia'])) {
      $total_files = count($_FILES['fotos-incidencia']['name']);
      $nueva_incidencia['numero_fotos'] = $total_files;
      // Recorrer cada archivo subido
      for ($i = 0; $i < $total_files; $i++) {
        $archivo_temporal = $_FILES['fotos-incidencia']['tmp_name'][$i];
        $tam_archivo = $_FILES['fotos-incidencia']['size'][$i];
        $tipo_archivo = $_FILES['fotos-incidencia']['type'][$i];
        // Comprobación de tipo de archivo
        if (in_array($tipo_archivo, ['image/jpeg', 'image/png'])) {
          $extension = $tipo_archivo == 'image/jpeg' ? '.jpeg' : '.png';
          // Comprobación de tamaño máximo
          $max = 5 * 1024 * 1024; // 5 MB
          if ($tam_archivo < $max) {
            $ruta = './img/' . uniqid() . $extension;
            move_uploaded_file($archivo_temporal, $ruta);
            $nueva_incidencia["fotos_incidencia"][$i] = $ruta;
          }
        }
      }
      $nueva_incidencia["confirmar_2"] = true;
    }

    if (empty($errores)) {
        $nueva_incidencia["confirmar"] = true;
        if(isset($_POST["enviar-incidencia-2"]))
        $nueva_incidencia["confirmar_2"] = true;
    }
    if(isset($_SESSION["datos_incidencia"]["id_incidencia"])){
      $nueva_incidencia["id_incidencia"] = $_SESSION["datos_incidencia"]["id_incidencia"];
    }

      var_dump($nueva_incidencia);
    return [$nueva_incidencia, $errores];
}

/* 
Función para validar todos los datos introducidos en el formulario de registro y si hay alguno mal devolver error
Se utiliza en formularios tanto de registro de usuarios como de modificación 
*/
function validar_datos_registro_confirmacion($conexion) {
    $errores = array();
    $usuario = array();
    $email_valido = $_POST['email'];
    $telefono_valido = $_POST['telefono'];

    if (!isset($_SESSION["datos_usuario"]["editar"])) {
      if (empty($_POST['id-usuario'])) {
        $errores["id_usuario"] = 'El usuario es obligatorio';
      } else {
        $usuario['id_usuario'] = validar_datos($_POST['id-usuario']);
        $comprobacion = comprobar_usuario($conexion, $usuario['id_usuario']);
        if ($comprobacion == true) {
            $errores["id_usuario"] = 'Usuario ya existente';
        }
      }
    } else {
      $usuario["id_usuario"] = $_SESSION["datos_usuario"]["id_usuario"];
    }

    if (isset($_FILES['foto-perfil']) && $_FILES['foto-perfil']['error'] === 0) {
      $archivo_temporal = $_FILES['foto-perfil']['tmp_name'];
      $tipo_archivo = $_FILES['foto-perfil']['type'];
      $tam_archivo = $_FILES['foto-perfil']['size'];
      // Comprobación de tipo de archivo
      if (in_array($tipo_archivo, ['image/jpeg', 'image/png'])) {
        $extension = $tipo_archivo == 'image/jpeg' ? '.jpeg' : '.png';
        // Comprobación de tamaño máximo
        $max = 5 * 1024 * 1024; // 5 MB
        if ($tam_archivo < $max) {
          $ruta = './img/' . uniqid() . $extension;
          move_uploaded_file($archivo_temporal, $ruta);
          $usuario['foto_subida'] = $ruta;
        } else {
          $errores['foto_subida'] = 'La foto de perfil no ha sido subida';
        }
      }
    } else if (isset($_SESSION["datos_usuario"]["foto_subida"])) {
      $usuario["foto_subida"] = $_SESSION["datos_usuario"]["foto_subida"];
    } else {
      $usuario["foto_subida"] = "./img/perfil_normal.png";
    }
      
    if (empty($_POST['nombre-registro'])) {
        $errores["nombre"] = 'El nombre es obligatorio';
    } else {
          $usuario['nombre'] = validar_datos($_POST['nombre-registro']);
    }

    if (empty($_POST['apellidos'])) {
        $errores["apellidos"] = 'Los apellidos son obligatorios';
    } else {
        $usuario['apellidos'] = validar_datos($_POST['apellidos']);
    }

    if (!filter_var($email_valido, FILTER_VALIDATE_EMAIL)) {
        $errores["email"] = 'El email no es válido';
    } else  if (!isset($_SESSION["datos_usuario"]["editar"])) {
      $usuario['email'] = validar_datos($_POST['email']);
      $comprobacion = comprobar_email($conexion,$usuario['email']);
      if ($comprobacion == true) {
          $errores["email"] = 'Email en uso';
      }
    } else {
      $usuario["email"] = $_SESSION["datos_usuario"]["email"];
    }

    if (empty($_POST['direccion'])) {
      $errores["direccion"] = 'La direccion es obligatoria';
    } else {
        $usuario['direccion'] = validar_datos($_POST['direccion']);
    }

    if (empty($_POST["password"]) && isset($_SESSION["datos_usuario"]["editar"]) && $_SESSION["datos_usuario"]["editar"]) {
      $usuario["clave"] = $_SESSION["datos_usuario"]["clave"];
      $usuario["hash"] = true;
    } else if (empty($_POST['password'])) {
      $errores["clave"] = 'La contraseña es obligatoria';
    } else {
      $usuario['clave'] = validar_datos($_POST['password']);
      if (empty($_POST['password-2'])){
        $errores["clave"] = 'Confirme su contraseña';
      } else{
        $usuario['clave_2'] = validar_datos($_POST['password-2']);
        if ($usuario['clave'] != $usuario['clave_2']) {
          $errores["clave"] = 'Las contraseñas no coinciden';
        }
      }
    }

    if (empty($_POST["rol"]) && isset($_SESSION["datos_usuario"]["rol"])) {
      $usuario["rol"] = $_SESSION["datos_usuario"]["rol"];
    } else if (empty($_POST["rol"])) {
      $usuario["rol"] = "Colaborador";
    } else {
      $usuario["rol"] = $_POST["rol"];
    }

    if (empty($_POST["estado"]) && isset($_SESSION["datos_usuario"]["estado"])) {
      $usuario["estado"] = $_SESSION["datos_usuario"]["estado"];
    } else if (empty($_POST["estado"])) {
      $usuario["estado"] = "Inactivo";
    } else {
      $usuario["estado"] = $_POST["estado"];
    }

    if (!preg_match('/^\d{9}$/', $telefono_valido)) {
        $errores["telefono"] = 'El teléfono no es valido';
    } else {
        $usuario['telefono'] = validar_datos($_POST['telefono']);
    }

    if(empty($errores)){
      $usuario['mensaje'] = 'Usuario registrado correctamente';
    } else {
        $errores['error'] = 'Hay errores';
    }

    if (!isset($_SESSION["datos_usuario"]["editar"])) {
      $_SESSION['datos_usuario'] = $usuario;
    } else {
      $_SESSION["datos_usuario"] = $usuario;
      $_SESSION["datos_usuario"]["editar"] = true;
    }

    return $errores;
}

// Función para validar los ficheros para restaurar la base de datos
function validar_fichero_restauracion(){
  if(isset($_FILES['fichero-restaurarbbdd'])){
    $archivo_temporal = $_FILES['fichero-restaurarbbdd']['tmp_name'];
    $tipo_archivo = $_FILES['fichero-restaurarbbdd']['type'];
    $tam_archivo = $_FILES['fichero-restaurarbbdd']['size'];
    // Comprobación de tipo de archivo
    if ($tipo_archivo === 'aplication/sql'){
      // Comprobación de tamaño máximo
      $max = 10 * 1024 * 1024; // 10 MB
      $ruta = "";
      if ($tam_archivo < $max) {
        $ruta = 'fichero_restaurar.sql';
        move_uploaded_file($archivo_temporal, $ruta);
      } else {
        $errores['tamaño_archivo'] = 'Archivo demasiado grande';
      }
    }else {
      $errores['extension_archivo'] = 'Archivo con extension no permitida';
    }

  } else {
    $errores['archivo_subido'] = 'No se ha subido ningun fichero para restaurar la base de datos';
  }

  return [$ruta, $errores];
}


function validar_valoracion($conexion){
  if(isset($_GET['id_incidencia']) && isset($_GET['valoracion'])){
    $id_incidencia = $_GET['id_incidencia'];
    $valoracion = $_GET['valoracion'];
    if($valoracion == "dislike"){
      $valor = 0;
    }else if($valoracion == "like"){
      $valor = 1;
    }
    if(isset($_SESSION['usuario'])){
      comprobar_valoracion($conexion, $id_incidencia, $_SESSION['usuario'], $valor);
    }else {
      if (isset($_COOKIE["anonimo_valora"]) && $_COOKIE["anonimo_valora"] === "true") {
        comprobar_valoracion($conexion, $id_incidencia, $_COOKIE["anonimo_valora"], $valor);
      }else {
        setcookie("anonimo_valora", "true", time() + 36000);
      }
    }
  }
}

function validar_comentario(){
  $errores = array();
  $comentario = array();
  if (empty($_POST["comentario-incidencia"])) {
      $comentario["error"] = "No se puede enviar un comentario vacio";
  } else {
      $comentario["texto"] = validar_datos($_POST["comentario-incidencia"]);
      $comentario["mensaje"] = "Comentario añadido correctamente";
      
  }

  return $comentario;
}

function validar_datos_filtro(){
  $filtro = array();
  if(isset($_POST['orden'])){
    $filtro['orden_filtro'] = $_POST['orden'];
  }
  else {
    $_SESSION['errores_filtro']['orden'] = "Introduce el orden";
  }

  if(!empty($_POST['busqueda-filtro'])){
    $filtro['busqueda_filtro'] = validar_datos($_POST['busqueda-filtro']);
  }else {
    $filtro['busqueda_filtro'] = "";
  }  
  if(isset($_POST['lugar-filtro'])){
    $filtro['lugar_filtro'] = $_POST['lugar-filtro'];
  }

  if(isset($_POST['estado-filtro'])){
    $filtro['estado_filtro'] = $_POST['estado-filtro'];
  }
  else {
    $_SESSION['errores_filtro']['estado'] = "Introduce al menos un estado";
  }

  return $filtro;
}

function comprobar_inactivo(){
  if (($_SESSION["rol"] == "Colaborador" || $_SESSION["rol"] == "Administrador") && $_SESSION["estado"] == "Inactivo") {
    header("Location: inactivo.php");
  }
}

function filtro_mis_incidencias($incidencias, $usuario){
  $datos = array();
  $i=0;
  foreach($incidencias as $incidencia){
    if($incidencia['ID_usuario'] == $usuario){
      $datos[$i] = $incidencia;
      $i++;
    }
  }
  return $datos;
}

?>