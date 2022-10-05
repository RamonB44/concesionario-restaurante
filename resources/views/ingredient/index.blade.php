@extends('adminlte::page')

@section('title', 'Ingredientes')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-start">
                    <h2>Ingredientes</h2>
                    <button class="btn btn-primary m-1" onclick="create()" >Agregar Ingrediente</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="manageTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Descripcion</th>
                                    <th>Unidad</th>
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
          <h4 class="modal-title">Nuevo Ingrediente</h4>
        </div>
        <form id="formcreate" action="{{ route('ingredient.create')}}" method="post">
            {{ csrf_field() }}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                    <small>Descripcion</small>
                        <input type="text" class="form-control" id="description" name="description" required>
                    </div>
                    <div class="col-md-4">
                    <small>Unidad</small>
                        <select name="unidad" id="unidad" class="form-control" require>
                            <option value="UNIDAD">Unidad</option>
                            <option value="KILOS">KG</option>
                            <option value="LITROS">Litros</option>
                        </select>
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
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
          <h4 class="modal-title">Editar Ingrediente</h4>
        </div>
        <form id="formupdate" method="post">
            {{ csrf_field() }}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                    <small>Descripcion</small>
                        <input type="text" class="form-control" id="description-edit" name="description" required>
                    </div>
                    <div class="col-md-4">
                    <small>Unidad</small>
                        <select name="unidad" id="unidad" class="form-control" required>
                            <option value="UNIDAD">Unidad</option>
                            <option value="KILOS">KG</option>
                            <option value="LITROS">Litros</option>
                        </select>
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
        ajax: {
            type : 'get',
            url: '{{ route("ingredient.getTable") }}',
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            xhr: function () {

                var xhr = new window.XMLHttpRequest();
                //Download progress

                xhr.addEventListener("progress", function (evt) {
                    // console.log(evt.lengthComputable); // false

                    if (evt.lengthComputable) {
                        percentComplete = parseInt( (evt.loaded / evt.total * 100), 10);
                        console.log(percentComplete);
                        $('.progress-bar').data("aria-valuenow",percentComplete);
                        $('.progress-bar').css("width",percentComplete+'%');
                        $('.progress-bar').html("Ingredientes Cargados "+percentComplete+'%');
                        // progress.attr("style", "witdh:"+Math.round(percentComplete * 100) + "%");
                    }

                }, false);
                return xhr;
            },

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
                url: url+'/ingredient/delete/'+id,
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
        url: url+'/ingredient/update/'+id,
        type: 'get',
        success: function(response){
            $('#formupdate').attr('action', url+'/ingredient/update/'+id );
            $('#description-edit').val(response.data.fullname);
            $('#unidad-edit').val(response.data.unidad);
            $('#editModal').modal('show');
        },
        error: function(ex){
            console.log(ex);
        }
    });

}
</script>
@endsection
