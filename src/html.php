<?php
// Función para listar los eventos del sistema
function HTML_log($datos) {
?>
  <div class="contenedor-log">
    <h1>Eventos del sistema</h1>
    <table>
  <?php 
  foreach ($datos as $fila) {
  ?>
      <tr><td><?php echo $fila["Fecha"]?></td><td><?php echo $fila["Descripcion"]?></td></tr>
<?php
  }

  echo "</table></div>";
}

// Función con las opciones básicas de gestión de usuarios
function menu_gestion_usuarios() {
  echo <<<HTML
  <div class="menu-gestion-usuarios">
    <ul>
      <li><a href="./gestion_usuarios.php">Listar usuarios</a></li>
      <li><a href="./registro.php?gestion_usuarios=true&add=true">Añadir nuevo usuario</a></li>
    </ul>
  </div>
  HTML;
}

// Función para listar los usuarios de la BBDD y el menú de gestión de usuarios
function listado_usuarios($datos, $borrar) {
  echo "<div id='contenedor-usuarios'>";
  if ($borrar) {
    echo <<<HTML
      <div class="contenedor-borrado-usuario-2">
        <p>El usuario ha sido eliminado</p>
        <a href="./gestion_usuarios.php"><img src="./img/iconos/denegar.png"/></a>
      </div>
    HTML;
  }

  menu_gestion_usuarios();
  foreach ($datos as $fila) {
?>
  <div class="tarjeta-usuario"> 
    <img class="miniatura-usuario" src="<?php echo $fila["Foto"] ?>">
    <div class="contenedor-datos-usuario-1">
        <p>
          Usuario: <span class="datos-usuario"><?php echo $fila["Nombre"] . " " . $fila["Apellidos"]?></span>
          Email: <span class="datos-usuario"><?php echo $fila["Email"] ?></span>
        </p>
        <p>
          Dirección: <span class="datos-usuario"><?php echo $fila["Direccion"] ?></span class="datos-usuario">
          Teléfono: <span class="datos-usuario"><?php echo $fila["Telefono"] ?></span>
        </p>
        <p>
          Rol: <span class="datos-usuario"><?php echo $fila["Rol"] ?></span> 
          Estado: <span class="datos-usuario"><?php echo $fila["Estado"] ?></span>
        </p>
    </div>
      <div class ="contenedor-botones-usuario">
      <a href="./registro.php?usuario=<?php echo $fila['ID_usuario']?>&gestion_usuarios=true&editar=true">
        <img src="./img/iconos/editar.png" alt="Editar usuario"/>
      </a>
      <a href="./gestion_usuarios.php?usuario=<?php echo $fila['ID_usuario']?>&borrar=true">
        <img src="./img/iconos/borrar.png" alt="Borrar usuario"/>
      </a>
    </div>
  </div>
<?php
  }
  echo "</div>";
}


// Función que muestra una página secundaria en gestion_usuarios.php para borrar un usuario
function confirmar_borrado_usuario($usuario) {
?>
  <div class="contenedor-borrado-usuario-1">
    <p>¿Está seguro de que desea borrar a <?php echo $usuario ?>?</p>
    <div class="contenedor-borrado-usuario-botones">
      <a href="./gestion_usuarios.php?usuario=<?php echo $usuario?>&confirmar-borrar=true"><img src="./img/iconos/aceptar.png" alt="Aceptar"/></a>
      <a href="./gestion_usuarios.php"><img src="./img/iconos/denegar.png" alt="Denegar"/></a>
    </div>
  </div>
<?php
}

// **************************************************
// FORMULARIOS 

function FORM_login() {
  echo <<< HTML
  <div class='frm_login'>
    <form action='' method='POST' >
      <div class='frm_login_input'>
        <input type='text' name='usuario' placeholder="usuario"/>
      </div>
      <div class='frm_login_input'>
        <input type='password' name='clave' placeholder="Contraseña"/>
      </div>
      <div class='frm_login_submit'>
        <input type='submit' name='submit' value='Acceder'/>
      </div>
      <a href="./registro.php">Regístrate</a>
    </form>
  </div>
  HTML;
}

