<?php

require_once('../PDO.php');

$datos=$_POST['datos'];
$datos=json_decode($datos);

$ActualizarCliente=$conexion->prepare("UPDATE clientes SET nombre_cliente=:1,apellido_cliente=:2,dni_cliente=:3,telefono_cliente=:4,mail_cliente=:5,fnac_cliente=:6,idCiudad_cliente=:7 WHERE id_cliente=:8");
$ActualizarCliente->bindParam(':1',$datos[0]);
$ActualizarCliente->bindParam(':2',$datos[1]);
$ActualizarCliente->bindParam(':3',$datos[2]);
$ActualizarCliente->bindParam(':4',$datos[3]);
$ActualizarCliente->bindParam(':5',$datos[4]);
$ActualizarCliente->bindParam(':6',$datos[5]);
$ActualizarCliente->bindParam(':7',$datos[6]);
$ActualizarCliente->bindParam(':8',$datos[7]);
if($ActualizarCliente->execute()){
    echo $datos[7];
}else{
    echo "\nPDO::errorInfo():\n";
    print_r($ActualizarCliente->errorInfo());
}


?>
