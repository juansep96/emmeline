<?php

require_once('../PDO.php');

$dniCliente = $_POST['dniCliente'];

$ObtenerCliente = $conexion -> prepare("SELECT * FROM clientes WHERE dni_cliente=:1 ");
$ObtenerCliente -> bindParam(':1',$dniCliente);
$ObtenerCliente -> execute();

if($ObtenerCliente->RowCount()>0){
    $result = $ObtenerCliente->fetchAll(\PDO::FETCH_ASSOC);
    print_r (json_encode($result));
}else{
    echo "NO";
}


?>