function HTML_mensaje_incidencia(){
  echo <<<HTML
  <div class="contenedor-borrado-usuario-2">
      <p>La incidencia ha sido añadida</p>
    <a href="./gestion_usuarios.php"><img src="./img/iconos/denegar.png"/></a>
  </div>
HTML;
}

function  FORM_incidencia($accion) {
  isset($_SESSION["datos_incidencia"]["confirmar"]) ? $completado = "disabled" : $completado = "";?>
  
  <div class="contenedor-formulario-incidencia">
    <form id="formulario-incidencia" action="nueva_incidencia.php" method="POST" enctype="multipart/form-data" novalidate>     
      <h1 id="titulo-formulario-incidencia"><?php echo ($accion == "editar") ? "Editar Incidencia" : "Nueva Incidencia"?></h1>
    <?php
    if($accion == "editar") {?>
      <fieldset class="estado-incidencia">
        <legend>Estado:</legend>
        <label><input type="radio" name="estado-incidencia" value="Pendiente" <?php if( isset($_SESSION['datos_incidencia']['estado_incidencia']) && $_SESSION['datos_incidencia']['estado_incidencia'] == "Pendiente") echo "checked" ?> <?php if($_SESSION['rol'] != 'Administrador') {echo "disabled"; } ?>> Pendiente</label>
        <label><input type="radio" name="estado-incidencia" value="Comprobada" <?php if(isset($_SESSION['datos_incidencia']['estado_incidencia']) && $_SESSION['datos_incidencia']['estado_incidencia'] == "Comprobada") echo "checked" ?> <?php if($_SESSION['rol'] != "Administrador") {echo "disabled";} ?>> Comprobada</label>
        <label><input type="radio" name="estado-incidencia" value="Tramitada" <?php if(isset($_SESSION['datos_incidencia']['estado_incidencia']) && $_SESSION['datos_incidencia']['estado_incidencia'] == "Tramitada") echo "checked" ?> <?php if($_SESSION['rol'] != "Administrador") {echo "disabled"; } ?>> Tramitada</label>
        <label><input type="radio" name="estado-incidencia" value="Irresoluble" <?php if(isset($_SESSION['datos_incidencia']['estado_incidencia']) && $_SESSION['datos_incidencia']['estado_incidencia'] == "Irresoluble") echo "checked" ?> <?php if($_SESSION['rol'] != "Administrador") {echo "disabled"; } ?>> Irresoluble</label>
        <label><input type="radio" name="estado-incidencia" value="Resuelta" <?php if(isset($_SESSION['datos_incidencia']['estado_incidencia']) && $_SESSION['datos_incidencia']['estado_incidencia'] == "Resuelta") echo "checked" ?> <?php if($_SESSION['rol'] != "Administrador") {echo "disabled"; } ?>> Resuelta</label>    
      </fieldset>
      <?php
    }
      ?>
      <div class="contenedor-inputs-incidencia">
        <div class="entrada">
          <input type="text" class="TextoPequeño" placeholder="Título" name="titulo-incidencia" value="<?php if (isset($_SESSION["datos_incidencia"]["titulo"])) { echo $_SESSION["datos_incidencia"]["titulo"]; }?>" <?php echo $completado ?>/>
          <?php if (isset($_SESSION["errores_incidencia"]["titulo"])) { echo "<span class=estilo-error>" . $_SESSION["errores_incidencia"]["titulo"] . "</span>"; }?>
        </div>
        <div class="entrada">
          <input type="text" class="TextoGrande" placeholder="Descripción" name="descripcion-incidencia" value="<?php if (isset($_SESSION["datos_incidencia"]["descripcion"])) { echo $_SESSION["datos_incidencia"]["descripcion"]; }?>" <?php echo $completado ?>/>
          <?php if (isset($_SESSION["errores_incidencia"]["descripcion"])) { echo "<span class=estilo-error>" . $_SESSION["errores_incidencia"]["descripcion"] . "</span>"; }?>
        </div>
        <div class="entrada">
          <input type="text" class="TextoPequeño" placeholder="Lugar" name="lugar-incidencia" value="<?php if (isset($_SESSION["datos_incidencia"]["lugar"])) { echo $_SESSION["datos_incidencia"]["lugar"]; }?>" <?php echo $completado ?>/>
          <?php if (isset($_SESSION["errores_incidencia"]["lugar"])) { echo "<span class=estilo-error>" . $_SESSION["errores_incidencia"]["lugar"] . "</span>"; }?>
        </div>
        <div class="entrada">
          <input type="text" class="TextoPequeño" placeholder="Palabras clave" name="palabras-incidencia" value="<?php if (isset($_SESSION["datos_incidencia"]["palabras_clave"])) { echo $_SESSION["datos_incidencia"]["palabras_clave"]; }?>" <?php  echo $completado ?>/>
          <?php if (isset($_SESSION["errores_incidencia"]["palabras_clave"])) { echo "<span class=estilo-error>" . $_SESSION["errores_incidencia"]["palabras_clave"] . "</span>"; }?>
        </div>
        <?php
        
        if($accion == "editar"){?>
        <div class="entrada">
          <form id="image-form" enctype="multipart/form-data">
            <input type="file" id="image-input" name="fotos-incidencia[]" onchange=previewImages() multiple>
          </form>
    
          <div id="image-preview-container" class="image-preview"></div>
            <?php
            
            if (isset($_SESSION["datos_incidencia"]["fotos_incidencia"])) {
              foreach($_SESSION["datos_incidencia"]["fotos_incidencia"] as $foto){
                if(isset($foto['Ruta'])){
                  $foto = $foto['Ruta'];
                }
                echo "<img id='foto-incidencia-visualizacion' src='" . $foto . "'/>";
              }
            }
            echo "</div>";
        }?> 
        <div class='entrada'>
          <?php
          if (isset($_SESSION["datos_incidencia"]["confirmar"]) && $accion != "editar") {
            echo "<input type='submit' name='confirmar-incidencia' value='Confirmar datos'/>";
          } else if($accion != "editar"){
            echo "<input type='submit' name='enviar-incidencia' value='Enviar datos'/>";
          } else if( isset($_SESSION["datos_incidencia"]["confirmar_2"])){
            echo "<input type='submit' name='confirmar-incidencia-2' value='Confirmar datos'/>";
          } else {
            echo "<input type='submit' name='enviar-incidencia-2' value='Enviar datos'/>";
          }

          
          ?>
        </div>
      </div>
    </form>   
  </div>
<?php
}

