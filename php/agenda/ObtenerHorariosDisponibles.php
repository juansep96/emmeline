<?php

require_once "../PDO.php";

$idServicio = $_POST['servicio'];
$fecha = $_POST['fecha'];
$idSucursal = $_SESSION['idSucursal'];

$ObtenerDatosServicio = $conexion -> prepare("SELECT * from servicios WHERE id_servicio=:1");
$ObtenerDatosServicio -> bindParam(':1',$idServicio);
$ObtenerDatosServicio -> execute();
foreach($ObtenerDatosServicio as $Data){
    $horaInicial = $Data['horaInicio_servicio'];
    $segundos_horaInicial=strtotime($horaInicial);

    $tiempo_por_turno=(int)$Data['tiempo_servicio'];
    $segundos_minutoAnadir=$tiempo_por_turno*60;

    $segundos_horaInicial=$segundos_horaInicial-$segundos_minutoAnadir;
    $hora_inicial=date("H:i",$segundos_horaInicial);

    $horaFinal=$Data['horaFin_servicio'];
    $segundos_horafin=strtotime($horaFinal);
    $segundos_horafin=$segundos_horafin-$segundos_minutoAnadir;
    $idProfesional = $Data['idProfesional_servicio'];
}

//Primero guardo en un array los posibles horarios
$arrayPosibles = [];


for ($i=$segundos_horaInicial;$i<$segundos_horafin;){
    $i=$i+$segundos_minutoAnadir;
    $posible=date("H:i",$i);
    array_push($arrayPosibles,$posible);
}


//Luego guardo en otro array los horarios ya ocupados
$arrayOcupados = [];


$ObtenerHorariosOcupados = $conexion -> prepare("SELECT * from turnos  WHERE idProfesional_turno=:1 AND fecha_turno=:2 AND idSucursal_turno=:3");
$ObtenerHorariosOcupados -> bindParam(':1',$idProfesional);
$ObtenerHorariosOcupados -> bindParam(':2',$fecha);
$ObtenerHorariosOcupados -> bindParam(':3',$idSucursal);
$ObtenerHorariosOcupados -> execute();


foreach($ObtenerHorariosOcupados as $HorarioOcupado){
    $hora = date('H:i',strtotime($HorarioOcupado['hora_turno']));
    array_push($arrayOcupados,$hora);
}

//Busco que valor NO se encuentra en ambos arrays  y los agrego a un nuevo array disponibles
$arrayDisponibles = [];

foreach($arrayPosibles as $Posible){
    if(!in_array($Posible,$arrayOcupados)){
        array_push($arrayDisponibles,$Posible);
    }
}


print_r (json_encode($arrayDisponibles)); 
//No hace validacion de turnos, por lo tanto mostramos los horarios posibles siempre.


?>