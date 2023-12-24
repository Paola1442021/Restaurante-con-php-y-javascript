// Al inicio de tu script
let comentarios = [];

async function enviarComentario(event) {
    try {
        console.log('La función enviarComentario se está ejecutando.');

        event.preventDefault(); // Evitar que el formulario se envíe de forma predeterminada

        // Obtener el comentario del campo de entrada
        let comentario = document.getElementById('comentarios').value;

        // Verificar si el campo está completo
        if (!comentario) {
            document.getElementById('msgComentario').innerText = 'Debe completar el formulario de comentarios';
            return;
        }

        // Obtener el nombre de usuario (asumiendo que se guarda en sessionStorage)
        let nombreUsuario = sessionStorage.getItem('nombreUsuario');

        // Agregar el comentario a la variable global
        comentarios.push({
            nombreUsuario: nombreUsuario,
            comentario: comentario
        });

        // Mostrar mensaje de éxito
        document.getElementById('msgComentario').innerText = 'Comentario enviado con éxito';

        // Llamar a la función para mostrar comentarios en el carrusel
        mostrarComentariosEnFooter();

    } catch (error) {
        console.error('Error en la solicitud de comentarios:', error.message);
    }
}

// Función para mostrar comentarios en el carrusel
function mostrarComentariosEnFooter() {
    // Obtener el elemento del carrusel donde mostrar los comentarios
    let comentariosCarousel = document.getElementById('comentariosCarousel');

    // Limpiar contenido existente
    comentariosCarousel.innerHTML = '';

    // Iterar sobre los comentarios y agregarlos al carrusel
    comentarios.forEach(comentario => {
        const comentarioElement = document.createElement('p');
        comentarioElement.innerText = `${comentario.nombreUsuario}: ${comentario.comentario}`;
        comentariosCarousel.appendChild(comentarioElement);
    });
}