function FORM_registro($errores, $registrar) {
?>
<?php isset($_SESSION['datos_usuario']['mensaje']) ? $completado = "disabled" : $completado = "";?>
<div class='contenedor-formulario-registro'>
  <?php
  if (!empty($registrar)) {
    echo <<<HTML
      <div class="contenedor-registro-usuario">
        <p>$registrar</p>
        <a href="./registro.php"><img src="./img/iconos/denegar.png"/></a>
      </div>
    HTML;
  }

  if (isset($_SESSION["administrador_gestiona_usuario"]) && !isset($_GET["aside"])) {
    menu_gestion_usuarios();
  }
  ?>
  <form id="formulario-registro" action='registro.php' method='POST' enctype='multipart/form-data'>
    <h1 id="titulo-registro"><?php echo isset($_SESSION["datos_usuario"]["editar"]) ? "Editar usuario" : "Registrar usuario";?></h1>
    <div class="contenedor-inputs-registro">
      <div class='frm_registro_input'>
        <?php
        if (isset($_SESSION['datos_usuario']['foto_subida'])) {
          echo "<img id='foto-perfil-visualizacion-2' src='" . $_SESSION['datos_usuario']['foto_subida'] . "'/>";
        } else {
          echo "<img id='foto-perfil-visualizacion-2' src='./img/perfil_normal.png'/>";
        }
        ?>
        <input type='file' id='foto-perfil-input' name='foto-perfil' accept="image/*" onchange="previsualizarFoto()" <?php echo $completado?>/>
      </div>
      <div class='frm_registro_input' <?php if(isset($_SESSION["datos_usuario"]["editar"])) { echo "style='display: none'"; } ?>>
        <input type='text' name='id-usuario' placeholder="Usuario" value="<?php if (isset($_SESSION['datos_usuario']['id_usuario'])) { echo $_SESSION['datos_usuario']['id_usuario']; }?>" <?php echo $completado?>/>
        <?php if (isset($errores["id_usuario"])) { echo "<span class=estilo-error>" . $errores["id_usuario"] . "</span>"; }?>
      </div>
      <div class='frm_registro_input'>
        <input type='text' name='nombre-registro'  placeholder="Nombre" value="<?php if (isset($_SESSION['datos_usuario']['nombre'])) { echo $_SESSION['datos_usuario']['nombre']; }?>" <?php echo $completado?>/>
        <?php if (isset($errores["nombre"])) { echo "<span class=estilo-error>" . $errores["nombre"] . "</span>";}?>
      </div>
      <div class='frm_registro_input'>
        <input type='text' name='apellidos' placeholder="Apellidos" value="<?php if (isset($_SESSION['datos_usuario']['apellidos'])) { echo $_SESSION['datos_usuario']['apellidos']; }?>" <?php echo $completado?>/>
        <?php if (isset($errores["apellidos"])) { echo "<span class=estilo-error>" . $errores["apellidos"] . "</span>";}?>
      </div>

      <div class='frm_registro_input'>
        <input type='password' name='password' placeholder="Contraseña" <?php echo $completado?>/>
        <input type='password' name='password-2' placeholder="Confirmar contraseña" <?php echo $completado?>/>
        <?php if (isset($errores["clave"])) { echo "<span class=estilo-error>" . $errores["clave"] . "</span>"; }?>
      </div>
      <div class='frm_registro_input'>
        <input type='email' name='email' placeholder="Email" value="<?php if (isset($_SESSION['datos_usuario']['email']) && !isset($errores["email"])) { echo $_SESSION['datos_usuario']['email'];}?>" <?php echo $completado?>/>
        <?php if (isset($errores["email"])) { echo "<span class=estilo-error>" . $errores["email"] . "</span>"; }?>
      </div>
      <div class='frm_registro_input'>
        <input type='text' name='direccion' placeholder="Dirección" value="<?php if (isset($_SESSION['datos_usuario']['direccion']) && !isset($errores["direccion"])) { echo $_SESSION['datos_usuario']['direccion']; }?>" <?php echo $completado?>/>
        <?php if (isset($errores["direccion"])) { echo "<span class=estilo-error>" . $errores["direccion"] . "</span>"; }?>
      </div>
      <div class='frm_registro_input'>
        <input type='tel' name='telefono' placeholder="Teléfono" value="<?php if(isset($_SESSION['datos_usuario']['telefono']) && !isset($errores["telefono"])){echo $_SESSION['datos_usuario']['telefono'];}?>" <?php echo $completado?>/>
        <?php if (isset($errores["telefono"])) { echo "<span class=estilo-error>" . $errores["telefono"] . "</span>"; }?>
      </div>
      <?php if ((isset($_SESSION["datos_usuario"]["editar"]) || isset($_SESSION["administrador_gestiona_usuario"])) && isset($_SESSION["rol"]) && $_SESSION["rol"] == "Administrador") {?>
      <div class='frm_registro_input'> 
        <select name="rol" <?php echo $completado?>>
          <option <?php if (isset($_SESSION["datos_usuario"]["rol"]) && $_SESSION["datos_usuario"]["rol"] == "Colaborador") { echo "selected"; } ?>>Colaborador</option>
          <option <?php if (isset($_SESSION["datos_usuario"]["rol"]) && $_SESSION["datos_usuario"]["rol"] == "Administrador") { echo "selected"; } ?>>Administrador</option>
        </select>
      </div>
      <div class='frm_registro_input'> 
        <select name="estado" <?php echo $completado?>>
          <option <?php if (isset($_SESSION["datos_usuario"]["rol"]) && $_SESSION["datos_usuario"]["estado"] == "Activo") { echo "selected"; } ?>>Activo</option>
          <option <?php if (isset($_SESSION["datos_usuario"]["rol"]) && $_SESSION["datos_usuario"]["estado"] == "Inactivo") { echo "selected"; } ?>>Inactivo</option>
        </select>
      </div>
      <?php }?>
      <div class='frm_registro_submit'>
        <?php
        if (isset($_SESSION['datos_usuario']['mensaje'])) {
          echo "<input type='submit' name='confirmar-registro' value='Confirmar registro'/>";
        } else {
          echo "<input type='submit' name='registrar' value='Registrar'/>";
        }
        ?>
      </div>
    </div>
  </form>
</div>
<?php
}

