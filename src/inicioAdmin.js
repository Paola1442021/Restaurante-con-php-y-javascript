// Llamar a la función para enviar la solicitud y mostrar los productos
enviarFormulario();

// Definir la función asíncrona para enviar la solicitud
async function enviarFormulario() {
    try {
        // Configuración de la solicitud para obtener los pedidos
        const url = `http://localhost:8080/apiweb/pedidoAdmin.php/`;
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        // Obtener referencias a elementos del DOM
        const msj = document.getElementById('msgProductos');
        const productosContainer = document.getElementById('productosContainer');

        // Verificar si la solicitud fue exitosa
        if (response.ok) {
            // Código de estado 200 indica éxito en una solicitud GET
            // Obtener la lista de productos desde la respuesta
            const productos = await response.json();

            // Objeto para agrupar productos por carrito
            const productosPorCarrito = {};

            // Iterar sobre la lista de productos y agrupar por carrito
            productos.forEach(producto => {
                const idCarrito = producto.idCarrito;
                if (!productosPorCarrito[idCarrito]) {
                    productosPorCarrito[idCarrito] = [];
                }
                productosPorCarrito[idCarrito].push(producto);
            });

            // Limpiar contenido existente en el contenedor
            productosContainer.innerHTML = '';

            // Crear una fila (row) para alinear las tarjetas en la parte superior
            const row = document.createElement('div');
            row.className = 'row align-items-start';

            // Iterar sobre los carritos y renderizar los productos
            for (const idCarrito in productosPorCarrito) {
                const productosDeCarrito = productosPorCarrito[idCarrito];

                // Crear un nuevo elemento div para el carrito
                const card = document.createElement('div');
                card.className = 'col-md-3 card mt-4  border-0 m-2'; // Agregar la clase "card" aquí
                card.innerHTML = `
                    <div class="card-body">
                    <button class="btn- rounded"><a onclick="eliminarPedido(${idCarrito})">Enviar cadete</button>
                        <h5 class="card-title mt-2 mx-1">Carrito :  ${idCarrito}</h5>
                        ${productosDeCarrito.map(producto => `
                            <h6 class=" m-1">Producto a enviar: ${producto.productoid}</h6>
                            <p class="m-1">Cantidad a enviar :${producto.cantidad}</p>
                            <p class="m-1">Direccion a enviar :${producto.direccion}</p>
                        `).join('')}
                    </div>
                `;
                // Agregar el nuevo elemento a la fila
                row.appendChild(card);
            }

            // Agregar la fila al contenedor
            productosContainer.appendChild(row);
        } else {
            // Manejar errores de red u otros errores del servidor
            const errorText = `Error en la solicitud: ${response.status} ${response.statusText}`;
            throw new Error(errorText);
        }
    } catch (error) {
        // Capturar y manejar cualquier error durante la ejecución
        console.error('Error en la solicitud:', error.message);
    }
}


async function eliminarPedido(idCarro) {
    try {
        

        const data = {
            idCarrito: idCarro
        };

        // Configuración de la solicitud
        console.log('Solicitud de eliminación:', JSON.stringify(data));

        const response = await fetch(`http://localhost:8080/apiweb/pedidoAdmin.php/${idCarro}`, {
            method: 'DELETE',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        });

        console.log('Respuesta de la solicitud:', response);

        if (response.status === 200) {
            await enviarFormulario();
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

