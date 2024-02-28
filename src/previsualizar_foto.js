// Función para previsualizar la foto de perfil de un usuario
function previsualizarFoto() {
    var archivo = document.getElementById("foto-perfil-input").files[0];
    var tipoArchivo = archivo.type;
    var tamañoArchivo = archivo.size;
    // Validar si el archivo es una foto
    if (!tipoArchivo.startsWith('image/')) {
        document.getElementById("foto-perfil-input").value = "";
        return;
    }

    // Validar si el tamaño es adecuado
    var tamañoMaximo = 5 * 1024 * 1024; // 5MB
    if (tamañoArchivo > tamañoMaximo) {
        document.getElementById("foto-perfil-input").value = "";
        return;
    }

    // Previsualizar la foto antes de enviarla
    var lector = new FileReader();
    lector.onload = function (e) {
        var fotoPreview = document.getElementById("foto-perfil-visualizacion-2");
        fotoPreview.src = e.target.result;
        fotoPreview.style.display = "block";
    };

    lector.readAsDataURL(archivo);
}

function guardarImagen() {
    var archivo = document.getElementById("foto-perfil-input").files[0];
    if (!archivo) {
        return;
    }
    var formData = new FormData();
    formData.append("foto", archivo);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "registro.php", true);
    xhr.send(formData);
}


//Funciones y elementos para mostrar los comentarios al pulsar sobre el icono de comentar

let comentario = document.querySelectorAll('.comentario-usuario');

function abrirCerrar(item) {
    if(item.style.display=="none") {
        item.style.display="block";
    } else {
        item.style.display="none"
    }
}

boton = document.querySelectorAll('.boton-comentario');

for( let i = 0; i < comentario.length ; i++ ){
    boton[i].addEventListener('click', () => abrirCerrar(comentario[i]));
}

// Función para previsualizar imágenes seleccionadas
function previewImages() {
    var previewContainer = document.getElementById('image-preview-container');
    previewContainer.innerHTML = ''; // Limpiar el contenedor de previsualización
    
    var files = document.getElementById('image-input').files;
    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        var reader = new FileReader();
        
        reader.onload = function(e) {
        var img = document.createElement('img');
        img.src = e.target.result;
        previewContainer.appendChild(img);
        img.classList.toggle('imagen-previsualizacion-incidencia');
        
        // Agregar botón de eliminar junto a la imagen
        var deleteButton = document.createElement('button');
        deleteButton.innerHTML = 'Eliminar';
        deleteButton.addEventListener('click', function() {
            previewContainer.removeChild(img); // Eliminar la imagen de la previsualización
            previewContainer.removeChild(deleteButton);
        });
        previewContainer.appendChild(deleteButton);
        };
        
        reader.readAsDataURL(file);
    }
}

// Reutilizando la funcion abrirCerrar para mostrar el filtro cuando pulsamos sobre filtrar


let filtrar = document.getElementById('filtro');

botonfiltro = document.getElementById('boton-filtro');

botonfiltro.addEventListener('click', () => abrirCerrar(filtrar));


let borrado_incidencia = document.querySelectorAll('.contenedor-borrado-incidencia');

boton_borrar = document.querySelectorAll('.boton-borrar-incidencia');

for( let i = 0; i < borrado_incidencia.length ; i++ ){
    boton_borrar[i].addEventListener('click', () => abrirCerrar(borrado_incidencia[i]));
}

