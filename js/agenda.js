var urlBase="./php/agenda/";

const ObtenerSucursalesSelect = async () => {
  $.post("./php/arqueos/ObtenerSucursalesSelect.php")
  .then(async (data)=>{
    data = JSON.parse(data);
    await data.forEach((e)=>{
      opcion = "<option value='"+e.id_sucursal+"'>"+e.nombre_sucursal.toUpperCase()+"</option>";
      $("#idSucursal").append(opcion);
    })
    CargarServicios();
  })
}



const ObtenerTurnosFacheros = async () => {
    //Como refrescamos, aprovechamos y refescamos servicios tambien
    $("#servicios").empty();
    $("#servicios_2").empty();
    idSucursal = $("#idSucursal").val();
    var opcion = "<option value='0'>TODOS</option>";
                    $("#servicios").append(opcion);
    $.post(urlBase+"ObtenerServiciosSelect.php",{idSucursal})
    .then((data)=>{
        data=JSON.parse(data);
        data.forEach((e)=>{
            opcion = "<option value='"+e.id_servicio+"'>"+e.nombre_servicio.toUpperCase()+"</option>";
            $("#servicios").append(opcion);
            $("#servicios_2").append(opcion);
        })
    })
    fecha = $("#fecha").val();
    idServicio = $("#servicios").val();
    idSucursal = $("#idSucursal").val();
    calendarEl = document.getElementById('calendar');
    $.post(urlBase+"ObtenerTurnosFacheros.php",{fecha,idServicio,idSucursal})
        .then(async (data)=>{
            array = [];
            data = JSON.parse(data);
            var clases;
            await data.forEach((e)=> {
                title = '';
                start = e.fecha_turno + 'T' + e.hora_turno;
                end = moment(e.fecha_turno + ' ' + e.hora_turno).add(e.tiempo_servicio, 'm');
                end = moment(end).format('YYYY-MM-DD') + 'T' + moment(end).format('HH:mm:ss');
                e.apellido_cliente ? title = title + e.apellido_cliente.toUpperCase() + ' ' : '';
                e.nombre_cliente ? title = title + e.nombre_cliente.toUpperCase() + ' ' : '';
                e.nombre_servicio ? title = title + e.nombre_servicio.toUpperCase() + ' ' : '';
                clases = [];
                e.nombre_servicio ? e.nombre_servicio = e.nombre_servicio.toLowerCase() : '';
                if(e.nombre_servicio.includes('pesta')){
                    clases.push('pesta');
                }
                if(e.nombre_servicio.includes('capp')){
                    clases.push('capp');
                }
                if(e.nombre_servicio.includes('escul')){
                    clases.push('escul');
                }
                if(e.nombre_servicio.includes('mani')){
                    clases.push('mani');
                }
                if(e.nombre_servicio.includes('pies')){
                    clases.push('pies');
                }
                if(e.nombre_servicio.includes('semi')){
                    clases.push('semi');
                }
                if(e.nombre_servicio.includes('remo')){
                    clases.push('remo');
                }
                if(e.nombre_servicio.includes('soft')){
                    clases.push('soft');
                }
                if(e.nombre_servicio.includes('reti')){
                    clases.push('reti');
                }           
                
                obj = {
                    title,
                    start,
                    end,
                    description : title,
                    classNames : clases,
                    servicio : e.nombre_servicio.toUpperCase(),
                    profesional : e.name_user.toUpperCase(),
                    idTurno : e.id_turno,
                    dni : e.dni_cliente,
                };
                array.push(obj);
                console.log(obj);
            })
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                initialDate: moment().format('YYYY-MM-DD'),
                headerToolbar: {
                    left: 'prev,next hoy',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                views: {
                    dayGridMonth: { // name of view
                      titleFormat: { year: 'numeric', month: '2-digit', day: '2-digit' },
                      editable: true
                    },
                    timeGridWeek: {
                        editable: true
                    }
                },
                events : array,
                eventClick:  function(event, jsEvent, view) {
                    $('#modalTitle').html(event.event.title);
                    data = event.event.extendedProps;
                    $('#desdeMostrar').html(moment(event.event.start).format('DD/MM/YYYY HH:mm'));
                    $('#hastaMostrar').html(moment(event.event.end).format('DD/MM/YYYY HH:mm'));
                    $('#servicioMostrar').html(data.servicio);
                    $('#profesionalMostrar').html(data.profesional);
                    div = '';
                    if (data.dni == 0 ){
                        div = '<div class="alert alert-danger" role="alert" >Debe actualizar los datos del cliente</div>';
                        $("#errorMensaje").html(div);
                    } 
                    botones = '<a role="button" onclick="EditarTurno('+data.idTurno+')" class="btn btn-default">Editar</a>' + '<a role="button"  onclick="EliminarTurno('+data.idTurno+')" class="btn btn-default">Eliminar</a>';
                    $("#acciones").html(botones);
                    $('#calendarModal').modal('show');
                },
            });      
            calendar.render();
            calendar.setOption('locale', 'es');
        })
    
}


