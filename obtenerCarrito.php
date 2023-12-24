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

if ($metodo == 'GET') {
    // Obtener los datos del cuerpo de la solicitud
    $nombreUsuario = obtenerNombreUsuario();

    if (empty($nombreUsuario)) {
        http_response_code(400); // Bad Request
        echo json_encode(array('error' => 'El nombre de usuario no puede estar vacío'));
        return;
    }

    consultaCarrito($conexion, $nombreUsuario);
} else {
    echo "Método no permitido";
}

function obtenerNombreUsuario() {
    // Intentar obtener el nombre de usuario de los parámetros de la URL
    if (isset($_GET['nombreUsuario'])) {
        return $_GET['nombreUsuario'];
    }

    // Si no se encuentra en la URL, intentar obtenerlo del cuerpo de la solicitud
    $datos = json_decode(file_get_contents('php://input'), true);
    return isset($datos['nombreUsuario']) ? $datos['nombreUsuario'] : null;
}

function consultaCarrito($conexion, $nombreUsuario) {
    // Validar que el nombre no esté vacío
    if (empty($nombreUsuario)) {
        http_response_code(400); // Bad Request
        echo json_encode(array('error' => 'El nombre de usuario no puede estar vacío'));
        return;
    }

    // Usar parámetros seguros para evitar inyección de SQL
    $sql = "SELECT idCarrito FROM usuarios, carrito WHERE usuarios.id = carrito.idUsuario AND nombreUsuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('s', $nombreUsuario); // Cambiado a 's' ya que es un solo parámetro
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado) {
        $datos = array();
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }

        echo json_encode(array('idCarrito' => isset($datos[0]['idCarrito']) ? $datos[0]['idCarrito'] : null));
    }
}


?>
