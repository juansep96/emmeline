<?php

require_once('../PDO.php');

$datos=$_POST['datos'];
$datos=json_decode($datos);

$ActualizarTurno=$conexion->prepare("UPDATE turnos SET idProfesional_turno=:1,estado_turno=:2 WHERE id_turno=:3");
$ActualizarTurno->bindParam(':1',$datos[1]);
$ActualizarTurno->bindParam(':2',$datos[2]);
$ActualizarTurno->bindParam(':3',$datos[0]);
if($ActualizarTurno->execute()){
    echo $datos[0];
}else{
    echo "\nPDO::errorInfo():\n";
    print_r($ActualizarTurno->errorInfo());
}


?>
