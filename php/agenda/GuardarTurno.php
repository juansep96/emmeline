<?php

require_once('../PDO.php');

$datos=$_POST['datosTurno'];
$datos=json_decode($datos);
$idSucursal = $_SESSION['idSucursal'];

$GuardarTurno=$conexion->prepare("INSERT INTO turnos (fecha_turno,idCliente_turno,idServicio_turno,hora_turno,obs_turno,estado_turno,idProfesional_turno,idSucursal_turno) VALUES (:1,:2,:3,:4,:5,:6,:7,:8)");
$GuardarTurno->bindParam(':1',$datos[0]);
$GuardarTurno->bindParam(':2',$datos[1]);
$GuardarTurno->bindParam(':3',$datos[2]);
$GuardarTurno->bindParam(':4',$datos[3]);
$GuardarTurno->bindParam(':5',$datos[4]);
$GuardarTurno->bindParam(':6',$datos[5]);
$GuardarTurno->bindParam(':7',$datos[6]);
$GuardarTurno->bindParam(':8',$idSucursal);
if($GuardarTurno->execute()){
  $idTurno = $conexion -> lastInsertId();

  if($datos[7]=="SI"){ //Enviar recordatorio??
    $ObtenerDatosCliente = $conexion -> prepare("SELECT nombre_cliente,apellido_cliente,telefono_cliente FROM clientes WHERE id_cliente=:1");
    $ObtenerDatosCliente -> bindParam(':1',$datos[1]);
    $ObtenerDatosCliente -> execute();
  
    foreach($ObtenerDatosCliente as $Cliente){
      $telefonoCliente = $Cliente['telefono_cliente'];
      $nombreCliente = $Cliente['nombre_cliente'];
    }
  
    $ObtenerDatosServicio = $conexion -> prepare("SELECT nombre_servicio FROM servicios WHERE id_servicio=:1");
    $ObtenerDatosServicio -> bindParam(':1',$datos[2]);
    $ObtenerDatosServicio -> execute();
  
    foreach($ObtenerDatosServicio as $Servicio){
      $nombreServicio = $Servicio['nombre_servicio'];
    }
  
    $fechaTurno = strtotime($datos[0]);
    $fechaTurno = date("d/m/Y", $fechaTurno);
  
    $horaTurno = $datos[3];
  
    $fechaRecordatorio = strtotime ( '-1 day' , strtotime ( $datos[0] ) ) ;
    $fechaRecordatorio = date("Y-m-d", $fechaRecordatorio);  
  
    $mensaje = "ESTIMADX ".$nombreCliente." SU TURNO DE ".$nombreServicio." EN EMMELINE - LELOIR 931 EL ".$fechaTurno." A LAS  ".$horaTurno." ESTA CONFIRMADO";
    $mensajeRecordatorio = " RECORDATORIO : ESTIMADX ".$nombreCliente." SU TURNO DE ".$nombreServicio." EN EMMELINE - LELOIR 931 ES EL ".$fechaTurno." A LAS  ".$horaTurno;
  
    //Aca vamos a programar los recordatorios via SMS.
  
    EnviarMensaje($telefonoCliente,$mensaje);
    EnviarMensajeProgramado($telefonoCliente,$mensajeRecordatorio,$fechaRecordatorio);  
  }

  echo $idTurno;
  
}else{
    echo "\nPDO::errorInfo():\n";
    print_r($GuardarTurno->errorInfo());
}


function EnviarMensaje($telefono,$mensaje){	
	$smsusuario = "SMSDEMOPLUSC134673"; //usuario de SMS MASIVOS
	$smsclave 	 = "SMSDEMOPLUSC134673489"; //clave de SMS MASIVOS
	$smsrespuesta = file_get_contents("http://servicio.smsmasivos.com.ar/enviar_sms.asp?API=1&TOS=". urlencode($telefono) ."&TEXTO=". urlencode($mensaje) ."&USUARIO=". urlencode($smsusuario) ."&CLAVE=".urlencode($smsclave));
}

function EnviarMensajeProgramado($telefono,$mensaje,$fecha){	
	$smsusuario = "SMSDEMOPLUSC134673"; //usuario de SMS MASIVOS
	$smsclave 	 = "SMSDEMOPLUSC134673489"; //clave de SMS MASIVOS
	$smsrespuesta = file_get_contents("http://servicio.smsmasivos.com.ar/enviar_sms.asp?API=1&TOS=". urlencode($telefono) ."&TEXTO=". urlencode($mensaje) ."&USUARIO=". urlencode($smsusuario) ."&CLAVE=". urlencode($smsclave)."&FECHADESDE=".urlencode($fecha) );
}



?>
