
async function enviarFormulario(event) {
    try {
        console.log('La función enviarFormulario se está ejecutando.');

        event.preventDefault(); // Evitar que el formulario se envíe de forma predeterminada

        // Obtener los valores de los campos de entrada
        let calle = document.getElementById('direccion').value;
        let numero = document.getElementById('numero').value;
        let numeroTarjeta = document.getElementById('numeroTarjeta').value;
        let vencimiento = document.getElementById('vencimiento').value;

        // Verificar si los campos están completos
        if (!numeroTarjeta || !calle || !numero || !vencimiento) {
            document.getElementById('msgpedido').innerText = 'Debe completar el formulario';
            return;
        }
        if(!validarTarjeta()|| !validarVencimiento()){
            document.getElementById('msgpedido').innerText = 'Debe ingresar un numero de tarjeta correcto y que no este vencida';
            return;

        }
        const miCarrito = sessionStorage.getItem('idCarrito');

        // Obtener la lista de productos del carrito
        const urlCarrito = `http://localhost:8080/apiweb/obtenerProductosDeCarrito.php/${miCarrito}`;
        const responseCarrito = await fetch(urlCarrito, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!responseCarrito.ok) {
            const errorText = `Error en la solicitud del carrito: ${responseCarrito.status} ${responseCarrito.statusText}`;
            console.error(errorText);
            throw new Error(errorText);
        }

        const productosCarrito = await responseCarrito.json();

        // Iterar sobre los productos del carrito y realizar la inserción
        for (let producto of productosCarrito) {
            const data = {
                idCarrito: miCarrito,
                productoid: producto.id_producto,
                cantidad: producto.total_cantidad,
                direccion: calle + " " + numero
            };

            console.log('Datos a enviar:', data);

            // Configuración de la solicitud
            const urlPedido = `http://localhost:8080/apiweb/pedidoAdmin.php/`;
            const responsePedido = await fetch(urlPedido, {
                method: 'POST',
                body: JSON.stringify(data),
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (!responsePedido.ok) {
                const errorText = `Error en la solicitud del pedido: ${responsePedido.status} ${responsePedido.statusText}`;
                console.error(errorText);
                throw new Error(errorText);
            }
        }

        // Mostrar mensaje de éxito
        document.getElementById('msgpedido').innerText = 'Pedido confirmado';
        // Llamar a la función para borrar todos los productos del carrito
        await borrarTodosLosProductosDelCarrito();
        
            window.location.href = '../html/comentarios.html';
       

    } catch (error) {
        console.error('Error en la solicitud:', error.message);
    }
}


async function borrarTodosLosProductosDelCarrito() {
    try {
        // Obtener el ID del carrito de sessionStorage
        let idCarro = sessionStorage.getItem('idCarrito');
        console.log('ID del carrito:', idCarro);

        const data = {
            idCarrito: idCarro
        };

        // Configuración de la solicitud
        console.log('Solicitud de eliminación:', JSON.stringify(data));

        const response = await fetch(`http://localhost:8080/apiweb/borrarTodosLosProductos.php/${idCarro}`, {
            method: 'DELETE',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        });

        console.log('Respuesta de la solicitud:', response);

        if (response.status === 200) {
            await obtenerCarrito();
        } else if (!response.ok) {
            // Manejar errores de red u otros errores del servidor
            const errorText = `Error en la solicitud: ${response.status} ${response.statusText}`;
            throw new Error(errorText);
        } else {
            // El código de estado es OK, pero la respuesta no tiene el formato esperado
            const res = await response.json();
            console.log('Respuesta inesperada:', res);
        }
    } catch (error) {
        console.error('Error en la solicitud:', error.message);
    }
}

function validarTarjeta() {
    var numeroTarjeta = document.getElementById('numeroTarjeta').value;
    var regex = /^[0-9]+$/;

    if (!regex.test(numeroTarjeta)) {
        document.getElementById('msgpedido').innerText = 'Número de tarjeta inválido. Debe contener solo números.';
        return false;
    }

    return true;
}

function validarVencimiento() {
    var fechaVencimiento = new Date(document.getElementById('vencimiento').value);
    var hoy = new Date();

    if (fechaVencimiento <= hoy) {
        document.getElementById('msgpedido').innerText = 'La fecha de vencimiento debe ser posterior a hoy.';
        return false;
    }

    return true;
}
