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
    $datos = json_decode(file_get_contents('php://input'), true);

// Validar que los datos no estén vacíos
if (empty($datos['nombreUsuario']) || empty($datos['contrasenia'])) {
    http_response_code(400); // Bad Request
    echo json_encode(array('error' => 'El nombre de usuario y la contraseña no pueden estar vacíos'));
    return;
}


    consultaSelect($conexion, $datos['nombreUsuario'], $datos['contrasenia']);
} else {
    echo "Método no permitido";
}

function consultaSelect($conexion, $nombreU, $contrasenia)
{
    // Validar que el nombre no esté vacío
    if (empty($nombreU)) {
        http_response_code(400); // Bad Request
        echo json_encode(array('error' => 'El nombre de usuario no puede estar vacío'));
        return;
    }
    if (empty($contrasenia)) {
        http_response_code(400); // Bad Request
        echo json_encode(array('error' => 'La contraseña no puede estar vacía'));
        return;
    }

    // Usar parámetros seguros para evitar inyección de SQL
    $sql = "SELECT * FROM usuarios WHERE nombreUsuario = ? AND contrasenia = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('ss', $nombreU, $contrasenia);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado) {
        $datos = array();
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }

        echo json_encode($datos);
    }

}




?>
