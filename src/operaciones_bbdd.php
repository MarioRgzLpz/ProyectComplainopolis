<?php
// Función que crea un administrador por defecto si la BBDD está vacía
function crear_administrador($conexion) {
    $consulta = "SELECT COUNT(*) AS total FROM usuario";
    $resultado = mysqli_fetch_assoc(mysqli_query($conexion, $consulta));
    if ($resultado["total"] == 0) {
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $consulta = <<<EOD
            INSERT INTO usuario
            VALUES ('administrador', 'Javier', 'Sanchez', 'javisanchez@gmail.es', '$password',
            'Universidad de Granada', '666666666', 'Administrador', 'Activo', './img/perfil_admin.png')
        EOD;
        $resultado = mysqli_query($conexion, $consulta);

        $evento = "INFO: Se ha añadido el usuario administrador por defecto";
        insertar_log($conexion, $evento);
    }
}

// Función que obtiene los eventos del sistema de la BBDD
function obtener_log($conexion) {
    $consulta = "SELECT Fecha, Descripcion FROM log ORDER BY Fecha DESC LIMIT 10";
    $c = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_execute($c);
    $resultado = mysqli_stmt_get_result($c);
    $datos = array();
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $datos[] = $fila;
    }

    mysqli_stmt_close($c);
    return $datos;
}

// Función que añade eventos al log
function insertar_log($conexion, $evento) {
    $consulta = "INSERT INTO log (Descripcion) VALUES (?)";
    $c = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($c, "s", $evento);
    mysqli_execute($c);
    mysqli_stmt_close($c);
}

// Función básica para validar datos insertados en un formulario
function validar_datos($datos){
    $datos = trim($datos);
    $datos = stripslashes($datos);
    $datos = htmlspecialchars($datos);
    return $datos;
}

/* 
Función que obtiene todos los usuarios de la BBDD exceptuando al administrador
que utilice la función, para evitar el autoborrodo
*/
function obtener_usuarios($conexion) {
    $usuario = $_SESSION["usuario"];
    $consulta = <<<EOD
        SELECT ID_usuario, Nombre, Apellidos, Email, Direccion, Telefono, Rol, Estado, Foto
        FROM usuario WHERE ID_usuario != ?;
    EOD;

    $c = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($c, "s", $usuario);
    mysqli_stmt_execute($c);
    $resultado = mysqli_stmt_get_result($c);
    $datos = array();
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $datos[] = $fila;
    }

    mysqli_stmt_close($c);

    return $datos;
}

// Función que obtiene los datos ID_usuario = id (un único usuario)
function obtener_usuario($conexion, $id) {
    $consulta = "SELECT * FROM usuario WHERE ID_usuario = ?";
    $c = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($c, "s", $id);
    mysqli_stmt_execute($c);
    $resultado = mysqli_stmt_get_result($c);
    $datos = array();
    $datos = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($c);

    return $datos;
}

function insertar_usuario($conexion, $datos) {
    $password = password_hash($datos['clave'], PASSWORD_DEFAULT);
    if (isset($datos["foto_subida"])) {
        $foto = $datos["foto_subida"];
    } else {
        $foto = "./img/perfil_normal.png";
    }

    $consulta = <<<EOD
        INSERT INTO usuario(ID_usuario, Nombre, Apellidos, Email, Clave, Direccion, Telefono, Rol, Estado, Foto)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
    EOD;

    $c = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($c, "ssssssisss", $datos['id_usuario'], $datos['nombre'], $datos['apellidos'], 
        $datos['email'], $password, $datos['direccion'], $datos['telefono'], 
        $datos["rol"], $datos["estado"], $foto);
    mysqli_stmt_execute($c);
    mysqli_stmt_close($c);
    $evento = "INFO: Se ha añadido el usuario " . $datos["id_usuario"];
    insertar_log($conexion, $evento);
}

function actualizar_usuario($conexion, $datos) {
    if (isset($datos["hash"])) {
        $clave = $datos["clave"];
    } else {
        $clave = password_hash($datos['clave'], PASSWORD_DEFAULT);
    }

    if (isset($datos["foto_subida"])) {
        $foto = $datos["foto_subida"];
    } else {
        $foto = "./img/perfil_normal.png";
    }
    
    $consulta = <<<EOD
        UPDATE usuario 
        SET Nombre = ?, Apellidos = ?, Email = ?, Clave = ?, Direccion = ?, Telefono = ?,
            Rol = ?, Estado = ?, Foto = ?
        WHERE ID_usuario = ?
    EOD;
    $c = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($c, "sssssissss", $datos["nombre"], $datos["apellidos"], $datos["email"],
        $clave, $datos["direccion"], $datos["telefono"], $datos["rol"], $datos["estado"],
        $datos["foto_subida"], $datos["id_usuario"]);
    mysqli_stmt_execute($c);
    mysqli_stmt_close($c);

    $evento = "INFO: Se ha modificado el usuario " . $datos["id_usuario"];
    insertar_log($conexion, $evento);
} 