function HTML_basededatos($ruta){
  ?>
  <main>
  <div class="contenedor-restaurar-bbdd-completo">
    <h1>Restaurar la BBDD</h1>
    <div class='contenedor-restaurar-bbdd'>
      <p>Al hacer clic en este botón, se crea una réplica de seguridad de los datos almacenados en la base de datos en ese momento. Esta copia de seguridad se guarda en un archivo separado 
      y se puede utilizar para restaurar la base de datos en caso de pérdida de datos o errores inesperados.</p>
      <a href="<?php echo $ruta ?>" download><img src="./img/iconos/descargar.png"/></a>
    </div>
  </div>
  </main>
  <?php
}

// **************************************************
// Titulo
function HTML_inicio() {
  echo <<< HTML
    <!DOCTYPE html>
    <html lang="es">
    <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="./vista/index.css" />
    <title>Complainopolis</title>
    </head>
    <body>  
    HTML;
}
// Cierre de página web
function HTML_fin() {
  echo '<script src="./previsualizar_foto.js"></script>';
  echo '</body></html>';
}

// Encabezado 
function HTML_encabezado() {
  echo <<< HTML
    <header>
      <img src="./img/logo.png" alt="Logo" class="logo">
      <h1>Complainopolis<h1>
    </header>
  HTML;
}

