<?php

require_once "../PDO.php";

$conn=$conexion;
$fecha = $_GET['fecha'];
$idServicio = $_GET['idServicio'];
$idSucursal = $_GET['idSucursal'];


## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value

$searchArray = array();

## Search
$searchQuery = " ";
if($searchValue != ''){
    $searchQuery = " AND (nombre_cliente LIKE :name) ";
    $searchArray = array(
        'name'=>"%$searchValue%"
   );
}

if($idServicio=="0"){ //Todos los servicios

  ## Total number of records without filtering
  $stmt = $conn->prepare("SELECT COUNT(*) AS allcount FROM turnos WHERE (fecha_turno = '$fecha') AND idSucursal_turno='$idSucursal'");
  $stmt->execute();
  $records = $stmt->fetch();
  $totalRecords = $records['allcount'];

  ## Total number of records with filtering
  $stmt = $conn->prepare("SELECT COUNT(*) AS allcount FROM turnos WHERE 1 ".$searchQuery ." AND (fecha_turno = '$fecha') AND idSucursal_turno='$idSucursal'");
  $stmt->execute($searchArray);
  $records = $stmt->fetch();
  $totalRecordwithFilter = $records['allcount'];

  ## Fetch records
  $stmt = $conn->prepare("SELECT * FROM turnos LEFT JOIN clientes ON id_cliente=idCliente_turno LEFT JOIN users ON id_user=idProfesional_turno LEFT JOIN servicios ON id_servicio=idServicio_turno WHERE 1 ".$searchQuery." AND (fecha_turno = '$fecha') AND idSucursal_turno='$idSucursal' ORDER BY ".$columnName." ".$columnSortOrder." LIMIT :limit,:offset");

  // Bind values
  foreach($searchArray as $key=>$search){
    $stmt->bindValue(':'.$key, $search,PDO::PARAM_STR);
  }

  $stmt->bindValue(':limit', (int)$row, PDO::PARAM_INT);
  $stmt->bindValue(':offset', (int)$rowperpage, PDO::PARAM_INT);
  $stmt->execute();
  $empRecords = $stmt->fetchAll();
}else{ //X servicio
  ## Total number of records without filtering
  $stmt = $conn->prepare("SELECT COUNT(*) AS allcount FROM turnos WHERE (fecha_turno = '$fecha') AND (idServicio_turno = '$idServicio')");
  $stmt->execute();
  $records = $stmt->fetch();
  $totalRecords = $records['allcount'];

  ## Total number of records with filtering
  $stmt = $conn->prepare("SELECT COUNT(*) AS allcount FROM turnos WHERE 1 ".$searchQuery ." AND (fecha_turno = '$fecha') AND (idServicio_turno = '$idServicio')");
  $stmt->execute($searchArray);
  $records = $stmt->fetch();
  $totalRecordwithFilter = $records['allcount'];

  ## Fetch records
  $stmt = $conn->prepare("SELECT * FROM turnos LEFT JOIN clientes ON id_cliente=idCliente_turno LEFT JOIN users ON id_user=idProfesional_turno LEFT JOIN servicios ON id_servicio=idServicio_turno WHERE 1 ".$searchQuery." AND (fecha_turno = '$fecha') AND (idServicio_turno = '$idServicio') ORDER BY ".$columnName." ".$columnSortOrder." LIMIT :limit,:offset");

  // Bind values
  foreach($searchArray as $key=>$search){
    $stmt->bindValue(':'.$key, $search,PDO::PARAM_STR);
  }

  $stmt->bindValue(':limit', (int)$row, PDO::PARAM_INT);
  $stmt->bindValue(':offset', (int)$rowperpage, PDO::PARAM_INT);
  $stmt->execute();
  $empRecords = $stmt->fetchAll();
}


$data = array();

foreach($empRecords as $row){  
  $hora = date('H:i',strtotime($row['hora_turno']));
  $idTurno = $row['id_turno'];
  $acciones = '<div class="d-flex align-items-center gap-3 fs-6"><a href="javascript:;" onclick="EditarTurno('.$idTurno.')" class="text-primary eliminar" data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="Editar Estado" aria-label="Ver"><i class="bx bx-edit"></i></a>';
  
  if($_SESSION['nombreRol']=="ADMIN"){
    $acciones = $acciones .'<a href="javascript:;" onclick="EliminarTurno('.$idTurno.')" class="text-danger eliminar" data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="Eliminar Arqueo" aria-label="Eliminar"><i class="bi bi-trash-fill"></i></a>';  
  }
  
  $acciones=$acciones ."</div>";

  if($row['estado_turno']=="CONFIRMADO"){
    $estado='<span class="badge bg-light-warning text-warning w-100">CONFIRMADO</span>';
  }

  if($row['estado_turno']=="ASISTIO"){
    $estado='<span class="badge bg-light-success text-success w-100">ASISTIÓ</span>';
  }

  if($row['estado_turno']=="CANCELADO"){
    $estado='<span class="badge bg-light-danger text-danger w-100">CANCELADO</span>';
  }

  if($row['estado_turno']=="AUSENTE"){
    $estado='<span class="badge bg-light-danger text-danger w-100">AUSENTE</span>';
  }

   $data[] = array(
      "hora_turno"=>$hora,
      "nombre_servicio"=>strtoupper($row['nombre_servicio']),
      "nombre_cliente"=>strtoupper($row['apellido_cliente']." ".$row['nombre_cliente']),
      "name_user"=>strtoupper($row['name_user']),
      "obs_turno"=>$row['obs_turno'],
      "estado_turno"=>$estado,
      "acciones_turno"=>$acciones,
   );
}

## Response
$response = array(
   "draw" => intval($draw),
   "iTotalRecords" => $totalRecords,
   "iTotalDisplayRecords" => $totalRecordwithFilter,
   "aaData" => $data
);

echo json_encode($response);



?>
