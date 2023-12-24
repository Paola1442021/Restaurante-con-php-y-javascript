<?php

$host = "localhost";
$usuario = "root";
$password="";
$basedeDatos="restaurante";

$conexion= new mysqli($host,$usuario,$password,$basedeDatos);

if($conexion->connect_error){
    die ("conexion no establecida". $conexion->connect_error);
}

header("Content-Type: application/json"); 
$metodo= $_SERVER['REQUEST_METHOD'];
//encontrar el id
$path= isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'/';
$buscarId = explode('/',$path);
$id= ($path!=='/') ? end($buscarId):null;


//$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : null;

switch ($metodo){
//select
case 'GET':
    /*if ($nombre !== null) {
        consultaSelectNombre($conexion, $nombre);
    } else {*/
        consultaSelect($conexion, $id);
    /*}*/
    break;
//insert
    case 'POST':
        insertar($conexion);
        break;
//update
    case 'PUT':
        actualizar($conexion, $id);
        break;
//delete
    case 'DELETE':
        borrar($conexion, $id);
        break;
    default:
        echo "Metodo no permitido";


    }

    function consultaSelect($conexion, $id) {
        
        if ($id !== null) {
            // Caso 2: Se proporciona solo el id
            $sql = "SELECT * FROM productos WHERE id=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id);
        } else {
            // Caso 1: No se proporciona ni id ni nombre
            $sql = "SELECT * FROM productos";
            $stmt = $conexion->prepare($sql);
        }
    
        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            $datos = $resultado->fetch_all(MYSQLI_ASSOC);
            echo json_encode($datos);
        } else {
            echo json_encode(array('error' => 'Error en la ejecución de la consulta.'));
        }
    
        $stmt->close();
    }
  /*  function consultaSelectNombre($nombre) {
        if ($nombre !== null) {
            // Caso 3: Se proporciona solo el nombre
            $sql = "SELECT * FROM productos WHERE nombre=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("s", $nombre);
        
    
        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            $datos = $resultado->fetch_all(MYSQLI_ASSOC);
            echo json_encode($datos);
        } else {
            echo json_encode(array('error' => 'Error en la ejecución de la consulta.'));
        }
    
        $stmt->close();
    }}*/
    
    
    
    function insertar($conexion) {
        $dato = json_decode(file_get_contents('php://input'), true);
        $nombre = $dato['nombre'];
        $descripcion = $dato['descripcion'];
        $precio = $dato['precio'];
        $tipo = $dato['tipo'];
        $imagen = $dato['imagen'];
    
        // Validar que el nombre no esté vacío
        if (empty($nombre)) {
            echo json_encode(array('error' => 'El nombre no puede estar vacío'));
            return;
        }
    
        // Validar que el nombre no se repita
        $stmt = $conexion->prepare("SELECT * FROM productos WHERE nombre = ?");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            echo json_encode(array('error' => 'El nombre ya existe'));
            $stmt->close();
            return;
        }
        $stmt->close();
    
        // Validar que el precio sea un número mayor a 0
        if (!is_numeric($precio) || $precio <= 0) {
            echo json_encode(array('error' => 'El precio debe ser un número mayor a 0'));
            return;
        }
    
        // Validar que el tipo sea uno de los tipos permitidos
        $tiposPermitidos = ['Hamburguesas', 'Sandwiches', 'Postres', 'Helados', 'Wraps', 'Papas', 'Bebidas', 'Ensaladas', 'Pizza', 'Cafes', 'Meriendas', 'Snacks', 'Otros'];
        if (!in_array($tipo, $tiposPermitidos)) {
            echo json_encode(array('error' => 'El tipo no es válido'));
            return;
        }
    
        // Insertar el producto utilizando declaraciones preparadas
        $stmt = $conexion->prepare("INSERT INTO productos(nombre, descripcion, precio, tipo, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $nombre, $descripcion, $precio, $tipo, $imagen);
        $resultado = $stmt->execute();
        $stmt->close();
    
        if ($resultado) {
            $dato['id'] = $conexion->insert_id;
            echo json_encode($dato);
        } else {
            echo json_encode(array('error' => 'Error al crear el producto'));
        }
    }
    
    function borrar($conexion, $id) {

        // Obtener el nombre de la imagen antigua
        $stmt = $conexion->prepare("SELECT imagen FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($imagen_antigua);
        $stmt->fetch();
        $stmt->close();
        $sql= "DELETE FROM productos WHERE id = $id";
        $resultado= $conexion->query($sql);

        // Eliminar la imagen antigua del sistema de archivos
        if (!empty($imagen_antigua)) {
            $ruta_imagen_antigua = 'imagenes/' . $imagen_antigua;
            unlink($ruta_imagen_antigua);
        }
        if($resultado){
            echo json_encode(array('mensaje'=>'producto eliminado'));
        }else{
            echo json_encode(array('error'=>'Error al eliminar el producto'));
        }
        }
    
    
    
    
    
    function actualizar($conexion, $id) {
        $dato = json_decode(file_get_contents('php://input'), true);
    
        // Verificar si los campos necesarios están presentes
        if (!isset($dato['nombre'], $dato['descripcion'], $dato['tipo'], $dato['precio'], $dato['imagen'])) {
            echo json_encode(array('error' => 'Datos incompletos en la solicitud'));
            return;
        }
    
        $nombre = $dato['nombre'];
        $descripcion = $dato['descripcion'];
        $tipo = $dato['tipo'];
        $precio = $dato['precio'];
        $imagen_nueva = $dato['imagen'];
    
        // Obtener el nombre de la imagen antigua
        $stmt = $conexion->prepare("SELECT imagen FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($imagen_antigua);
        $stmt->fetch();
        $stmt->close();
    
        // Validar que el nombre de producto no se repita
        $sql = "SELECT id FROM productos WHERE nombre = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->store_result();
    
        if ($stmt->num_rows > 0) {
            // Si el nombre de producto encontrado es diferente al que estás intentando actualizar, mostrar un error
            $stmt->bind_result($existingId);
            $stmt->fetch();
            if ($existingId != $id) {
                echo json_encode(array('error' => 'El nombre ya existe'));
                $stmt->close();
                return;
            }
        }
    
        // Subir la nueva imagen
        $ruta_imagen_nueva = 'imagenes/' . $imagen_nueva;
        // ... código para subir la imagen al servidor, por ejemplo, usando move_uploaded_file()
    
        // Actualizar el producto
        $sql = "UPDATE productos SET nombre=?, descripcion=?, tipo=?, precio=?, imagen=? WHERE id=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssidsi", $nombre, $descripcion, $tipo, $precio, $imagen_nueva, $id);
        $resultado = $stmt->execute();
        $stmt->close();
    
        if ($resultado) {
            echo json_encode(array('mensaje' => 'Producto actualizado'));
    
            // Eliminar la imagen antigua del sistema de archivos
            if (!empty($imagen_antigua)) {
                $ruta_imagen_antigua = 'imagenes/' . $imagen_antigua;
                unlink($ruta_imagen_antigua);
            }
        } else {
            echo json_encode(array('error' => 'Error al modificar el producto'));
        }
    }
    
    
    
?>