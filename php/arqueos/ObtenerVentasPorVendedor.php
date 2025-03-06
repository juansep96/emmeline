<?php

require_once "../PDO.php";

$fechaIni = $_POST['desde']." 00:00:00";
$fechaFin = $_POST['hasta']." 23:59:59";
$dniVendedor = $_POST['idVendedor_venta'];
$idSucursal = $_POST['idSucursal'];

$ObtenerVentas = $conexion -> prepare("SELECT id_venta,fechaHora_venta,totalVenta_venta FROM ventas WHERE (fechaHora_venta BETWEEN :1 AND :2) AND estado_venta=1 AND idVendedor_venta=:3");
$ObtenerVentas -> bindParam(':1',$fechaIni);
$ObtenerVentas -> bindParam(':2',$fechaFin);
$ObtenerVentas -> bindParam(':3',$dniVendedor);
$ObtenerVentas -> execute();

$result = $ObtenerVentas->fetchAll(\PDO::FETCH_ASSOC);
print_r (json_encode($result));


?>