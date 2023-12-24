enviarFormulario();

async function enviarFormulario() {
    try {
        
        // Datos que deseas enviar en la solicitud POST 
        const data = {
            categoria: "Pizza"
        };

        // Configuración de la solicitud
        const url = `http://localhost:8080/apiweb/productosPorCategoria.php/`;
        const response = await fetch(url, {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const msj = document.getElementById('msgPizza');
        const productosContainer = document.getElementById('pizzaContainer');

        if (response.ok) {
            // Código de estado 200 indica éxito en una solicitud GET
            const productos = await response.json();
            // Verificar si hay un nombre de usuario en sessionStorage
            var nombreUsuario = sessionStorage.getItem('nombreUsuario');
                       // Limpiar contenido existente en el contenedor
                       productosContainer.innerHTML = '';
           
                       // Iterar sobre los productos y agregarlos al contenedor
                       productos.forEach(producto => {
                           const card = document.createElement('div');
                           card.className = 'col-md-4';
                           card.innerHTML = `
                           <div class="card mt-4 mb-4 border-0">
                           <img style="height: 320px;" class="card-img-top" src="../imagenes/${producto.imagen}" alt="${producto.nombre}">
                           <div style="height: 160px;" class="card-body">
                               <h3 class="d-flex flex-column align-items-center justify-content-center">${producto.nombre}</h3>
                               <p class="d-flex flex-column align-items-center justify-content-center">${producto.precio}</p>
                               ${nombreUsuario ? `<a onclick="anadirCarrito(${producto.id})">
                               <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag-plus-fill icono-carrito" viewBox="0 0 16 16">
                                   <path fill-rule="evenodd" d="M10.5 3.5a2.5 2.5 0 0 0-5 0V4h5zm1 0V4H15v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4h3.5v-.5a3.5 3.5 0 1 1 7 0M8.5 8a.5.5 0 0 0-1 0v1.5H6a.5.5 0 0 0 0 1h1.5V12a.5.5 0 0 0 1 0v-1.5H10a.5.5 0 0 0 0-1H8.5z"/>
                               </svg>
                           </a>` : ''}
                           </div>
                       </div>
           `;



                productosContainer.appendChild(card);
            });
        } else {
            // Manejar errores de red u otros errores del servidor
            const errorText = `Error en la solicitud: ${response.status} ${response.statusText}`;
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
        
                const iconoCarrito = document.querySelector('.icono-carrito');

                if (response.status === 201) {
                 
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