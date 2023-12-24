document.getElementById('Login').addEventListener('click', enviarFormulario);

async function enviarFormulario(event) {
    try {
        event.preventDefault(); // Evitar que el formulario se envíe de forma predeterminada

        // Obtener los valores de los campos de entrada
        let usuario = document.getElementById('usuarioLogin').value;
        let contrasenia = document.getElementById('contraseniaLogin').value;

        // Verificar si los campos están completos
        if (!usuario || !contrasenia) {
            document.getElementById('msgLogin').innerText = 'Debe completar el formulario';
            return;
        }

        // Datos que deseas enviar en la solicitud POST 
        const data = {
            nombreUsuario: usuario,
            contrasenia: contrasenia
        };

        // Configuración de la solicitud
        const url = `http://localhost:8080/apiweb/login.php/`;
        const response = await fetch(url, {
            method: 'POST',  
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const msj = document.getElementById('msgLogin');
        let exito = false;

        if (response.ok) {
            // Código de estado 200 indica éxito en una solicitud GET

            // Almacenar el nombre de usuario en sessionStorage
            sessionStorage.setItem('nombreUsuario', usuario);
            exito = true;
            
            // Llamar a la función para obtener el carrito después de iniciar sesión
            if (exito) {
                await obtenerCarrito();
                // Obtener el ID del carrito de sessionStorage
                const idCarrito = sessionStorage.getItem('idCarrito');
                console.log(idCarrito)
            }

            if(usuario=='administrador')
            {
                window.location.href = '../html/inicioAdmin.html';
            }
            
        else{
            
                window.location.href = '../html/index.html';
        }
        } else {
            // Manejar errores de red u otros errores del servidor
            const errorText = `Error en la solicitud: ${response.status} ${response.statusText}`;
            msj.innerText = errorText;
            throw new Error(errorText);
        }
    } catch (error) {
        console.error('Error en la solicitud:', error.message);
    }
}

async function obtenerCarrito() {
    try {
        // Obtener el nombre de usuario almacenado en sessionStorage
        const nombreUsuario = sessionStorage.getItem('nombreUsuario');

        // Validar si el nombre de usuario está presente
        if (!nombreUsuario) {
            console.error('Nombre de usuario no encontrado en sessionStorage');
            return;
        }

        // Datos que deseas enviar en la solicitud GET 
        const url = `http://localhost:8080/apiweb/obtenerCarrito.php/?nombreUsuario=${nombreUsuario}`;
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            // Código de estado 200 indica éxito en una solicitud GET
            const carrito = await response.json();
            sessionStorage.setItem('idCarrito', carrito.idCarrito);
        } else {
            // Manejar errores de red u otros errores del servidor
            const errorText = `Error en la solicitud: ${response.status} ${response.statusText}`;
            console.error(errorText);
            throw new Error(errorText);
        }
    } catch (error) {
        console.error('Error en la solicitud:', error.message);
    }
}
