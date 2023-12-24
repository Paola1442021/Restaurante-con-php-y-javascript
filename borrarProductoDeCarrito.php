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
$idCarrito = is_numeric($id) ? intval($id) : null;
$idProducto = is_numeric($id) ? intval($id) : null;

switch ($metodo){
    //select
    case 'GET':
        // consultaSelect($conexion,$id);
        break;
    case 'DELETE':
        // Asegúrate de obtener los valores correctos para $idCarrito y $idProducto
        borrar($conexion, $idCarrito, $idProducto);
        break;
    default:
        echo "Metodo no permitido";
}

function borrar($conexion, $idCarrito, $idProducto) {
    try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        // Utilizamos una consulta preparada para evitar inyección de SQL
        $sql = "DELETE FROM detallecarrito WHERE idCarrito = ? AND idProducto = ?";

        // Preparamos la consulta
        $stmt = $conexion->prepare($sql);

        if ($stmt) {
            // Ligamos los parámetros a la consulta preparada
            $stmt->bind_param("ii", $idCarrito, $idProducto);

            // Imprimir o registrar valores para depuración
            error_log("ID Carrito: $idCarrito, ID Producto: $idProducto");

            // Imprimir o registrar SQL para depuración
            error_log("SQL: $sql");

            // Ejecutamos la consulta
            $resultado = $stmt->execute();

            // Verificamos si la consulta se ejecutó correctamente
            if ($resultado) {
                echo json_encode(array('mensaje' => 'Producto eliminado del carrito'));
            } else {
                // Imprimir o registrar el error específico
                echo json_encode(array('error' => 'Error al eliminar el producto del carrito: ' . $stmt->error));
            }

            // Cerramos la consulta preparada
            $stmt->close();
        } else {
            // Imprimir o registrar el error específico
            echo json_encode(array('error' => 'Error al preparar la consulta: ' . $conexion->error));
        }
    } catch (Exception $e) {
        // Imprimir o registrar el error específico
        echo json_encode(array('error' => 'Excepción: ' . $e->getMessage()));
    }
}

?>
