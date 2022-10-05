@extends('adminlte::page')

@section('title', 'Consultas')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex bd-highlight">
                        <div class="p-2 bd-highlight"><h2 class="text-inline text-bold">Resportes Graficos</h2></div>
                        <div class="p-2 bd-highlight">

                            <input id="codeOrdni" name="code" type="text" class="form-control">

                        </div>
                        <div class="p-2 bd-highlight">

                            <a href="javascript:search(null)" class="btn btn-primary">Buscar</a>
                        </div>
                        <div class="ml-auto p-2 bd-highlight">
                            <button type="button" class="btn btn-default" id="daterange-btn">
                                <span>
                                  <i class="fa fa-calendar"></i> {{ date('Y-m-d'). " - " . date('Y-m-d') }}
                                </span>
                                <i class="fa fa-caret-down"></i>
                              </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <small>Resultado</small>
                    <div id="result">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('plugins.DataRangePickerJs', true)
@section('js')
<script>
    var url = '{{ url("/") }}';
    var start_date = "{{ date('Y-m-d') }}";
    var end_date = "{{ date('Y-m-d') }}";
    $(document).ready(function(){
        $('#daterange-btn').daterangepicker(
        {
            ranges   : {
            'Hoy'       : [moment(), moment()],
            'Ayer'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Ultimos 7 Dias' : [moment().subtract(6, 'days'), moment()],
            'Ultimos 30 Dias': [moment().subtract(29, 'days'), moment()],
            'Este Semana'  : [moment().startOf('week').day('1'), moment().endOf('week').day('7')],
            'Este Mes'  : [moment().startOf('month'), moment().endOf('month')],
            'Ultimo Mes'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate  : moment(),
        },

        function (start, end) {
            $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
            //loadGraphics(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
            start_date = start.format('YYYY-MM-DD');
            end_date = end.format('YYYY-MM-DD');
        }
    );

    });

    function search(){
        var codigo = $('#codeOrdni').val();

        $.ajax({
            url: url+'/orders/searchByEmploye/'+codigo+'/'+start_date+'/'+end_date,
            type: 'GET',
            error:function(response){
                alert('Algo Ocurrio mal');
            },
            success:function(response){
                $('#result').children().remove();
                $('#result').append(response);
            },
            complete:function(response){
                console.log(response);
            }
        })
        // $('#result').append('<span>').addClass('badge badge-primary').text('holo');

    }

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
</script>
@endsection
