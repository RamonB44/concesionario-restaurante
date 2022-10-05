@extends('adminlte::page')

@section('title', 'Almacen')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                <h2>Regular Almacen</h2>
                </div>
                <form action="{{ route('stock.regulate') }}" method="post">
                {{ csrf_field() }}
                    <div class="card-body">
                        <div class="row m-1">
                            <div class="col-md-4">
                                <small>Movimiento</small>
                                <select name="movimiento" id="movimiento" class="form-control" required>
                                    <option value="1">Ingreso</option>
                                    <option value="0">Salida</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                        <small>Ingredientes <a href="javascript:addRow()">[Add]</a></small>
                            <div class="table">
                                <table id="productsTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Ingrediente</th>
                                            <th>Qty</th>
                                            <th class="text-center">[-]</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('plugins.Select2', true)
@section('js')
<script>
function addRow(){
    var table = $("#productsTable");
    var count_table_tbody_tr = $("#productsTable tbody tr").length;
    var row_id = count_table_tbody_tr +1;

    // if(response.success){
        // console.log(response.data);
        html = '<tr id="row_'+row_id+'">'+
        '<td>'+row_id+'</td>'+
        '<td>'+
        '<select id="product_'+row_id+'" name="product[]" class="form-control js-example-responsive" style="width: 100%" required></select>';
        html += '</td>'+
        '<td><input type="number" name="qty[]" id="qty_'+row_id+'" value="1" class="form-control"></td>'+
        '<td class="text-center"><button type="button" class="btn btn-danger" onclick="removeRow(\''+row_id+'\')"><i class="fas fa-eraser"></i></button></td>'+
        '</tr>';
        if(count_table_tbody_tr >= 1) {$("#productsTable tbody tr:last").after(html);}else{$("#productsTable tbody").html(html);}

        $('select[name="product[]"]').select2({
            placeholder: "Seleccione un ingrediente",
            minimumInputLength: 1,
            width: 'resolve',
            ajax: {
                url: "{{ route('ingredient.getData') }}", //change this url
                dataType: 'json',
                type: "GET",
                data: function (params) {
                    return {
                        termVal : params.term
                    };
                },
                processResults: function (data) {
                    return {
                    results: $.map(data, function(obj) {
                        return {
                            id: obj.id,
                            text: obj.fullname
                            };
                    })
                    };
                }
            },
            cache: true
        });
}


function removeRow(tr_id){
    $("#productsTable tbody tr#row_"+tr_id).remove();
}


</script>
@endsection