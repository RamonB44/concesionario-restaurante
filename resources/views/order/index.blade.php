@extends('adminlte::page')

@section('title', 'Orden')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-start">
                    <h2>Ordenes</h2>
                    <a class="btn btn-primary m-1" href="{{ route('orders.submitOrden') }}" >Crear Orden</a>
                    <a class="btn btn-success m-1" href="{{ route('xlsx.getData') }}" >Export to Excel</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="manageTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>N° Orden</th>
                                    <!-- <th>Qty</th> -->
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
            url: '{{ route("orders.getTable") }}',
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
                        $('.progress-bar').html("Productos Cargados "+percentComplete+'%');
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
                url: url+'/orders/anular/'+id,
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
        url: url+'/orders/update/'+id,
        type: 'get',
        success: function(response){
            $('#formupdate').attr('action', url+'/orders/update/'+id );
            $('#name-edit').val(response.name);
            $('#editModal').modal('show');
        },
        error: function(ex){
            console.log(ex);
        }
    });
}

function getXlsx(){

}
</script>
@endsection
