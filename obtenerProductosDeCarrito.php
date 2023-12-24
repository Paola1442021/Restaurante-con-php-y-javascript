<?php

$host = "localhost";
$usuario = "root";
$password = "";
$basedeDatos = "restaurante";

$conexion = new mysqli($host, $usuario, $password, $basedeDatos);

if ($conexion->connect_error) {
    die("conexion no establecida" . $conexion->connect_error);
}

header("Content-Type: application/json");

// Permitir solicitudes desde cualquier origen (en un entorno de desarrollo)
header("Access-Control-Allow-Origin: *");
// Permitir solicitudes con los métodos POST, GET y OPTIONS
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
// Permitir el encabezado Content-Type
header("Access-Control-Allow-Headers: Content-Type");

header("Content-Type: application/json");

$metodo = $_SERVER['REQUEST_METHOD'];
// encontrar el id
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
$buscarId = explode('/', $path);
$id = ($path !== '/') ? end($buscarId) : null;


switch ($metodo){
    //select
        case 'GET':
            consultaSelect($conexion,$id);
        break;
        /*case 'DELETE':
            // Asegúrate de obtener los valores correctos para $idCarrito y $idProducto
            $idCarrito = $_GET['idCarrito'] ?? null;
            $idProducto = $_GET['idProducto'] ?? null;
            
            borrar($conexion, $idCarrito, $idProducto);
            break;*/
        default:
            echo "Metodo no permitido";
    
    
        }

       /* function borrar($conexion, $idCarrito, $idProducto) {
            try {
                mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
                // Utilizamos una consulta preparada para evitar inyección de SQL
                $sql = "DELETE FROM detallecarrito WHERE idCarrito = ? AND idProducto = ? LIMIT 1";
        
                // Preparamos la consulta
                $stmt = $conexion->prepare($sql);
        
                if ($stmt) {
                    // Ligamos los parámetros a la consulta preparada
                    $stmt->bind_param("ii", $idCarrito, $idProducto);
        
                    // Ejecutamos la consulta
                    $resultado = $stmt->execute();
        
                    // Verificamos si la consulta se ejecutó correctamente
                    if ($resultado) {
                        echo json_encode(array('mensaje' => 'Producto eliminado del carrito'));
                    } else {
                        echo json_encode(array('error' => 'Error al eliminar el producto del carrito: ' . $stmt->error));
                    }
        
                    // Cerramos la consulta preparada
                    $stmt->close();
                } else {
                    echo json_encode(array('error' => 'Error al preparar la consulta: ' . $conexion->error));
                }
            } catch (Exception $e) {
                echo json_encode(array('error' => 'Excepción: ' . $e->getMessage()));
            }
        }
        */
        function consultaSelect($conexion, $id)
{
    try {
        if ($id != null) {
            // Consulta preparada
            $sql = "SELECT
                p.nombre AS nombre_producto,
                p.id AS id_producto,
                p.imagen AS imagen_producto,
                p.precio*dc.cantidad AS precio,
                dc.cantidad AS cantidad_producto
            FROM
                detallecarrito dc
                JOIN productos p ON dc.idProducto = p.id
                JOIN carrito c ON dc.idCarrito = c.idCarrito
            WHERE
                c.idCarrito = ?";

            // Preparar la consulta
            $stmt = $conexion->prepare($sql);

            if ($stmt) {
                // Ligamos los parámetros a la consulta preparada
                $stmt->bind_param("i", $id);

                // Ejecutar la consulta
                $stmt->execute();

                // Obtener el resultado
                $resultado = $stmt->get_result();

                // Procesar los resultados
                if ($resultado) {
                    $datos = array();

                    while ($fila = $resultado->fetch_assoc()) {
                        $id_producto = $fila['id_producto']; // Utilizar el alias correcto

                        // Si el producto aún no está en el array $datos, agregarlo
                        if (!isset($datos[$id_producto])) {
                            $datos[$id_producto] = array(
                                'id_producto' => $id_producto,
                                'nombre_producto' => $fila['nombre_producto'],
                                'imagen_producto' => $fila['imagen_producto'],
                                'precio' => $fila['precio'],
                                'total_cantidad' => 0,
                            );
                        }

                        // Sumar la cantidad del producto actual al total
                        $datos[$id_producto]['total_cantidad'] += $fila['cantidad_producto'];
                    }

                    // Convertir el array indexado a un array simple
                    $resultado_final = array_values($datos);

                    echo json_encode($resultado_final);
                } else {
                    echo json_encode(array('error' => 'Error al obtener el resultado: ' . $conexion->error));
                }

                // Cerrar la consulta preparada
                $stmt->close();
            } else {
                echo json_encode(array('error' => 'Error al preparar la consulta: ' . $conexion->error));
            }
        }
    } catch (Exception $e) {
        echo json_encode(array('error' => 'Excepción: ' . $e->getMessage()));
    }
}

        
?>