// Barra de navegación
function HTML_nav($tipo_usuario) {
  echo <<< HTML
    <nav class='barranavegacion'>
      <a href='index.php'>Ver Incidencias</a> 
  HTML;

  if ($tipo_usuario != "Visitante") {
    echo <<< HTML
        <a href='nueva_incidencia.php'>Nueva Incidencia</a>
        <a href='mis_incidencias.php'>Mis Incidencias</a>
        HTML;

    if ($tipo_usuario != "Colaborador") {
      echo <<< HTML
          <a href='gestion_usuarios.php'>Gestión usuarios</a>
          <a href='log.php'>Ver Log</a>
          <a href='gestion_bbdd.php'>Gestión BBDD</a>
      HTML;
    }
  }

  echo <<< HTML
    </nav>
  HTML;
}

// Barra Lateral
function HTML_aside($accion, $rankingincidencias, $rankingcomentarios) {
  echo <<<HTML
    <aside>
  HTML;

  if($accion == "formulario") {
    FORM_login();
  }
  else if ($accion == "identificado") {
    echo "identificado";
  }
  else if ($accion == "error") {
    echo <<<HTML
      <p>Las credenciales ingresadas no son válidas</p>
    HTML;
    FORM_login();
  }
  else if($accion == "bienvenida") {
    ?>
    <div class="contenedor-perfil">
        <?php
          $ruta = './img/perfil_normal.png';
          if (isset($_SESSION['foto_perfil'])) {
            $ruta = $_SESSION['foto_perfil'];
          }
        ?>
        <img src="<?php echo $ruta ?>" alt="Foto de perfil" id="foto-perfil-aside"/>
      <div class="info-perfil">
        <p><?php echo "@" . $_SESSION['usuario'] ?></p>
        <p><?php echo $_SESSION['rol'] ?></p>
      </div>
      <form action="" method="POST">
        <a id="editar-aside" href="./registro.php?usuario=<?php echo $_SESSION['usuario']?>&editar=true&aside=true"><img src="./img/iconos/editar.png" alt="Editar"/></a>
        <input type="submit" id="cerrar-sesion-aside" name="cerrar-sesion" value=""/>
      </form>
    </div>
    <?php
  }

  echo "<div class='ranking'><h4>Los que mas añaden</h4><ul>";
  for($i = 0 ; $i < 3 && $i < count($rankingincidencias) ; $i++){
    echo "<li>";
    echo $rankingincidencias[$i]['ID_usuario'];
    echo "</li>";
  }
  echo "</ul></div>";

  echo "<div class='ranking'><h4>Los que mas opinan</h4><ul>";
  for($i = 0 ; $i < 3 && $i < count($rankingcomentarios) ; $i++){
    if(!empty($rankingcomentarios[$i]['ID_usuario']) && $rankingcomentarios[$i]['ID_usuario'] != "Anónimo"){
      echo "<li>";
      echo $rankingcomentarios[$i]['ID_usuario'];
      echo "</li>";
    }
  }
  echo "</ul></div>";

  echo "<p><h4>Numero total de incidencias</h4>";
  if(isset($_SESSION['total_incidencias'])){
    echo $_SESSION['total_incidencias'];
  }
  echo "</p>";

  echo <<<HTML
    </aside>
  HTML;
}