// Función que borra un usuario de la BBDD mediante su ID
function borrar_usuario($conexion, $usuario) {
    $consulta = "DELETE FROM usuario WHERE ID_usuario=?";
    $c = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($c, "s", $usuario);
    mysqli_stmt_execute($c);
    mysqli_stmt_close($c);
    $evento = "INFO: Se ha eliminado el usuario " . $usuario;
    insertar_log($conexion, $evento);
}

// Función que inserta una incidencia en la BBDD
function insertar_incidencia($conexion, $datos, $usuario) {
    $id = uniqid();
    $consulta = <<<EOD
        INSERT INTO incidencia(ID_incidencia, Titulo, Descripcion, Lugar, Palabras_clave, ID_usuario)
        VALUES (?, ?, ?, ?, ?, ?);
    EOD;
    $c = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($c, "ssssss", $id, $datos["titulo"], $datos["descripcion"],
        $datos["lugar"], $datos["palabras_clave"], $usuario);
    mysqli_stmt_execute($c);
    mysqli_stmt_close($c);

    $evento = "INFO: Se ha añadido una nueva incidencia";
    insertar_log($conexion, $evento);

    return $id;
}

// Función que modifica una incidencia en la BBDD
function actualizar_incidencia($conexion, $datos) {  
    $consulta = <<<EOD
        UPDATE incidencia 
        SET Titulo = ?, Descripcion = ?, Lugar = ?, Palabras_Clave = ?, Estado = ?
        WHERE ID_incidencia = ?
    EOD;
    $c = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($c, "ssssss", $datos["titulo"], $datos["descripcion"], $datos["lugar"],
     $datos["palabras_clave"], $datos['estado_incidencia'], $datos['id_incidencia']);
    echo mysqli_stmt_execute($c);

    mysqli_stmt_close($c);

    $evento = "INFO: Se ha modificado una incidencia";
    insertar_log($conexion, $evento);
} 

function insertar_foto_incidencia($conexion,$id_incidencia , $foto) {
    $id = uniqid();
    $consulta = <<<EOD
        INSERT INTO imagen_incidencia(ID_imagen, ID_incidencia, Ruta)
        VALUES (?, ?, ?);
    EOD;
    $c = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($c, "sss", $id, $id_incidencia, $foto);
    mysqli_stmt_execute($c);
    mysqli_stmt_close($c);
    $evento = "INFO: Se ha añadido una foto";
    insertar_log($conexion, $evento); 
}

//Funcion que inserta un comentario en la BBDD
function insertar_comentario($conexion, $usuario, $id_incidencia, $comentario) {
    $id = uniqid();
    $consulta = <<<EOD
        INSERT INTO comentario(ID_comentario, ID_usuario, ID_incidencia, Descripcion)
        VALUES (?, ?, ?, ?);
    EOD;
    $c = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($c, "ssss", $id, $usuario, $id_incidencia, $comentario);
    mysqli_stmt_execute($c);
    mysqli_stmt_close($c);
    $evento = "INFO: Se ha añadido un comentario";
    insertar_log($conexion, $evento); 
}

function borrar_comentario($conexion, $id_comentario){
    $consulta = "DELETE FROM comentario WHERE ID_comentario=?";
    $c = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($c, "s", $id_comentario);
    mysqli_stmt_execute($c);
    mysqli_stmt_close($c);  
    $evento = "INFO: Se ha borrado un comentario";
    insertar_log($conexion, $evento);     
}

