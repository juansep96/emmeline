<?php

require_once "../PDO.php";

$conn=$conexion;
$fecha = $_GET['fecha'].' 00:00:00';
$fechaHasta = $_GET['fechaHasta'].' 23:59:59';
$idSucursal = $_GET['idSucursal'];

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = ''; // Search value

$searchArray = array();

## Search
$searchQuery = " ";
if($searchValue != ''){
    $searchQuery = " AND (id_comision LIKE :name ) ";
    $searchArray = array(
        'name'=>"%$searchValue%"
   );
}

## Search
$searchQuery = "";
## Total number of records without filtering
$stmt = $conn->prepare("SELECT COUNT(*) AS allcount FROM ventas LEFT JOIN users ON users.DNI_user=ventas.idVendedor_venta WHERE estado_venta=1  AND (fechaHora_venta BETWEEN '$fecha' AND '$fechaHasta') AND (idSucursal_user = '$idSucursal') GROUP BY idVendedor_venta");
$stmt->execute();
$records = $stmt->fetch();
$totalRecords = $records['allcount'];

## Total number of records with filtering
$stmt = $conn->prepare("SELECT COUNT(*) AS allcount FROM ventas LEFT JOIN users ON users.DNI_user=ventas.idVendedor_venta WHERE estado_venta=1  AND (fechaHora_venta BETWEEN '$fecha' AND '$fechaHasta') AND idSucursal_user = '$idSucursal' GROUP BY idVendedor_venta");
$stmt->execute($searchArray);
$records = $stmt->fetch();
$totalRecordwithFilter = $records['allcount'];

## Fetch records
if($_SESSION['nombreRol']=='ADMIN'){
    $stmt = $conn->prepare("SELECT SUM(totalVenta_venta div 2) as 'total',users.name_user as 'profesional',COUNT(*) as 'cantidad',ventas.idVendedor_venta as 'DNI' FROM ventas LEFT JOIN users ON users.DNI_user=ventas.idVendedor_venta WHERE estado_venta=1  AND (fechaHora_venta BETWEEN '$fecha' AND '$fechaHasta') AND idSucursal_user = '$idSucursal' GROUP BY profesional ORDER BY profesional ASC");
}else{
    $DNIUsuario = $_SESSION['DNIUser'];
    $stmt = $conn->prepare("SELECT SUM(totalVenta_venta div 2) as 'total',users.name_user as 'profesional',COUNT(*) as 'cantidad',ventas.idVendedor_venta as 'DNI' FROM ventas LEFT JOIN users ON users.DNI_user=ventas.idVendedor_venta WHERE estado_venta=1  AND (fechaHora_venta BETWEEN '$fecha' AND '$fechaHasta') AND idVendedor_venta = '$DNIUsuario' AND idSucursal_user = '$idSucursal' GROUP BY profesional ORDER BY profesional ASC");
}

// Bind values
foreach($searchArray as $key=>$search){
   $stmt->bindValue(':'.$key, $search,PDO::PARAM_STR);
}

$stmt->execute();
$empRecords = $stmt->fetchAll();

$data = array();

foreach($empRecords as $row){
  $idProfesional = $row['DNI'];
  $acciones = '<div class="d-flex align-items-center gap-3 fs-6"><a href="javascript:;" onclick="VerVentas('.$idProfesional.')" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="Ver Ventas" aria-label="Ver"><i class="bi bi-eye-fill"></i></a>';
  $acciones=$acciones ."</div>";
   $data[] = array(
      "profesional"=>strtoupper($row['profesional']),
      "cantidad"=>$row['cantidad'],
      "total"=>"$ ".number_format(floatval($row['total']),2,'.',''),
      "acciones"=>$acciones,
   );
}

## Response
$response = array(
   "draw" => intval($draw),
   "iTotalRecords" => count($empRecords),
   "iTotalDisplayRecords" => count($empRecords),
   "aaData" => $data
);

echo json_encode($response);



?>