function HTML_contenedor_inicio() {
  echo <<< HTML
    <div class="container">
  HTML;
}

function HTML_contenedor_fin() {
  echo <<< HTML
    </div>
  HTML;
} 

// Pie de página
function HTML_pie_pagina() {
  echo <<< HTML
    <footer class='pie-pagina'>
      <p>&copy; Mario Rodríguez López & Santiago García Santamaría</p>
      <a href="Documentacion_tw.pdf">Documentación</a>
    </footer>
  HTML;
}

function HTML_main2(){
  echo <<< HTML
    <main class="main">
      <div class="incidencias">
        <h4>Este es el main</h4>
      </div>
    </main>
  HTML;
}

function HTML_borrar_incidencia($datos) {
  ?>
    <div class="contenedor-borrado-incidencia-1">
    <p>¿Está seguro de que desea borrar la incidencia?</p>
    <div class="contenedor-borrado-incidencia-botones">
      <a href="./mis_incidencias.php?incidencia=<?php echo $datos?>&confirmar-borrar=true"><img src="./img/iconos/aceptar.png" alt="Aceptar"/></a>
      <a href="./mis_incidencias.php"><img src="./img/iconos/denegar.png" alt="Denegar"/></a>
    </div>
  </div>

  <?php
}

function HTML_filtro($lugares){
  $estados = array("Pendiente", "Tramitada", "Resuelta", "Irresoluble", "Comprobada");
  ?>
  <div class="primer-filtro">
    <button id="boton-filtro" name="boton-filtro" value="Opciones filtro">Opciones filtro</button>
    <div id="filtro" class="contenedor-filtro" style="display: none;">
      <form class="formulario-filtro" method="POST" action="">
        <fieldset class="ordenacion-filtro">
          <legend>Ordenar por:</legend>
          <label><input type="radio" name="orden" value="fecha" <?php if (isset($_SESSION['datos_filtro']['orden_filtro']) && $_SESSION['datos_filtro']['orden_filtro'] == 'fecha') echo 'checked'; ?>>
          Ordenar por fecha </label>
          <label><input type="radio" name="orden" value="positivos" <?php if (isset($_SESSION['datos_filtro']['orden_filtro']) && $_SESSION['datos_filtro']['orden_filtro'] == 'positivos') echo 'checked'; ?>>
          Ordenar por positivos </label>
          <label><input type="radio" name="orden" value="positivos_neto" <?php if (isset($_SESSION['datos_filtro']['orden_filtro']) && $_SESSION['datos_filtro']['orden_filtro'] == 'positivos_neto') echo 'checked'; ?>>
          Ordenar por positivos netos </label>
          <?php  if (isset($_POST['boton-filtro']) && !isset($_SESSION['datos_filtro']['orden_filtro'])){echo "<span class=estilo-error>" . $_SESSION["errores_filtro"]['orden'] . "</span>"; }?>
        </fieldset>
        <fieldset class="busqueda-filtro">
          <legend>Incidencias que contengas: </legend>
          Texto de busqueda:
          <input class=""type="TextoGrande" name="busqueda-filtro" value="<?php if (isset($_SESSION['datos_filtro']['busqueda_filtro'])){echo $_SESSION['datos_filtro']['busqueda_filtro'];} ?>">
          Lugar:
          <select class="select-filtro" name="lugar-filtro">
            <option value="">Todos</option>
            <?php 
            foreach($lugares as $lugar){ ?>
            
              <option <?php if (isset($_SESSION['datos_filtro']['lugar_filtro']) && $_SESSION['datos_filtro']['lugar_filtro'] == $lugar['Lugar']) echo 'selected'; ?> value = "<?php echo $lugar['Lugar'] ?>" > <?php echo $lugar['Lugar'] ?> </option>
            <?php } ?>
        </select>
        </fieldset>
        <fieldset class="estado-incidencia-filtro">
          <legend>Estado:</legend>
          <label><input type="checkbox" name="estado-filtro[]" value="Pendiente" <?php if( isset($_SESSION['datos_filtro']['estado_filtro']) && in_array("Pendiente", $_SESSION['datos_filtro']['estado_filtro'])) echo "checked" ?>> Pendiente</label>
          <label><input type="checkbox" name="estado-filtro[]" value="Comprobada" <?php if(isset($_SESSION['datos_filtro']['estado_filtro']) && in_array("Comprobada", $_SESSION['datos_filtro']['estado_filtro'])) echo "checked" ?>> Comprobada</label>
          <label><input type="checkbox" name="estado-filtro[]" value="Tramitada" <?php if(isset($_SESSION['datos_filtro']['estado_filtro']) && in_array("Tramitada", $_SESSION['datos_filtro']['estado_filtro'])) echo "checked" ?>> Tramitada</label>
          <label><input type="checkbox" name="estado-filtro[]" value="Irresoluble" <?php if(isset($_SESSION['datos_filtro']['estado_filtro']) && in_array("Irresoluble", $_SESSION['datos_filtro']['estado_filtro'])) echo "checked" ?> > Irresoluble</label>
          <label><input type="checkbox" name="estado-filtro[]" value="Resuelta" <?php if(isset($_SESSION['datos_filtro']['estado_filtro']) && in_array("Resuelta", $_SESSION['datos_filtro']['estado_filtro'])) echo "checked" ?>> Resuelta</label>
          <?php if (isset($_POST['boton-filtro']) && !isset($_SESSION['datos_filtro']['estado_filtro'])){echo "<span class=estilo-error>" . $_SESSION["errores_filtro"]['estado'] . "</span>"; }?>   
        </fieldset>   
        <input type="submit" name="boton-filtro" value="Filtrar">
      </form>
    </div>
  </div>
  <?php
}

