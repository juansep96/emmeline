<?php

require_once '../PDO.php';

$idTurno = $_GET['idTurno'];


$ObtenerDetallesTurno = $conexion -> prepare("SELECT * from turnos LEFT JOIN sucursales ON id_sucursal = idSucursal_turno LEFT JOIN clientes ON idCliente_turno=id_cliente LEFT JOIN servicios ON id_servicio=idServicio_turno WHERE id_turno = :1");
$ObtenerDetallesTurno -> bindParam(':1',$idTurno);
$ObtenerDetallesTurno -> execute();

foreach($ObtenerDetallesTurno as $Detalle){
    $fechaTurno = date("d/m/Y", strtotime($Detalle['fecha_turno']));
    $horaTurno = $Detalle['hora_turno'];
    $servicio = $Detalle['nombre_servicio'];
    $nombreCliente = $Detalle['nombre_cliente']." ".$Detalle['apellido_cliente'];
    $dniCliente = $Detalle['dni_cliente'];
    $telefonoCliente = $Detalle['telefono_cliente'];
    $nombreSucursal = strtoupper($Detalle['nombre_sucursal']);
    $direccionSucursal = strtoupper($Detalle['direccion_sucursal']);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!--Import Google Icon Font-->
    <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jq-3.3.1/dt-1.10.21/r-2.2.5/sc-2.0.2/sl-1.3.1/datatables.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jq-3.3.1/dt-1.10.21/r-2.2.5/sc-2.0.2/sl-1.3.1/datatables.min.js"></script>

    <!--Import materialize.css-->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//momentjs.com/downloads/moment.min.js"></script> <!--Moments Library-->
    <script src="https://kit.fontawesome.com/733c744223.js" crossorigin="anonymous"></script>
    <script src="cute-alert.js"></script>
    <link rel="stylesheet" href="style2.css">

    <!--Let browser know website is optimized for mobile-->
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="utf-8"/>
    <title>IMPRIMIR COMPROBANTE - EMMELINE NAILS </title>
  </head>
<body onload="window.print();">
    <div class="container">
        <div class="col-6 text-center">
		    <img style="width:15%" src="../../img/logo.png"></br></br>
			<p style="font-size:1.2em;"><?php echo $nombreSucursal;?></p>
			<p style="font-size:1.2em;"><?php echo $direccionSucursal;?></p>
			<p style="font-size:1.2em;">www.emmelinenails.com.ar</p>
        </div>
        =======================================================
        <div class="col-6 text-center">
        <h3 class="m-3">DATOS DEL CLIENTE</h3>
            <p>Nombre: <?php echo $nombreCliente;?></p>
            <p>DNI: <?php echo $dniCliente;?></p>
            <p>Telefono: <?php echo $telefonoCliente;?></p>
        </div>
        =======================================================
        <div class="col-6 text-center">
            <h3 class="m-3">DATOS DEL CURSO</h3>
            <h5><?php echo $servicio;?></h5>
            <h5>Fecha: <?php echo $fechaTurno;?> </h5>
            <h5>Hora: <?php echo $horaTurno;?></h5>
        </div>
        =======================================================
<div class="col-6 text-center mb-5 pb-5 mt-2">
            <p>En caso de no poder asistir a la clase, debe dar aviso hasta 24hs antes, caso contario se perderá el pago del curso. </p>
            <h4>NO CONCURRUR CON ACOMPAÑANTE.</h4>
        </div>

    </div>
</body>

</html>
