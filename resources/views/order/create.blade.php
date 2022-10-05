@extends('adminlte::page')

@section('title', 'Orden')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                <h2>Ventas - Concesionario  </h2>
                </div>

                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-1 text-center">
                        <small>ScanBar</small>
                            <input type="checkbox" name="is_checked" class="form-control" id="is_checked" checked>
                        </div>
                        <div class="col-md-3">
                        <small>Empleado</small>
                            <input type="text" class="form-control" id="employe" name="employe">
                        </div>
                        <div class="col-md-1">
                        <br>
                            <a href="javascript:void(null)" class="btn btn-primary">Buscar</a>
                        </div>
                        <div id="result" class="col-md-5">

                        </div>
                        <div class="col-md-1 text-center">
                            <small >Contado</small>
                            <input type="checkbox" name="is_credit" class="form-control" id="is_credit">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2">
                            <div class="list-group" id="list-tab" role="tablist">
                            @forelse ($category as $k => $item)
                            <a class="list-group-item list-group-item-action <?php echo ($k==0)?"show active":"" ?>" id="list-{{ $item->name }}-list" data-toggle="list" href="#list-{{ $item->name }}" role="tab" aria-controls="{{ $item->name }}">{{ $item->name }}</a>
                            @empty
                            <h2>Sin existencias</h2>
                            @endforelse
                            </div>
                        </div>
                        <div class="col-10">
                            <div class="tab-content" id="nav-tabContent">

                            @forelse ($category as $k => $item)
                            <div class="tab-pane fade <?php echo ($k==0)?"show active":"" ?>" id="list-{{ $item->name }}" role="tabpanel" aria-labelledby="list-{{ $item->name }}-list">

                                <div id="itemsSell" class="row row-cols-1 row-cols-md-3">


                                        @forelse ($product as $i)
                                            @if ($i->id_category == $item->id)
                                            <div class="col-mb-3 " onclick="addProduct('{{ $i->id }}')">
                                                <div class="card bg-dark text-white text-uppercase text-center m-2" style="max-width: 15rem;">
                                                    <img src="{{ asset('/storage/blank.png') }}" class="card-img" alt="...">
                                                    <div class="card-img-overlay " style="font-size: 3em;word-break: break-all">
                                                    <h5 class="card-title text-center" style="float:none">{{ $i->fullname}}</h5>
                                                        <div class="card-body "  >
                                                        <p class="contado_price">S/{{  $i->rate_price }}</p>
                                                        <p class="rate_price" hidden>S/{{  $i->contado_price   }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        @empty

                                        @endforelse

                                </div>

                            </div>
                            @empty

                            @endforelse

                            </div>
                        </div>
                        </div>

                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-header text-center">
                    <h2>Carrito</h2>
                </div>
                <form id="formCreate" action="{{ route('orders.submitOrden') }}" method="post">
                    {{ csrf_field() }}
                    <div id="items" class="card-body text-center">

                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-center">
                            {{-- <button type="submit" class="btn btn-primary">Guardar</button> --}}
                            <input type="hidden" id="net_amount_value" name="net_amount_value">
                            <button type="button" onclick="submitOrden()" id="submitAmount" class="btn btn-success" style="font-size: 1.5em">Total: S/0</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
@section('js')
<script>
    var url = "{{ url('/') }}";
    var string = "";
    var credit = 1;

$(document).on('keypress', function (e) {

    patron = /^([0-9])*$/;
    if(patron.test(String.fromCharCode(e.which)) ){
        string += String.fromCharCode(e.which);
    }

    if (e.which == 13 && $('#is_checked').is(':checked')) {

        console.log(string);
        $.ajax({
        url: url+'/employes/code/'+string,
        type: 'get',
        dataType: 'json',
        success:function(response){

            console.log(response);

            var result = "";
            var html = "";

            if(response.success){
                result =  '<input type="hidden" id="id_employe" name="employe" value="'+response.data.id+'">'+
                        '<div class="alert alert-success alert-dismissible fade show text-center" role="alert">'+
                            '<strong>'+response.data.code+'</strong>'+response.data.fullname+
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                            '<span aria-hidden="true">&times;</span>'+
                            '</button>'+
                        '</div>';
                // response.data
                // $('#items')
                if(response.order != null){
                    $.each(response.order.detail,function(i,e){
                    console.log(e.products);
                    var id = "'"+e.id_product+"_"+credit+"'";
                    html+= '<a id="item_'+e.id_product+'_'+credit+'" href="javascript:discountRow('+id+')" class="btn btn-';
                    if(e.is_credit == 1){
                        html+="dark";
                    }else{
                        html+="primary";
                    }
                    html +=' m-1">'+
                    '<input type="hidden" id="remited_'+e.id_product+'_'+e.is_credit+'" name="remited[]" value="1">'+
                    '<input type="hidden" id="qty_plus_'+e.id_product+'_'+e.is_credit+'" name="qty_plus[]" value="0">'+
                    '<input type="hidden" id="product_'+e.id_product+'_'+e.is_credit+'" name="product[]" value="'+e.id_product+'">'+
                    '<input type="hidden" id="product_name_'+e.id_product+'_'+e.is_credit+'" name="product_name[]" value="'+e.products.fullname+'">'+
                    '<input type="hidden" id="qty_'+e.id_product+'_'+e.is_credit+'" name="qty[]" value="'+e.qty+'">';
                    html +='<input type="hidden" id="rate_'+e.id_product+'_'+e.is_credit+'" name="rate[]" value="'+e.rate_price+'">';
                    html+='<input type="hidden" id="is_credit_'+e.id_product+'_'+e.is_credit+'" name="is_credit[]" value="'+e.is_credit+'">'+
                    e.products.fullname   +' <span id="'+e.id_product+'_'+e.is_credit+'" class="badge badge-light">'+e.qty+'</span>'+
                    '</a>';
                    });
                    $('#net_amount_value').val(response.order.net_amount_value);
                    $('#submitAmount').text('Total S/'+response.order.net_amount_value);
                    $("#items").html(html);
                }


            }else{
                result =  '<div class="alert alert-danger alert-dismissible fade show text-center" role="alert">'+
                            '<strong>No existe</strong>'+
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                            '<span aria-hidden="true">&times;</span>'+
                            '</button>'+
                '</div>';
                $("#items").children().remove();
                $('#submitAmount').text('Total S/0');
            }

            $('#result').html(result);
        },
        error:function(response){

        }

        });
        string = "";
    }

});

$('#is_credit').on('click',function(){
    if(!$(this).is(':checked')){
        $('.rate_price').attr('hidden',true);
        $('.contado_price').removeAttr('hidden');
        credit = 1;
    }else{
        $('.contado_price').attr('hidden',true);
        $('.rate_price').removeAttr('hidden');
        credit = 0;
    }
});

function addProduct(id){

    $.ajax({
        url: url+'/products/getbyId/'+id,
        type: 'GET',
        dataType: 'json',
        success:function(response){
            // $('#demo2').html(response);
            console.log(response);
            var check = check_in_items(response);

            if(!check){
                addRow(response);
            }
            subAmount();
        },
        error:function(response){
            console.log(response);
        }
    });
}

function check_in_items(response){
    var check = false;
    var table = $("#items a");
    var count_table_tbody_tr = $("#items a").length;
    if(count_table_tbody_tr>0){

        $('#items a').each(function(i,e){
            console.log($(e).attr('id'));
            // console.log("Cantidad: all "+Number($(e).children().text()));
            if("item_"+response.data.id+"_"+credit == $(e).attr('id')){
                // console.log("Cantidad: all "+Number($(e).children().text()));
                var qty = Number($(e).children().text());
                var qty_plus = Number($('#qty_plus_'+response.data.id+"_"+credit).val());
                qty = qty + 1;
                $(e).find("span").remove();
                $('#qty_plus_'+response.data.id+"_"+credit).val(qty_plus + 1);
                $('#qty_'+response.data.id+"_"+credit).val(qty);
                $(e).append("<span class='badge badge-light' >"+qty+"</span>");
                check = true;
            }
        });
      }
    return check;
}

function subAmount(){
    var totalAmount = 0;
    $('#items a').each(function(i,e){
        var qty = $('input[name="qty[]"]',e).attr('value');
        var rate = $('input[name="rate[]"]',e).attr('value');
        var total = qty * rate;
        console.log(total);
        totalAmount = totalAmount + total;
        totalAmount.toFixed(2);
    });
    console.log(totalAmount);
    $('#submitAmount').text("Total S/"+totalAmount);
    $('#net_amount_value').val(totalAmount);
}

function addRow(response){
    var divs = $('#items');
    var count_a = $('#items a').length;
    var id = "'"+response.data.id+"_"+credit+"'";
    $('#is_credit').prop("checked", false);

    var html = '<a id="item_'+response.data.id+'_'+credit+'" href="javascript:discountRow(' +id + ')" class="btn btn-';
                    if(credit == 1){
                        html+="dark";
                    }else{
                        html+="primary";
                    }
                    html +=' m-1">'+
                    '<input type="hidden" name="qty_plus[]" value="0">'+
                    '<input type="hidden" name="remited[]" value="0">'+
                    '<input type="hidden" id="product_'+response.data.id+'_'+credit+'" name="product[]" value="'+response.data.id+'">'+
                    '<input type="hidden" id="product_name_'+response.data.id+'_'+credit+'" name="product_name[]" value="'+response.data.fullname+'">'+
                    '<input type="hidden" id="qty_'+response.data.id+'_'+credit+'" name="qty[]" value="1">';
                    if(credit == 1){
                        html +='<input type="hidden" id="rate_'+response.data.id+'_'+credit+'" name="rate[]" value="'+response.data.rate_price+'">';
                    }else{
                        html +='<input type="hidden" id="rate_'+response.data.id+'_'+credit+'" name="rate[]" value="'+response.data.contado_price+'">';
                    }
                    html+='<input type="hidden" id="is_credit_'+response.data.id+'_'+credit+'" name="is_credit[]" value="'+credit+'">'+
                    response.data.fullname   +' <span id="'+response.data.id+'_'+credit+'" class="badge badge-light">1</span>'+
    '</a>';

    if(count_a >= 1) {$("#items a:last").after(html);}else{$("#items").html(html);}
    $('.rate_price').attr('hidden',true);
        $('.contado_price').removeAttr('hidden');
        credit = 1;
}

function discountRow(row_id){
    var html = $("#items a#item_"+row_id+" span").text();
    console.log(html);
    if(html>1){
        html = html - 1;
        $('#qty_'+row_id).val(html);
        $("#items a#item_"+row_id+" span").html(html);
        subAmount();
    }else{
        $("#items a#item_"+row_id).remove();
        subAmount();
    }

}

function submitOrden(){
    var datos = {};
    var tableCount = $('#items a').length;
    var employe = $('#id_employe').length;
    if(!employe>0){
        alert("No se puede vender a ningun empleado");
        return;
    }
    if(!tableCount>0){
        alert("Elige algun producto");
        return;
    }
    datos['product'] = $("input[name='product[]']").serializeArray();
    datos['product_name'] = $("input[name='product_name[]']").serializeArray();
    datos['qty'] = $("input[name='qty[]']").serializeArray();
    datos['qty_plus'] = $("input[name='qty_plus[]']").serializeArray();
    datos['remited'] = $("input[name='remited[]']").serializeArray();
    datos['rate_price'] = $("input[name='rate[]']").serializeArray();
    datos['is_credit'] = $("input[name='is_credit[]']").serializeArray();

    datos['net_amount_value'] = $('#net_amount_value').val();
    datos['employe'] = $('#id_employe').val();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    console.log(datos);

    $.ajax({
        url: '{{ route("orders.submitOrden") }}',
        type: 'POST',
        data: datos,
        beforeSend:function(response){
            //disable button
            $('#submitAmount').attr('disabled',true);
        },
        success:function(response){
            // $('#demo2').html(response);
            eraser();
            if(!response.success){
                alert('algo ocurrio mal')
            }
        },
        error:function(response){
            //disable button and show error response
            $('#submitAmount').attr('disabled',false);
            console.log(response);
        },
        complete:function(response){
            //disable button
            eraser();
            $('#submitAmount').attr('disabled',false);
        }
    });
}

function eraser(){
    $('#submitAmount').text("Total S/0");
    $('#result').children().remove();
    $('#net_amount_value').val('0');
    $('#items').children().remove();
}
</script>
@endsection