$(document).ready(function() {
    

  var fecha = moment().format("YYYY-MM-DD");
  $("#fecha").val(fecha);
  $("#fechaNuevoTurno").val(fecha);

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
  CargarServicios();
  CargarProfesionales();

});

function CargarServicios(){
    $("#servicios_2").empty();
    idSucursal = $("#idSucursal").val();
    $.post(urlBase+"ObtenerServiciosSelect.php",{idSucursal})
    .then((data)=>{
        data=JSON.parse(data);
        data.forEach((e)=>{
            var opcion = "<option value='"+e.id_servicio+"'>"+e.nombre_servicio.toUpperCase()+"</option>";
            $("#servicios").append(opcion);
            $("#servicios_2").append(opcion);
        })
    })
    setTimeout(() =>{
       CargarTurnos();
       ObtenerTurnosFacheros();
    },1500);        
}

function CargarProfesionales(idServicio = null){
    if(idServicio){
        $("#profesionales").empty();
        $("#profesionales_edit").empty();
        $.post(urlBase+"ObtenerProfesionalesSelect.php",{idServicio})
        .then((data)=>{
            data=JSON.parse(data);
            data.forEach((e)=>{
                var opcion = "<option value='"+e.id_user+"'>"+e.name_user.toUpperCase()+"</option>";
                $("#profesionales").append(opcion);
                $("#profesionales_edit").append(opcion);
            })
        })
    }else{
        $("#profesionales").empty();
        $("#profesionales_edit").empty();
        $.post(urlBase+"ObtenerProfesionalesSelect.php")
        .then((data)=>{
            data=JSON.parse(data);
            var opcion = "<option disabled value='0'>Elija Profesional</option>";
            $("#profesionales").append(opcion);
            data.forEach((e)=>{
                var opcion = "<option value='"+e.id_user+"'>"+e.name_user.toUpperCase()+"</option>";
                $("#profesionales").append(opcion);
                $("#profesionales_edit").append(opcion);
    
            })
            $("#profesionales").val("0");
        })
    }
   
}

function CargarTurnos(){
    fecha = $("#fecha").val();
    idServicio = $("#servicios").val();
    idSucursal = $("#idSucursal").val();
    $("#turnos").DataTable().destroy();
    $('#turnos').DataTable({
      responsive: false,
      'processing': true,
      'serverSide': true,
      'serverMethod': 'post',
      'searching':false,
      'ajax': {
        'url': urlBase+"ObtenerTurnos.php?fecha="+fecha+"&idServicio="+idServicio+"&idSucursal="+idSucursal,
      },
      'columns': [{
          data: 'hora_turno'
        },
        {
          data: 'nombre_servicio'
        },
        {
          data: 'nombre_cliente'
        },
        {
          data: 'name_user'
        },
        {
          data: 'estado_turno'
        },
        {
          data: 'obs_turno'
        },
        {
          data: 'acciones_turno'
        }
      ]
    });
}