/* 
Esta función valida el login de un usuario.
Además, actualiza las variables de sesión para poder acceder a la distintas 
zonas según el tipo de usuario que haya iniciado la sesión.
*/
function validar_login($conexion) {
    if (isset($_SESSION['usuario'])) {
        $accion = 'bienvenida';
    } else if (isset($_POST['submit']) && isset($_POST['usuario']) && isset($_POST['clave'])) {
        $usuario = $_POST['usuario'];
        $c = mysqli_prepare($conexion, "SELECT * FROM usuario WHERE ID_usuario = ?");
        mysqli_stmt_bind_param($c, "s", $usuario);
        mysqli_stmt_execute($c);
        $datos = mysqli_stmt_get_result($c);
        mysqli_stmt_close($c);
        $resultado = mysqli_fetch_assoc($datos);
        if ($resultado == null) {
            $evento = "INFO: Intento de identificación erróneo";
            insertar_log($conexion, $evento);
            $accion = 'error';
            return $accion;
        }

        if (password_verify($_POST['clave'], $resultado['Clave'])) {
            $_SESSION['usuario'] = $_POST['usuario'];
            $_SESSION['rol'] = $resultado['Rol'];
            $_SESSION['nombre'] = $resultado['Nombre'] . " " . $resultado['Apellidos'];
            $_SESSION['foto_perfil'] = $resultado['Foto'];
            $_SESSION['estado'] = $resultado['Estado'];
            # Aquí podemos añadir más cosas que nos sean útiles
            $evento = "INFO: El usuario " . $_SESSION["usuario"] . " accede al sistema";
            insertar_log($conexion, $evento);
            $accion = 'bienvenida';
        } else {
            $evento = "INFO: Intento de identificación erróneo";
            insertar_log($conexion, $evento);
            $accion = 'error';
        }

    } else
        $accion = 'formulario';
        
    return $accion;
}

/* Funcion que se encarga de destruir la sesion, cerrandola */
function cerrar_sesion() {
    session_destroy();
}

/*Funcion para comprobar si un usuario se encuentra en la bbdd */
function comprobar_usuario($conexion,$id_usuario) {
    $usuario = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM usuario WHERE id_usuario = '$id_usuario'"));
    if ($usuario === false)
        echo "BBDD: Error al obtener usuario";

    if ($usuario === null)
        return false;
    return true;
}

/*Funcion para comprobar si un email se encuentra en la bbdd */
function comprobar_email($conexion,$email) {
    $resultado = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM usuario WHERE email = '$email'"));
    if ($resultado === false)
        echo "BBDD: Error al obtener email";

    if ($resultado === null) {
        return false;
    } 

    return true;
}

/*Funcion para obtener las rutas de las fotos asociadas a una incidencia  */
function obtener_fotos($conexion, $id_incidencia) {
    $consulta =  mysqli_query($conexion, "SELECT * FROM imagen_incidencia WHERE ID_incidencia = '{$id_incidencia}'");
    if ($consulta) {
        $datos = mysqli_fetch_all($consulta, MYSQLI_ASSOC);
        
    }

    return $datos;
}

/*Funcion para obtener una incidencia a partir de su id */
function obtener_incidencia($conexion, $id_incidencia){
    $consulta =  mysqli_query($conexion, "SELECT * FROM incidencia WHERE ID_incidencia = '{$id_incidencia}'");
    if ($consulta) {
        $datos = mysqli_fetch_all($consulta, MYSQLI_ASSOC);
    }

    return $datos[0];     
}

/*Funcion para obtener todas las incidencias*/
function obtener_incidencias($conexion){
    $consulta =  mysqli_query($conexion, "SELECT * FROM incidencia");
    if ($consulta) {
        $datos = mysqli_fetch_all($consulta, MYSQLI_ASSOC);
    }

    return $datos;     
}

/*Funcion para obtener todas las incidencias asociadas a un usuario*/
function obtener_incidencias_usuario($conexion, $id_usuario){
    $consulta =  mysqli_query($conexion, "SELECT * FROM incidencia WHERE ID_usuario='$id_usuario'");
    if ($consulta) {
        $datos = mysqli_fetch_all($consulta, MYSQLI_ASSOC);
    }

    return $datos;       
}

/*Funcion para borrar una incidencia*/
function borrar_incidencia($conexion, $id_incidencia){
    $consulta = "DELETE FROM incidencia WHERE ID_incidencia=?";
    $c = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($c, "s", $id_incidencia);
    mysqli_stmt_execute($c);
    mysqli_stmt_close($c);  
    $evento = "INFO: Se ha borrado una incidencia";
    insertar_log($conexion, $evento);   
}

/*Funcion para obtener los comentarios de una incidencia */
function obtener_comentario_incidencia($conexion, $id_incidencia){
    $consulta = mysqli_query($conexion, "SELECT * FROM comentario WHERE ID_incidencia = '$id_incidencia'");
    if($consulta) {
        $comentarios = mysqli_fetch_all($consulta, MYSQLI_ASSOC);
    }
    return $comentarios;   
}

