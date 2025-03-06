<?php

require_once('../PDO.php');

$idTurno = $_POST['idTurno'];

$ObtenerTurno = $conexion -> prepare("SELECT * FROM turnos  WHERE id_turno=:1 ");
$ObtenerTurno -> bindParam(':1',$idTurno);
$ObtenerTurno -> execute();

$result = $ObtenerTurno->fetchAll(\PDO::FETCH_ASSOC);
print_r (json_encode($result));

?>
