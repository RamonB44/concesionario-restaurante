@extends('adminlte::page')

@section('title', 'Empleados')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-start">
                    <h2>Empleados</h2>
                    <button class="btn btn-primary m-1" onclick="create()" >Agregar Empleados</button>
                    <button class="btn btn-secondary m-1" onclick="printBarcode()" >Print Barcode</button>
                    <form action="{{ route('employes.import') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="file" name="file_xlsx" id="file_xlsx" class="form-control" required>
                        <button type="submit" class="btn btn-success m-1" >Importar Empleados</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="manageTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Codigo</th>
                                    <th>Codigo de validacion</th>
                                    <th>Documento N°</th>
                                    <th>Nombres y Apellidos</th>
                                    <th>Area</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>

                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" id="createModal">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
          <h4 class="modal-title">Nuevo Empleado</h4>
        </div>
        <form id="formcreate" action="{{ route('employes.create')}}" method="post">
            {{ csrf_field() }}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">
                        <small>Codigo</small>
                        <input type="number" class="form-control" id="code" name="code" placeholder="Codigo" required>
                    </div>
                    <div class="col-md-4">
                        <small>Numero de Documento</small>
                        <input type="number" class="form-control" id="docnum" name="docnum" placeholder="Documento" required>
                    </div>
                    <div class="col-md-6">
                        <small>Nombres y Apellidos</small>
                        <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Nombres" required>
                    </div>
                </div>
            </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" >Guardar</button>
        </form>
        <button type="button" class="btn btn-default" id="btnclosemodal" data-dismiss="modal">Cerrar</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" id="editModal">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
          <h4 class="modal-title">Editar Empleado</h4>
        </div>
        <form id="formupdate" method="post">
            {{ csrf_field() }}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">
                        <small>Codigo</small>
                        <input type="number" class="form-control" id="code-edit" name="code" placeholder="Codigo" required>
                    </div>
                    <div class="col-md-4">
                        <small>Numero de Documento</small>
                        <input type="number" class="form-control" id="docnum-edit" name="docnum" placeholder="Documento" required>
                    </div>
                    <div class="col-md-6">
                        <small>Nombres y Apellidos</small>
                        <input type="text" class="form-control" id="name-edit" name="fullname" placeholder="Nombres" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="btnSend" >Guardar</button>
        </form>
        <button type="button" class="btn btn-default" id="btnclosemodal" data-dismiss="modal">Cerrar</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@section('plugins.Datatables', true)
@section('js')
<script>
var manageTable = null;
var url = "{{ url('/') }}";
$(document).ready(function(){
    $('#manageTable thead tr').clone(true).appendTo( '#manageTable thead' );

    $('#manageTable thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<input type="text" class="form-control" placeholder="Buscar '+title+'" />' );

        $( 'input', this ).on( 'keyup change', function () {
            if ( manageTable.column(i).search() !== this.value ) {
                manageTable
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
    } );

    manageTable = $('#manageTable').DataTable({
        orderCellsTop: true,
        fixedHeader: true,
        bLengthChange: false,
        bVisible: false,
        columnDefs: [ {
            visible: false,
            targets: null,
            defaultContent: '',
            orderable: false,
            className: 'select-checkbox'
        } ],
        select: {
            style:    'os',
            selector: 'td:first-child'
        },
        ajax: {
            type : 'get',
            url: '{{ route("employes.getTable") }}',
            beforeSend: function () {
                $('.progress').show();
            },
            complete: function () {
                // $('.progress').hide();
            },
        },
    });
});
function eliminar(id){
        if (confirm('Estas seguro de eliminar este registro?')) {
            // Deleted it!
            $.ajax({
                url: url+'/employes/delete/'+id,
                type: 'GET',
                success:function(response){
                    console.log(response);
                    if(response){
                        alert('eliminado correctamente :)');
                        manageTable.ajax.reload( null, false );
                    }
                },
                error:function(response){
                    console.log(response);
                }
            });
        } else {
            // Do nothing!
        }

}

function create(){
    //set inputs in void
    $('#createModal').modal('show');
}

function edit(id){
    $.ajax({
        url: url+'/employes/update/'+id,
        type: 'GET',
        success: function(response){
            console.log(response);
            $('#formupdate').attr('action', url+'/employes/update/'+id );
            $('#code-edit').val(response.data.code);
            $('#name-edit').val(response.data.fullname);
            $('#docnum-edit').val(response.data.doc_num);
            $('#editModal').modal('show');

        },
        error: function(ex){
            console.log(ex);
        }
    });

}

function printBarcode(){
    var ids = $.map(manageTable.rows('.selected').data(), function (item) {
        return item[1]+item[2];
    });

    console.log(ids)
    // alert(manageTable.rows('.selected').data().length + ' row(s) selected');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var r = confirm("Estas seguro de generar los codigos");
    if (r == true) {
        $.ajax({
            url: '{{ route("employes.barcode") }}',
            type: 'POST',
            data: {ids},
            dataType: 'json',
            success:function(response){
                console.log(response);
            },
            error:function(response){
                console.log(response);
            }
        });
    } else {
    }
}

function generateNew(id){
    if (confirm('Estas seguro de generar un nuevo codigo de validacion?')) {

            $.ajax({
                url: url+'/employes/generateCode/'+id,
                type: 'GET',
                success:function(response){
                    console.log(response);
                    if(response){
                        alert('generado correctamente :)');
                        manageTable.ajax.reload( null, false );
                    }
                },
                error:function(response){
                    console.log(response);
                }
            });
        } else {
            // Do nothing!
        }
}

function importxlsx(){

    if (confirm('Los campos seran actualizados si se encuentran concidencias en los codigos de empleados,¿Estas seguro de importar los empleados?')) {

    }
}
</script>
@endsection