/*Funcion para obtener el ranking de los usuarios que mas comentan */
function obtener_ranking_comentarios($conexion){
    $consulta = mysqli_query($conexion, "SELECT ID_usuario, COUNT(*) AS num_comentarios FROM comentario GROUP BY ID_usuario ORDER BY num_comentarios DESC;");
    if($consulta) {
        $rankingcomentarios = mysqli_fetch_all($consulta, MYSQLI_ASSOC);
    }
    return $rankingcomentarios; 
}

/*Funcion para obtener el ranking de los usuarios que mas incidencias han creado */
function obtener_ranking_incidencias($conexion){
    $consulta = mysqli_query($conexion, "SELECT ID_usuario, COUNT(*) AS num_incidencias FROM incidencia GROUP BY ID_usuario ORDER BY num_incidencias DESC;");
    if($consulta) {
        $rankingincidencias = mysqli_fetch_all($consulta, MYSQLI_ASSOC);
    }
    return $rankingincidencias; 
}

/*Funcion para obtener los posibles lugares de las incidencias */
function obtener_lugares($conexion){
    $consulta =  mysqli_query($conexion, "SELECT DISTINCT Lugar FROM incidencia");
    if ($consulta) {
        $datos = mysqli_fetch_all($consulta, MYSQLI_ASSOC);
    }
    return $datos; 
}

/*Funcion para insertar una valoracion en la bbdd */
function valorar($conexion, $valor, $id_usuario,$id_incidencia){
    $consulta = <<<EOD
        INSERT INTO valoracion(ID_usuario, ID_incidencia, Valoracion)
        VALUES (?, ?, ?);
    EOD;
    $c = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($c, "sss", $id_usuario, $id_incidencia, $valor);
    mysqli_stmt_execute($c);
    mysqli_stmt_close($c);   
    $evento = "INFO: Se ha añadido una valoracion";
    insertar_log($conexion, $evento); 
}

/*Funcion para obtener las valoraciones de una incidencia */
function obtener_valoraciones_incidencia($conexion, $id_incidencia){
    $num_positivos = 0;
    $num_negativos = 0;
    $consulta = mysqli_query($conexion, "SELECT Valoracion FROM valoracion WHERE ID_incidencia = '$id_incidencia'");
    if ($consulta) {
        $datos = mysqli_fetch_all($consulta, MYSQLI_ASSOC);
        foreach($datos as $valor){
            if($valor['Valoracion'] == 1){
                $num_positivos++;
            }else if($valor['Valoracion'] == 0){
                $num_negativos++;
            }
        }
    }
    return [$num_positivos, $num_negativos];
}

/*Funcion para las valoraciones que se encarga de borrar cuando el usuario clica sobre el boton de valorar 
que previamente ya habia pulsado o se encarga de modificar la valoracion si simplemente cambiamos de opinion */
function comprobar_valoracion($conexion, $id_incidencia, $usuario, $valor) {
    $consulta = "SELECT Valoracion FROM valoracion WHERE ID_incidencia = ? AND ID_usuario = ?";
    $c = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($c, "ss", $id_incidencia, $usuario);
    mysqli_stmt_execute($c);
    $resultado = mysqli_stmt_get_result($c);
    $datos = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($c);
    if (!empty($datos)) {
        if($datos['Valoracion'] == $valor){

            $consulta = "DELETE FROM valoracion WHERE ID_incidencia = ? AND ID_usuario = ?";
            $c = mysqli_prepare($conexion, $consulta);
            mysqli_stmt_bind_param($c, "ss", $id_incidencia, $usuario);
            mysqli_stmt_execute($c);
            mysqli_stmt_close($c);
            $evento = "INFO: Se ha eliminado una valoracion";
            insertar_log($conexion, $evento);  
        }
        else {
            $consulta = "UPDATE valoracion SET Valoracion = ? WHERE ID_incidencia = ? AND ID_usuario = ? ";
            $c = mysqli_prepare($conexion, $consulta);
            mysqli_stmt_bind_param($c, "iss", $valor, $id_incidencia, $usuario);
            mysqli_stmt_execute($c);
            mysqli_stmt_close($c);
            $evento = "INFO: Se ha modificado una valoracion";
            insertar_log($conexion, $evento);  
        }
    }
    else {
        valorar($conexion, $valor, $usuario, $id_incidencia);
    }
}

