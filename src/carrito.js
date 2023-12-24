obtenerCarrito();
async function obtenerCarrito() {
    try {
        // Obtener el nombre de usuario almacenado en sessionStorage
        const miCarrito = sessionStorage.getItem('idCarrito');

        // Validar si el nombre de usuario está presente
        if (!miCarrito) {
            console.error('Carrito no encontrado en sessionStorage');
            return;
        }

        // Datos que deseas enviar en la solicitud GET 
        const url = `http://localhost:8080/apiweb/obtenerProductosDeCarrito.php/${miCarrito}`;
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        const msj = document.getElementById('msgProductos');
        const productosContainer = document.getElementById('productosContainer');
        if (response.ok) {
            // Código de estado 200 indica éxito en una solicitud GET
            const productos = await response.json();

            // Limpiar contenido existente en el contenedor
            productosContainer.innerHTML = '';

            // Verificar si hay productos en el carrito
            if (productos.length == 0) {
                // Mostrar mensaje de que no hay productos en el carrito
                msj.innerHTML = 'No hay productos en el carrito.';
            } else {
               
                productos.forEach(producto => {
                    const card = document.createElement('div');
                    card.className = 'col-md-3';
                    card.innerHTML = `
                        <div class="card mt-4 mb-4 border-0">
                            <img style="height: 320px;" class="card-img-top" src="../imagenes/${producto.imagen_producto}" alt="${producto.nombre_producto}">
                            <div style="height: 160px;" class="card-body">
                                <h3 class="d-flex flex-column align-items-center justify-content-center">${producto.nombre_producto}</h3>
                                <p class="d-flex flex-column align-items-center justify-content-center">$ ${producto.precio * producto.total_cantidad}</p>
                                <div class="cantidad-container">
                                    <span class="cantidad text-black">${producto.total_cantidad}</span>
                                    <button class="btn-aumentar rounded"><a onclick="anadirCarrito(${producto.id_producto})">+</a></button>
                                </div>
                            </div>
                        </div>
                    `;
                
                    productosContainer.appendChild(card);
                });
                
                // Crear el botón de borrar fuera del bucle
                const botonBorrar = document.createElement('button');
                botonBorrar.id='btnBorrar';
                botonBorrar.className = 'btn-borrar rounded';
                botonBorrar.innerHTML = `
                    <a onclick="borrarTodosLosProductosDelCarrito()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                        </svg>
                    </a>
                `;
                
                // Agregar el botón de borrar después de agregar todos los productos
                productosContainer.appendChild(botonBorrar);

                
                // Agregar el botón después de agregar todos los productos
    const botonCompletar = document.createElement('button');
    botonCompletar.className = 'btn-formenvio rounded';
    botonCompletar.innerHTML = 'Llenar datos de envío';

    // Crear el botón de borrar fuera del bucle

    // Agregar el evento que redirigirá a formularioenvio.html
    botonCompletar.addEventListener('click', function() {
        window.location.href = '../html/formularioenvio.html';
    });

    productosContainer.appendChild(botonCompletar);


            }
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

async function anadirCarrito(id) {
    try {

        /// Obtener los valores de los campos de entrada
let idProducto = id;
let cantidad = 1;

// Obtener el ID del carrito de sessionStorage
let idCarro = sessionStorage.getItem('idCarrito');

const data = {
    idProducto: idProducto,
    cantidad: cantidad,
    idCarrito: idCarro
};
 
        // Configuración de la solicitud
        const response = await fetch('http://localhost:8080/apiweb/detalleCarrito.php/', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        });

        let iconoCarrito = document.querySelector('.icono-carrito');

        if (response.status === 201) {
            await obtenerCarrito();
          // Código de estado 201 indica que el recurso fue creado con éxito
          iconoCarrito.classList.add('clicked'); // Agrega la clase de animación
    
          // Esperar un tiempo para que se vea la animación
          await new Promise(resolve => setTimeout(resolve, 500));
    
          iconoCarrito.classList.remove('clicked'); // Quita la clase después de la animación
        } else if (!response.ok) {
          // Manejar errores de red u otros errores del servidor
          const errorText = `Error en la solicitud: ${response.status} ${response.statusText}`;
          throw new Error(errorText);
        } else {
          // El código de estado es OK, pero la respuesta no tiene el formato esperado
          const res = await response.json();
        }
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
    