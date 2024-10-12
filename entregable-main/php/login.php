<?php

$conexion= CrearConexion();

if ($conexion->connect_error) {
    die("Error en la conexion:" . $conexion->connect_error);
}

if(empty($_POST['email']) || empty($_POST['password'])){
    header("Location:index.php ");
}

try {

    $emailIngresado = LimpiarEntradas($_POST['email']);
    $contrasenaIngresar = LimpiarEntradas($_POST['password']);

    $sql = "SELECT * FROM usuarios WHERE email = ?";

    //Preparar una consulta 
    $queryPreparada = $conexion->prepare($sql);

    //Vincular los parametros 
    $queryPreparada->bind_param("s", $emailIngresado);

    //Ejecutar la consulta 
    $resultado = $queryPreparada->execute();
    $queryPreparada->store_result();

    if($resultado){
    //Vincular los resultados 

    $queryPreparada->bind_result($id, $nombreBd, $emailBd, $contrasenaCifradaBd,$salt);
    if($queryPreparada->fetch()){

        $contrasenaConSalt = $contrasenaIngresar . $salt;
        $contrasenaCifrada = hash('sha256', $contrasenaConSalt);

        


        if(trim($contrasenaCifradaBd) == trim($contrasenaCifrada)){
            session_start();
            $_SESSION['email'] = $usuario_db;

            header("Location: bienvenido.php");
        }
        else {
            echo" contraseña incorrecta";
        }
    }

    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    
}


function CrearConexion(){
    try{

        $host ="127.0.0.1";
        $usuario_db= "root";
        $contrasena_db = "";
        $nombre_db ="autenticacion";

        $conexion = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db);
        return $conexion;

    }catch(Exception $e){
        echo "Error: " . $e->getMessage();
    }
}

function LimpiarEntradas($contenido){
    $contenidoSinEspacios = trim($contenido);
    $contenidoLimpio = htmlspecialchars($contenidoSinEspacios, ENT_QUOTES, 'UTF-8');
    return $contenidoLimpio;
}

?>
