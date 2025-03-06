var urlBase="./php/arqueos/";

const ObtenerSucursalesSelect = async () => {
  $.post("./php/arqueos/ObtenerSucursalesSelect.php")
  .then((data)=>{
    data = JSON.parse(data);
    data.forEach((e)=>{
      opcion = "<option value='"+e.id_sucursal+"'>"+e.nombre_sucursal.toUpperCase()+"</option>";
      $("#idSucursal").append(opcion);
    })
    CargarComisiones();
  })
}

$(document).ready(function() {

  var fecha = moment().format("YYYY-MM-DD");
  $("#desde").val(fecha);
  $("#hasta").val(fecha);

  $.extend(true, $.fn.dataTable.defaults, {
    "language": {
      "decimal": ",",
      "thousands": ".",
      "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
      "infoPostFix": "",
      "infoFiltered": "(filtrado de un total de _MAX_ registros)",
      "loadingRecords": "Cargando...",
      "lengthMenu": "Mostrar _MENU_ registros",
      "paginate": {
        "first": "Primero",
        "last": "Último",
        "next": "Siguiente",
        "previous": "Anterior"
      },
      "processing": "Procesando...",
      "search": "Buscar:",
      "searchPlaceholder": "",
      "zeroRecords": "No se encontraron resultados",
      "emptyTable": "Ningún dato disponible en esta tabla",
      "aria": {
        "sortAscending": ": Activar para ordenar la columna de manera ascendente",
        "sortDescending": ": Activar para ordenar la columna de manera descendente"
      },
      //only works for built-in buttons, not for custom buttons
      "buttons": {
        "create": "Nuevo",
        "edit": "Cambiar",
        "remove": "Borrar",
        "copy": "Copiar",
        "csv": "CSV",
        "excel": "Excel",
        "pdf": "PDF",
        "print": "Imprimir",
        "colvis": "Visibilidad columnas",
        "collection": "Colección",
        "upload": "Seleccione fichero...."
      },
      "select": {
        "rows": {
          _: '%d filas seleccionadas',
          0: 'clic fila para seleccionar',
          1: 'una fila seleccionada'
        }
      }
    }
  });
});

function CargarComisiones(){
    fecha = $("#desde").val();
    fechaHasta=$("#hasta").val();
    idSucursal = $("#idSucursal").val();
    $("#comisiones").DataTable().destroy();
    $('#comisiones').DataTable({
      responsive: false,
      'processing': true,
      'serverSide': true,
      'serverMethod': 'post',
      'searching':false,
      dom: 'Bfrtip',
      buttons: [
          'excel', 'pdf', 'print'
      ],
      'ajax': {
        'url': urlBase+"ObtenerComisiones.php?fecha="+fecha+"&fechaHasta="+fechaHasta+'&idSucursal='+idSucursal,
      },
      'columns': [{
          data: 'profesional'
        },
        {
          data: 'cantidad'
        },
        {
          data: 'total'
        },
        {
          data: 'acciones'}
      ]
    });
}

function VerVentas(idVendedor_venta){
    $(".filaDetalle2").remove();
    desde = $("#desde").val();
    hasta = $("#hasta").val();
    $.post(urlBase+"ObtenerVentasPorVendedor.php",{idVendedor_venta,desde,hasta})
    .then((res)=>{
        if(res){
            res=JSON.parse(res);
            res.forEach((e)=>{
                acciones = '<a href="javascript:;" onclick="VerVenta('+e.id_venta+')" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="Ver Arqueo" aria-label="Ver"><i class="bi bi-eye-fill"></i></a>';
                htmlTags = '<tr class="filaDetalle2">' +
                '<td class="text-center">' + e.id_venta + '</td>'+
                '<td class="text-center">' + moment(e.fechaHora_venta).format('DD/MM/YYYY') + '</td>'+
                '<td class="text-center">' + moment(e.fechaHora_venta).format('HH:mm') + '</td>'+
                '<td class="text-center"> $ '+ parseFloat(e.totalVenta_venta).toFixed(2) +'</td>'+
                '<td class="text-center">' +acciones + '</td>'+
                '</tr>';
                $('#ventas tbody').append(htmlTags);
          });
            $("#modalVerVentas").modal('show');
        }      
    });
}

function CerrarVerVentas(){
    $("#modalVerVentas").modal('hide');
}


function VerVenta(idVenta){
    $(".filaDetalle").remove();
    $(".facturacion").prop('hidden',true);
    $.post("./php/transacciones/ObtenerVenta.php",{idVenta})
    .then((res)=>{
        res=JSON.parse(res);
        $("#tituloVerVenta").html("N° de Venta: 000"+res[0].id_venta);
        total = res[0].totalVenta_venta;
        total = parseInt(total);
        total = total.toFixed(2);
        $("#total").val("$ "+total);

        efectivo = res[0].pagoEfectivo_venta;
        efectivo = parseInt(efectivo);
        efectivo = efectivo.toFixed(2);
        $("#efectivo").val("$ "+efectivo);

        mp = res[0].pagoMP_venta;
        mp = parseInt(mp);
        mp = mp.toFixed(2);
        $("#mp").val("$ "+mp);

        posnet = res[0].pagoPosnet_venta;
        posnet = parseInt(posnet);
        posnet = posnet.toFixed(2);
        $("#posnet").val("$ "+posnet);

        if(res[0].idFactura_venta!="-1"){
            $("#fechaFacturacion").val(res[0].fecha_factura);
            $("#nro").val(res[0].PDV_factura+"-"+res[0].numero_factura);
            $("#cae").val(res[0].CAE_factura);
            $("#vto").val(res[0].fechaVencimiento_factura);
            $(".facturacion").prop('hidden',false);
        }

        res.forEach((e)=>{
            precio = e.precioVenta_ventaDetalle;
            precio = parseInt(precio);
            precio = precio.toFixed(2);
            var htmlTags = '<tr class="filaDetalle">' +
                            '<td>' + e.codigo_producto + '</td>'+
                            '<td>' + e.nombre_producto + '</td>'+
                            '<td> $ ' + precio + '</td>'+
                            '<td>' + e.cantidad_ventaDetalle + '</td>'+
                            '<td>' + e.dto_ventaDetalle + '% </td>'+
                            '</tr>';
            $('#tabla-items tbody').append(htmlTags);
        })
    })
    $("#modalVerVenta").modal('show');
}

function CerrarVerVenta(){
    $("#modalVerVenta").modal('hide');
}