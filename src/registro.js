document.getElementById('Registrarse').addEventListener('click', enviarFormulario);

async function enviarFormulario(event) {
    try {
        event.preventDefault(); // Evitar que el formulario se envíe de forma predeterminada

        // Obtener los valores de los campos de entrada
        let nombre = document.getElementById('nombre').value;
        let nombreUsuario = document.getElementById('usuario').value;
        let contrasenia = document.getElementById('password').value;
        const esAdmin = 0;

         // Verificar si los campos están completos
         if (!nombre && !nombreUsuario && !contrasenia) {
            document.getElementById('mensaje').innerText = 'Debe completar el formulario';
            return;
        }
        // Datos que deseas enviar en la solicitud POST 
        const data = {
            nombre: nombre,
            nombreUsuario: nombreUsuario,
            contrasenia: contrasenia,
            esAdmin: esAdmin
        };
 // Elemento donde se mostrará el mensaje
 const mensajeElement = document.getElementById('mensaje');
        // Configuración de la solicitud
        const response = await fetch('http://localhost:8080/apiweb/index.php', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.status === 201) {
            // Código de estado 201 indica que el recurso fue creado con éxito
            mensajeElement.innerText = 'Registro exitoso';
        } else if (!response.ok) {
            // Manejar errores de red u otros errores del servidor
            const errorText = `Error en la solicitud: ${response.status} ${response.statusText}`;
            mensajeElement.innerText = errorText;
            throw new Error(errorText);
        } else {
            // El código de estado es OK, pero la respuesta no tiene el formato esperado
            const res = await response.json();
            mensajeElement.innerText = `Error en el registro: ${res.error || 'Error desconocido'}`;
        }
    } catch (error) {
        console.error('Error en la solicitud:', error.message);
    }
}