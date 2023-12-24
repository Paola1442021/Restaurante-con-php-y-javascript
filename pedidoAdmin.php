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
header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
// Permitir el encabezado Content-Type
header("Access-Control-Allow-Headers: Content-Type");

header("Content-Type: application/json");

$metodo = $_SERVER['REQUEST_METHOD'];
// encontrar el id
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
$buscarId = explode('/', $path);
$id = ($path !== '/') ? end($buscarId) : null;

switch ($metodo) {
    //select
    case 'GET':
        todosLosPedidos($conexion);
        break;
    //insert
    case 'POST':
        insertarPedidoProductosDeCarro($conexion);
        break;
    //delete
    case 'DELETE':
        borrarPedido($conexion, $id);
       // borrarTodosLosPedidos($conexion);
        break;
    default:
        echo "Metodo no permitido";
}





function todosLosPedidos($conexion)
{
    $sql = "SELECT * FROM pedido";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        $datos = array();
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }

        echo json_encode($datos);
    }
}


function insertarPedidoProductosDeCarro($conexion)
{
    $dato = json_decode(file_get_contents('php://input'), true);
    $idCarrito = $dato['idCarrito'];
    $productoid = $dato['productoid'];
    $cantidad = $dato['cantidad'];
    $direccion = $dato['direccion'];

    // Validar que el nombre no esté vacío
    if (empty($direccion)) {
        http_response_code(400); // Bad Request
        echo json_encode(array('error' => 'La direccion no puede estar vacía'));
        return;
    }

    $sql = "INSERT INTO pedido(idCarrito,productoid,cantidad,direccion) VALUES ($idCarrito,$productoid,$cantidad,'$direccion')";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        http_response_code(201); // Created
        $dato['id'] = $conexion->insert_id;
        echo json_encode($dato);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(array('error' => 'Error al crear pedido: ' . $conexion->error));
    }
}

function borrarTodosLosPedidos($conexion)
{
    $sql = "DELETE FROM pedido";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        echo json_encode(array('mensaje' => 'pedidos eliminados'));
    } else {
        echo json_encode(array('error' => 'Error al eliminar los pedidos'));
    }
}

function borrarPedido($conexion, $id)
{
    $stmt = $conexion->prepare("DELETE FROM pedido WHERE idCarrito = ?");
    $stmt->bind_param("i", $idCarrito);
    $idCarrito = $id;
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(array('mensaje' => 'pedido eliminado'));
    } else {
        echo json_encode(array('error' => 'Error al eliminar el pedido'));
    }

    $stmt->close();
}
?>
