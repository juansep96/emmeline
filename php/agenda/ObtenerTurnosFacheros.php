<?php

require_once "../PDO.php";

$fecha = $_POST['fecha'];
$idServicio = $_POST['idServicio'];
$idSucursal = $_POST['idSucursal'];

$ObtenerTurnosFacheros = $conexion->prepare("SELECT * FROM turnos LEFT JOIN clientes ON id_cliente=idCliente_turno LEFT JOIN users ON id_user=idProfesional_turno LEFT JOIN servicios ON id_servicio=idServicio_turno WHERE fecha_turno >= :1 AND idSucursal_turno=:2");
$ObtenerTurnosFacheros -> bindParam(':1',$fecha);
$ObtenerTurnosFacheros -> bindParam(':2',$idSucursal);
$ObtenerTurnosFacheros -> execute();

$result = $ObtenerTurnosFacheros->fetchAll(\PDO::FETCH_ASSOC);
print_r (json_encode($result));




?>