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



// Permitir solicitudes desde cualquier origen (en un entorno de desarrollo)
header("Access-Control-Allow-Origin: *");
// Permitir solicitudes con los métodos POST, GET y OPTIONS
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
// Permitir el encabezado Content-Type
header("Access-Control-Allow-Headers: Content-Type");

header("Content-Type: application/json"); 



$metodo= $_SERVER['REQUEST_METHOD'];
//encontrar el id
$path= isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'/';
$buscarId = explode('/',$path);
$id= ($path!=='/') ? end($buscarId):null;

switch ($metodo){
//select
    case 'GET':
        consultaSelect($conexion,$id);
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

    function consultaSelect($conexion,$id){
        $sql=($id===null) ? "SELECT * FROM usuarios" : "SELECT * FROM usuarios WHERE id=$id";
        $resultado= $conexion->query($sql);

        if($resultado){
            $datos= array();
            while($fila= $resultado->fetch_assoc()){
                $datos[]= $fila;
            }

            echo json_encode($datos);
        }
    }

    function insertar($conexion){
        $dato= json_decode(file_get_contents('php://input'),true);
        $nombre=$dato['nombre'];
        $nombreUsuario=$dato['nombreUsuario'];
        $contrasenia=$dato['contrasenia'];
        $contraseniaEncriptada=encriptar_contrasenia($contrasenia);
        $esAdmin=$dato['esAdmin'];
    
        // Validar que el nombre no esté vacío
        if (empty($nombre)) {
            http_response_code(400); // Bad Request
            echo json_encode(array('error'=>'El nombre no puede estar vacío'));
            return;
        }
    
        // Validar que el nombre de usuario tenga al menos 6 caracteres
        if (strlen($nombreUsuario) < 6) {
            echo json_encode(array('error'=>'El nombre de usuario debe tener al menos 6 caracteres'));
            return;
        }
    
        // Validar que el nombre de usuario no se repita
        $sql = "SELECT * FROM usuarios WHERE nombreUsuario = '$nombreUsuario'";
        $resultado = $conexion->query($sql);
        if ($resultado->num_rows > 0) {
            echo json_encode(array('error'=>'El nombre de usuario ya existe'));
            return;
        }
  // Validar que la contraseña cumpla con los requisitos
// Verificar la longitud mínima de 8 caracteres
if (strlen($contrasenia) < 8) {
    echo json_encode(array('error' => 'La contraseña debe tener al menos 8 caracteres'));
    return;
}

// Verificar al menos una letra mayúscula en cualquier posición
if (!preg_match("/[A-Z]/", $contrasenia)) {
    echo json_encode(array('error' => 'La contraseña debe contener al menos una letra mayúscula'));
    return;
}

// Verificar al menos una letra minúscula en cualquier posición
if (!preg_match("/[a-z]/", $contrasenia)) {
    echo json_encode(array('error' => 'La contraseña debe contener al menos una letra minúscula'));
    return;
}

// Verificar al menos un número en cualquier posición
if (!preg_match("/\d/", $contrasenia)) {
    echo json_encode(array('error' => 'La contraseña debe contener al menos un número'));
    return;
}

// Verificar al menos un carácter especial en cualquier posición
if (!preg_match("/[@$!%*?&]/", $contrasenia)) {
    echo json_encode(array('error' => 'La contraseña debe contener al menos un carácter especial'));
    return;
}


    $sql="INSERT INTO usuarios(nombre,nombreUsuario,contrasenia,contraseniaEncriptada,esAdmin) VALUES ('$nombre','$nombreUsuario','$contrasenia','$contraseniaEncriptada','$esAdmin')";
    $resultado= $conexion->query($sql);

    if ($resultado) {
        http_response_code(201); // Created
        $dato['id'] = $conexion->insert_id;
        echo json_encode($dato);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(array('error' => 'Error al crear usuario: ' . $conexion->error));
    }
    
    }
    function encriptar_contrasenia($contrasenia) {
        // Generar un hash de contraseña utilizando el algoritmo bcrypt
        $hash = password_hash($contrasenia, PASSWORD_DEFAULT);
        // Retornar el hash de contraseña
        return $hash;
    }

    function borrar($conexion, $id){
        $sql= "DELETE FROM usuarios WHERE id = $id";
        $resultado= $conexion->query($sql);

        if($resultado){
            echo json_encode(array('mensaje'=>'usuario eliminado'));
        }else{
            echo json_encode(array('error'=>'Error al eliminar al usuario'));
        }

    }
  
    function actualizar($conexion, $id) {
        $dato = json_decode(file_get_contents('php://input'), true);
    
        // Verificar si los campos necesarios están presentes
        if (!isset($dato['nombre'], $dato['nombreUsuario'], $dato['contrasenia'])) {
            echo json_encode(array('error' => 'Datos incompletos en la solicitud'));
            return;
        }
    
        $nombre = $dato['nombre'];
        $contrasenia = $dato['contrasenia'];
        $contraseniaEncriptada = encriptar_contrasenia($contrasenia);
        $nombreUsuario = $dato['nombreUsuario'];

// Validar que el nombre no esté vacío
if (empty($nombre)) {
    echo json_encode(array('error'=>'El nombre no puede estar vacío'));
    return;
}

// Validar que el nombre de usuario tenga al menos 6 caracteres
if (strlen($nombreUsuario) < 6) {
    echo json_encode(array('error'=>'El nombre de usuario debe tener al menos 6 caracteres'));
    return;
}

// Validar que el nombre de usuario no se repita
$sql = "SELECT * FROM usuarios WHERE nombreUsuario = '$nombreUsuario'";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    // Si el nombre de usuario encontrado es diferente al que estás intentando actualizar, mostrar un error
    $fila = $resultado->fetch_assoc();
    if ($fila['id'] != $id) {
        echo json_encode(array('error' => 'El nombre de usuario ya existe'));
        return;
    }
}

// Resto de tu código...


// Validar que la contraseña cumpla con los requisitos
// Verificar la longitud mínima de 8 caracteres
if (strlen($contrasenia) < 8) {
echo json_encode(array('error' => 'La contraseña debe tener al menos 8 caracteres'));
return;
}

// Verificar al menos una letra mayúscula en cualquier posición
if (!preg_match("/[A-Z]/", $contrasenia)) {
echo json_encode(array('error' => 'La contraseña debe contener al menos una letra mayúscula'));
return;
}

// Verificar al menos una letra minúscula en cualquier posición
if (!preg_match("/[a-z]/", $contrasenia)) {
echo json_encode(array('error' => 'La contraseña debe contener al menos una letra minúscula'));
return;
}

// Verificar al menos un número en cualquier posición
if (!preg_match("/\d/", $contrasenia)) {
echo json_encode(array('error' => 'La contraseña debe contener al menos un número'));
return;
}

// Verificar al menos un carácter especial en cualquier posición
if (!preg_match("/[@$!%*?&]/", $contrasenia)) {
echo json_encode(array('error' => 'La contraseña debe contener al menos un carácter especial'));
return;
}


    
       // Utilizar la contraseña encriptada en la actualización
    $sql = "UPDATE usuarios SET nombre = '$nombre',nombreUsuario = '$nombreUsuario',contrasenia ='$contrasenia', contraseniaEncriptada = '$contraseniaEncriptada' WHERE id = $id";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        echo json_encode(array('mensaje' => 'Usuario actualizado'));
    } else {
        echo json_encode(array('error' => 'Error al modificar usuario'));
    }
    }
?>