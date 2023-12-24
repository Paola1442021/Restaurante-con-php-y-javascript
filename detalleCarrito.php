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

$metodo = $_SERVER['REQUEST_METHOD'];
// Encontrar el id
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
$buscarId = explode('/', $path);
$id = ($path !== '/') ? end($buscarId) : null;

switch ($metodo) {
    // Select
    case 'GET':
        consultaSelect($conexion, $id);
        break;
    // Insert
    case 'POST':
        insertar($conexion);
        break;
    // Update
    case 'PUT':
        actualizar($conexion, $id);
        break;
    // Delete
    case 'DELETE':
        borrar($conexion, $id);
        break;
    default:
        echo "Método no permitido";
}

function consultaSelect($conexion, $id)
{
    $sql = "SELECT productos.nombre, detallecarrito.cantidad FROM usuarios
            JOIN carrito ON usuarios.id = carrito.idUsuario
            JOIN detallecarrito ON carrito.idCarrito = detallecarrito.idCarrito
            JOIN productos ON detallecarrito.idProducto = productos.id";

    if ($id !== null) {
        $sql .= " WHERE usuarios.id = $id";
    }

    $resultado = $conexion->query($sql);

    if ($resultado) {
        $datos = array();
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }

        echo json_encode($datos);
    } else {
        echo json_encode(array('error' => 'Error en la ejecución de la consulta.'));
    }
}

function insertar($conexion)
{
    $dato = json_decode(file_get_contents('php://input'), true);
    $idProducto = $dato['idProducto'];
    $cantidad = $dato['cantidad'];
    $idCarrito = $dato['idCarrito'];

    $sql = "INSERT INTO detallecarrito (idCarrito, idProducto, cantidad) VALUES ('$idCarrito', '$idProducto', '$cantidad')";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        http_response_code(201); // Created
        $dato['id'] = $conexion->insert_id;
        echo json_encode($dato);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(array('error' => 'Error al insertar detalle de carrito: ' . $conexion->error));
    }
}

function borrar($conexion, $id)
{
    $sql = "DELETE FROM detallecarrito WHERE idDetalleCarrito = $id";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        echo json_encode(array('mensaje' => 'Detalle del carrito eliminado'));
    } else {
        echo json_encode(array('error' => 'Error al eliminar el detalle del carrito: ' . $conexion->error));
    }
}

function actualizar($conexion, $id)
{
    $dato = json_decode(file_get_contents('php://input'), true);
    $cantidad = $dato['cantidad'];
    $idProducto = $dato['idProducto'];

    $sql = "UPDATE detallecarrito SET cantidad = '$cantidad' WHERE idDetalleCarrito = '$id' AND idProducto = '$idProducto'";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        echo json_encode(array('mensaje' => 'Detalle actualizado'));
    } else {
        echo json_encode(array('error' => 'Error al actualizar detalle: ' . $conexion->error));
    }
}

?>
