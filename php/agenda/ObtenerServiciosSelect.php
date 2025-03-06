<?php

require_once('../PDO.php');

$idSucursal = $_POST['idSucursal'];

$ObtenerServicios = $conexion -> query("SELECT * FROM servicios WHERE estado_servicio=1 AND idSucursal_servicio='$idSucursal' ORDER BY nombre_servicio ASC ");

$result = $ObtenerServicios->fetchAll(\PDO::FETCH_ASSOC);
print_r (json_encode($result));

?>
