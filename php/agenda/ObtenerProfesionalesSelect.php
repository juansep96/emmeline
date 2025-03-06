<?php

require_once('../PDO.php');

if(isset($_POST['idServicio'])){
    $idServicio = $_POST['idServicio'];
    $ObtenerProfesionales = $conexion -> query("SELECT * FROM users left join servicios on idProfesional_servicio = id_user WHERE active_user=1 AND id_servicio='$idServicio' ORDER BY name_user ASC ");

}else{
    $ObtenerProfesionales = $conexion -> query("SELECT * FROM users WHERE active_user=1 ORDER BY name_user ASC ");

}



$result = $ObtenerProfesionales->fetchAll(\PDO::FETCH_ASSOC);
print_r (json_encode($result));

?>
