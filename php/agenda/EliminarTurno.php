<?php

require_once('../PDO.php');

$idTurno = $_POST['idTurno'];

$$EliminarTurno = $conexion -> prepare("DELETE FROM turnos WHERE id_turno=:1");
$$EliminarTurno -> bindParam(':1',$idTurno);
$$EliminarTurno -> execute();

$result = $$EliminarTurno->fetchAll(\PDO::FETCH_ASSOC);
print_r (json_encode($result));

?>
