<?php

require_once('../PDO.php');

$datos=$_POST['datos'];
$datos=json_decode($datos);

$InsertarCliente=$conexion->prepare("INSERT INTO clientes (nombre_cliente,apellido_cliente,dni_cliente,telefono_cliente,mail_cliente,fnac_cliente,idCiudad_cliente) VALUES (:1,:2,:3,:4,:5,:6,:7)");
$InsertarCliente->bindParam(':1',$datos[0]);
$InsertarCliente->bindParam(':2',$datos[1]);
$InsertarCliente->bindParam(':3',$datos[2]);
$InsertarCliente->bindParam(':4',$datos[3]);
$InsertarCliente->bindParam(':5',$datos[4]);
$InsertarCliente->bindParam(':6',$datos[5]);
$InsertarCliente->bindParam(':7',$datos[6]);
if($InsertarCliente->execute()){
  echo $conexion->lastInsertId();
}else{
    echo "\nPDO::errorInfo():\n";
    print_r($InsertarCliente->errorInfo());
}


?>