function EliminarTurno(idTurno){
    $("#calendarModal").modal('hide');
    Lobibox.confirm({
        msg: "Seguro  que desea eliminar este Turno?",
        callback: function ($this, type, ev) {
          if(type=="yes"){
            $.post(urlBase+"EliminarTurno.php",{idTurno})
            .then(()=>{
              Lobibox.notify('success', {
              pauseDelayOnHover: true,
              continueDelayOnInactiveTab: false,
              position: 'top right',
              icon: 'bx bx-check-circle',
              msg: 'Turno eliminado con éxito.',
            });
            table =   $("#turnos").DataTable();
            let info = table.page.info();
            let page = info.page;
            table.ajax.reload();
            table.page( page ).draw( false );
            });
          }else{
            Lobibox.notify('warning', {
              pauseDelayOnHover: true,
              continueDelayOnInactiveTab: false,
              position: 'top right',
              icon: 'bx bx-message-error',
              msg: 'Acción cancelada.',
            });
          }
      }
      });
}

function EditarTurno(idTurno){
  CargarProfesionales();
  $.post(urlBase+"ObtenerTurno.php",{idTurno})
  .then((data)=>{
    data=JSON.parse(data);
    data.forEach((e)=>{
      $("#idTurno").val(e.id_turno);
      $("#estado_edit").val(e.estado_turno);
      $("#profesionales_edit").val(e.idProfesional_turno);
    })
    $("#modalEditarTurno").modal('show');
    $("#calendarModal").modal('hide');
  })
}

function CerrarEditarTurno(){
  $("#modalEditarTurno").modal('hide');
}

function ActualizarTurno(){
  idTurno=$("#idTurno").val();
  idProfesional=$("#profesionales_edit").val();
  estadoTurno = $("#estado_edit").val();
  var datos = [
    idTurno,
    idProfesional,
    estadoTurno
  ]
  datos = JSON.stringify(datos);
  $.post(urlBase+"ActualizarTurno.php",{datos})
  .then(()=>{
    CerrarEditarTurno();
    $("#modalTurnoActualizado").modal('show');
  })
}

function CerrarModal(){
  $("#modalTurnoActualizado").modal('hide');
  CargarTurnos();
}

function AgendarCita(){
    $("#dni").val("");
    $("#idCliente").val("-1");
    $("#idCiudad").val("-1");
    $("#sms").val("0");
    idServicio = $("#servicios_2").val();
    CargarHorariosDisponibles();
    VaciarCampos();
    DeshabilitarCampos();
    CargarProfesionales(idServicio);
    $(".datosCliente").prop('hidden',true);
    $("#modalNuevoTurno").modal('show');
}


function CerrarAgendarCita(){
    $("#modalNuevoTurno").modal('hide');
}

function CargarHorariosDisponibles(){
    $(".datosCliente").prop('hidden',true);
    idServicio = $("#servicios_2").val();
    CargarProfesionales(idServicio);
    let servicio = $("#servicios_2").val();
    let fecha = $("#fechaNuevoTurno").val();
    $("#horariosNuevoTurno").empty();
    $.post(urlBase+"ObtenerHorariosDisponibles.php",{servicio,fecha})
    .then((data)=>{
        data = JSON.parse(data);
        data.forEach((e)=>{
            var opcion = "<option value='"+e+"'>"+e+"</option>";
            $("#horariosNuevoTurno").append(opcion);
        })
        var opcion = "<option selected disabled value='0'>Seleccione un Horario</option>";
        $("#horariosNuevoTurno").prepend(opcion);
    })
}

function PedirDatosCliente(){
    $(".datosCliente").prop('hidden',false);
}

function BuscarCliente(){
   
    dniCliente = $("#dni").val();
    if(dniCliente == 0){
        $("#idCliente").val("-1");
        $("#idCiudad").val("-1");
        HabilitarCampos();
        VaciarCampos();
    }else{
        $('#modalCargando').modal({backdrop: 'static', keyboard: false})
        $("#modalCargando").modal('show');
        $.post(urlBase+"BuscarCliente.php",{dniCliente})
        .then((data)=>{
            setTimeout(() =>{
            if(data!="NO"){
                data=JSON.parse(data);
                console.log("Encontrado");
                data.forEach((e)=>{
                    $("#idCliente").val(e.id_cliente);
                    $("#nombre").val(e.nombre_cliente.toUpperCase());
                    $("#apellido").val(e.apellido_cliente.toUpperCase());
                    $("#telefono").val(e.telefono_cliente);
                    $("#mail").val(e.mail_cliente);
                    $("#fnac").val(e.fnac_cliente);
                    $("#idCiudad").val(e.idCiudad_cliente);
                    HabilitarCampos();
                })
            }else{
                $("#idCliente").val("-1");
                $("#idCiudad").val("-1");
                HabilitarCampos();
                VaciarCampos();
            }
            $("#modalCargando").modal('hide');
            },1500);        
        })
    }
    
}

