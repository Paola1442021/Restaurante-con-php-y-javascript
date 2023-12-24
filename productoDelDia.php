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
    $dia = isset($datos['dia']) ? $datos['dia'] : null;

    consultaSelect($conexion, $dia);
} else {
    echo "Método no permitido";
}

function consultaSelect($conexion, $dia)
{
    $sql = "SELECT prodPri.nombre, prodPri.imagen, prodA.nombre as acompaniamiento, prodA.imagen, prodB.nombre as bebida, prodB.imagen, prom.precio 
            FROM promociones prom
            LEFT JOIN productos prodA ON prom.idAcompaniamiento = prodA.idProducto 
            LEFT JOIN productos prodB ON prom.idBebida = prodB.idProducto 
            LEFT JOIN productos prodPri ON prom.idProducto = prodPri.idProducto 
            WHERE prom.tipo_dia = '" . $dia . "'";

    $resultado = $conexion->query($sql);

    if ($resultado) {
        $datos = array();
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }

        echo json_encode($datos);
    } else {
        echo json_encode(array('error' => 'Error en la ejecución de la consulta: ' . $conexion->error));
    }
}


?>