function  HTML_mostrar_incidencia($incidencias, $conexion, $lugares) {
  ?>
  <main class="main">
  <?php

  HTML_filtro($lugares);

  foreach($incidencias as $incidencia){
    ?>
    <div class="contenedor-incidencia">
    <h3 class="titulo-incidencia"><?php echo $incidencia['Titulo'] ?></h3>
      <div class="contenedor-datos-incidencia">
        <ul>
          <li><img src="./img/iconos/lugar.png"/> Lugar: <span><?php echo $incidencia['Lugar'] ?></span></li>
          <li><img src="./img/iconos/calendario.png"/> Fecha: <span><?php echo $incidencia['Fecha'] ?></span></li>
          <li><img src="./img/iconos/creador.png"/> Creado por: <span><?php echo $incidencia['ID_usuario'] ?></span></li>
          <li><img src="./img/iconos/clave.png"/> Palabras clave: <span><?php echo $incidencia['Palabras_clave'] ?></span></li>
          <li><img src="./img/iconos/estado.png"/> Estado: <span><?php echo $incidencia['Estado'] ?></span></li>
          <li><img src="./img/iconos/me_gusta.png"/> Valoraciones positivas: <span><?php if(isset($incidencia['Valoraciones_positivas'])){echo $incidencia['Valoraciones_positivas'];}else {echo 0;} ?></span></li>      
          <li><img src="./img/iconos/no_me_gusta.png"/> Valoraciones negativas: <span><?php if(isset($incidencia['Valoraciones_negativas'])){echo $incidencia['Valoraciones_negativas'];}else {echo 0;} ?></span></li> 
        </ul>
        <p><?php echo $incidencia['Descripcion'] ?></p>
      </div>
    <?php

    $fotos = obtener_fotos($conexion,$incidencia['ID_incidencia']);
    foreach ($fotos as $foto) {
      ?>
      <div class="contenedor-foto-incidencia">
        <img src="<?php echo $foto['Ruta'] ?>">
      </div>
      <?php
    }

    $comentarios = obtener_comentario_incidencia($conexion,$incidencia['ID_incidencia']);
    foreach ($comentarios as $comentario) {
      ?>
      <div class="comentario-incidencia">
        <p class="nombre"><?php echo $comentario['ID_usuario'] ?></p>
        <p class="fecha"><?php echo $comentario['Fecha'] ?></p>
        <?php if(isset($_SESSION['rol']) && ($_SESSION['rol'] == "Administrador" ) || (isset($_SESSION['usuario']) && ($_SESSION['usuario'] ==  $incidencia['ID_usuario']))){?>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?id_comentario=<?php echo $comentario['ID_comentario']?>&confirmar-borrar=true" class="boton-borrar-comentario" name="boton-borrar-comentario" value="borrar"><img class="icono-borrar-comentario" src="./img/iconos/borrar.png"/></a>
        <?php } ?>
        <p class="comentario"><?php echo $comentario['Descripcion'] ?></p>
      </div>
      <?php
    }
    
    ?>
      <div class="comentario-usuario" style="display: none;">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id_incidencia=<?php echo $incidencia['ID_incidencia']?>" method="POST">
          <textarea type="text" class="texto-comentario" name="comentario-incidencia" placeholder="Ingrese su texto"></textarea>
          <button type="submit" name="enviar-comentario">Enviar comentario</button>
        </form>
      </div>
      <div class="contenedor-botones-incidencia">
        <div class="botones-incidencia-izquierda">
          <a id="me-gusta-incidencia" href="<?php echo $_SERVER['PHP_SELF']; ?>?valoracion=like&id_incidencia=<?php echo $incidencia['ID_incidencia']?>" name="boton-megusta" value="like"><img class="icono-incidencia" src="./img/iconos/me_gusta.png"/></a>
          <a id="no-me-gusta-incidencia" href="<?php echo $_SERVER['PHP_SELF']; ?>?valoracion=dislike&id_incidencia=<?php echo $incidencia['ID_incidencia']?>" name="boton-nomegusta" value="dislike"><img class="icono-incidencia" src="./img/iconos/no_me_gusta.png"/></a>
        </div>
        <div class="botones-incidencia-derecha">
          <a class="boton-comentario" name="boton-incidencia" value="comentar"><img class="icono-incidencia" src="./img/iconos/comentario.png"/></a>
          <?php if(isset($_SESSION['rol']) && ($_SESSION['rol'] == "Administrador" ) || (isset($_SESSION['usuario']) && ($_SESSION['usuario'] ==  $incidencia['ID_usuario']))){?>
          <a href="nueva_incidencia.php?editar-fuera=true&id_incidencia=<?php echo $incidencia['ID_incidencia']?>" name="boton-incidencia" value="editar"><img class="icono-incidencia" src="./img/iconos/editar.png"/></a>
          <a class="boton-borrar-incidencia" name="boton-incidencia" value="borrar"><img class="icono-incidencia" src="./img/iconos/borrar.png"/></a>
        </div>
        <?php } ?>
      </div>
      <div class="contenedor-borrado-incidencia" style="display: none;">
        <p>¿Está seguro de que desea borrar la incidencia?</p>
        <div class="contenedor-borrado-incidencia-botones">
          <a href="<?php echo $_SERVER['PHP_SELF']; ?>?id_incidencia=<?php echo $incidencia['ID_incidencia']?>&confirmar-borrar=true"><img class="icono-incidencia" src="./img/iconos/aceptar.png" alt="Aceptar"/></a>
          <a href="<?php echo $_SERVER['PHP_SELF']; ?>"><img class="icono-incidencia" src="./img/iconos/denegar.png" alt="Denegar"/></a>
        </div>
      </div>
    </div>
    <?php
  }
  ?>
  </main>
  <?php
}

function HTML_inactivo(){
  ?>
  <div class="contenedor-inactivo">
    <div class="contenedor-restaurar-bbdd-completo">
      <h1>Estado Inactivo</h1>
      <div class='contenedor-restaurar-bbdd'>
      <form action="" method="POST">
        <p>Debes esperar a que un administrador te active la cuenta. Por favor cierre sesion. Gracias por tu espera</p>
        <input type="submit" name="cerrar-sesion" value="Cerrar sesion"/>
      </form>
      </div>
    </div>
  </div>
  <?php  
}
?>