function HabilitarCampos(){
    $("#nombre").prop('readonly',false);
    $("#apellido").prop('readonly',false);
    $("#telefono").prop('readonly',false);
    $("#mail").prop('readonly',false);
    $("#fnac").prop('readonly',false);
    $("#obs").prop('readonly',false);
}

function DeshabilitarCampos(){
    $("#nombre").prop('readonly',true);
    $("#apellido").prop('readonly',true);
    $("#telefono").prop('readonly',true);
    $("#mail").prop('readonly',true);
    $("#fnac").prop('readonly',true);
    $("#obs").prop('readonly',true);
}

function VaciarCampos(){
    $("#nombre").val("");
    $("#apellido").val("");
    $("#telefono").val("");
    $("#mail").val("");
    $("#fnac").val("");
    $("#obs").val("");
}

function GuardarTurno(){
    idCliente = $("#idCliente").val();
    idServicio = $("#servicios_2").val();
    fecha = $("#fechaNuevoTurno").val();
    hora = $("#horariosNuevoTurno").val();
    nombre = $("#nombre").val();
    apellido = $("#apellido").val();
    dni = $("#dni").val();
    telefono=$("#telefono").val();
    mail=$("#mail").val();
    fnac = $("#fnac").val();
    ciudad = $("#idCiudad").val();
    obs = $("#obs").val();
    estado = "CONFIRMADO";
    sms = $("#sms").val();
    idProfesional = $("#profesionales").val();
    if(apellido && nombre && telefono && idProfesional){
        Lobibox.confirm({
            msg: "Confirma el Turno?",
            callback: function ($this, type, ev) {
              if(type=="yes"){
                $("#modalGuardando").modal('show');
                if(idCliente=="-1"){ //Nuevo Cliente
                    var datos = [
                        nombre,
                        apellido,
                        dni,
                        telefono,
                        mail,
                        fnac,
                        ciudad,
                    ];
                    url = urlBase+"NuevoCliente.php";
                 }else{ //Actualizo Cliente
                    var datos = [
                        nombre,
                        apellido,
                        dni,
                        telefono,
                        mail,
                        fnac,
                        ciudad,
                        idCliente
                    ];
                    url = urlBase+"ActualizarCliente.php";
                 }
                 datos = JSON.stringify(datos);
                 $.post(url,{datos})
                 .then((idCliente)=>{
                    var datosTurno = [
                        fecha,
                        idCliente,
                        idServicio,
                        hora,
                        obs,
                        estado,
                        idProfesional,
                        sms
                    ]
                    datosTurno = JSON.stringify(datosTurno);
                    $.post(urlBase+"GuardarTurno.php",{datosTurno})
                    .then((idTurno)=>{
                        CerrarAgendarCita();
                        ObtenerTurnosFacheros();
                        window.open(urlBase+'ImprimirComprobante.php?idTurno='+idTurno, '_blank');
                    })
                 })
                setTimeout(() =>{
                    $("#modalGuardando").modal('hide');
                },3000);   
              }else{
                Lobibox.notify('warning', {
                  pauseDelayOnHover: true,
                  continueDelayOnInactiveTab: false,
                  position: 'top right',
                  icon: 'bx bx-message-error',
                  msg: 'Acción cancelada.',
                });
              }
          }
          });
    }else{
        Lobibox.notify('error', {
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'top right',
            icon: 'bx bx-message-error',
            msg: 'Faltan campos por completar.',
          });
    }
}