/* Funcion para el filtro que obtiene las incidencias en funcion de las selecciones del filtro y devuelve en orden descendente por numero de valoraciones */
function obtener_incidencias_filtro_positivo($conexion, $lugar, $palabras, $estados){
    if($lugar == "Todos") {$lugar="";}
    $estadosString = "'" . implode("', '", $estados) . "'";
    $consulta = <<<EOD
    SELECT i.ID_incidencia,i.Titulo, i.Palabras_clave, i.Fecha, i.ID_usuario, i.Descripcion, i.Estado,  i.Lugar, COUNT(v.ID_incidencia) AS valoraciones_positivas
    FROM incidencia AS i
    LEFT JOIN valoracion AS v ON i.ID_incidencia = v.ID_incidencia AND v.Valoracion = 1
    WHERE (i.Lugar = '{$lugar}' OR '{$lugar}' = '')
    AND i.Estado IN ($estadosString)
    AND (i.Descripcion LIKE '%{$palabras}%' OR i.Titulo LIKE '%{$palabras}%' OR i.Palabras_clave LIKE '%{$palabras}%' OR i.ID_usuario LIKE '%{$palabras}%')
    GROUP BY i.ID_incidencia, i.Titulo, i.Palabras_clave, i.Fecha, i.ID_usuario, i.Descripcion, i.Estado
    ORDER BY valoraciones_positivas DESC;
    EOD;
    
    $resultado = mysqli_query($conexion, $consulta);
    $incidencias = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

    return $incidencias;
}

/* Funcion para el filtro que obtiene las incidencias en funcion de las selecciones del filtro y devuelve en orden descendente por numero de valoraciones netas*/
function obtener_incidencias_filtro_positivo_neto($conexion, $lugar, $palabras, $estados){
    if($lugar == "Todos") {$lugar="";}
    $estadosString = "'" . implode("', '", $estados) . "'";
    $consulta = <<<EOD
    SELECT i.ID_incidencia, i.Titulo, i.Palabras_clave, i.Fecha, i.ID_usuario, i.Descripcion, i.Estado,  i.Lugar, (SELECT COUNT(v.ID_incidencia) FROM valoracion v WHERE v.ID_incidencia = i.ID_incidencia AND v.Valoracion = 1) AS valoraciones_positivas,
    (SELECT COUNT(v.ID_incidencia) FROM valoracion v WHERE v.ID_incidencia = i.ID_incidencia AND v.Valoracion = 0) AS valoraciones_negativas,
    (SELECT COUNT(v.ID_incidencia) FROM valoracion v WHERE v.ID_incidencia = i.ID_incidencia AND v.Valoracion = 1) - (SELECT COUNT(v.ID_incidencia) FROM valoracion v WHERE v.ID_incidencia = i.ID_incidencia AND v.Valoracion = 0) AS valoraciones_netas
    FROM incidencia AS i
    LEFT JOIN valoracion AS v ON i.ID_incidencia = v.ID_incidencia AND v.Valoracion = 1
    WHERE (i.Lugar = '{$lugar}' OR '{$lugar}' = '')
    AND i.Estado IN ($estadosString)
    AND (i.Descripcion LIKE '%{$palabras}%' OR i.Titulo LIKE '%{$palabras}%' OR i.Palabras_clave LIKE '%{$palabras}%' OR i.ID_usuario LIKE '%{$palabras}%')
    GROUP BY i.ID_incidencia, i.Titulo, i.Palabras_clave, i.Fecha, i.ID_usuario, i.Descripcion, i.Estado
    ORDER BY valoraciones_netas DESC;
    EOD;

    $resultado = mysqli_query($conexion, $consulta);
    $incidencias = mysqli_fetch_all($resultado, MYSQLI_ASSOC);  

    return $incidencias;
}

/* Funcion para el filtro que obtiene las incidencias en funcion de las selecciones del filtro y devuelve en orden descendente por fecha */
function obtener_incidencias_filtro_fecha($conexion, $lugar, $palabras, $estados){
    if($lugar == "Todos") {$lugar="";}
    $estadosString = "'" . implode("', '", $estados) . "'";
    $consulta = <<<EOD
    SELECT i.ID_incidencia,i.Titulo, i.Palabras_clave, i.Fecha, i.ID_usuario, i.Descripcion, i.Estado,  i.Lugar
    FROM incidencia AS i
    WHERE (i.Lugar = '{$lugar}' OR '{$lugar}' = '')
    AND i.Estado IN ($estadosString)
    AND (i.Descripcion LIKE '%{$palabras}%' OR i.Titulo LIKE '%{$palabras}%' OR i.Palabras_clave LIKE '%{$palabras}%' OR i.ID_usuario LIKE '%{$palabras}%')
    GROUP BY i.ID_incidencia, i.Titulo, i.Palabras_clave, i.Fecha, i.ID_usuario, i.Descripcion, i.Estado
    ORDER BY i.Fecha DESC;
    EOD;

    $resultado = mysqli_query($conexion, $consulta);
    $incidencias = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

    return $incidencias;
}

?>
