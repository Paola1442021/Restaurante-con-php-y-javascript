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

if ($metodo == 'POST') {
    // Obtener los datos del cuerpo de la solicitud
    $datos = json_decode(file_get_contents('php://input'), true);
    // Verificar si la categoría está presente en los datos
    $categoria = isset($datos['categoria']) ? $datos['categoria'] : null;

    consultaSelect($conexion, $categoria);
} else {
    echo "Método no permitido";
}

function consultaSelect($conexion, $categoria)
{
  
    $sql = ($categoria !== null) ? "SELECT * FROM productos WHERE tipo='" . $categoria . "'" : "No hay productos de esa categoria";

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
?>
