<div class="d-flex justify-content-start">
    <div>
        <h2>Empleado : {{$employe->fullname}}</h2>
    </div>
</div>
<div class="table row">
    <table class="table table-bordered" id="myTable">
        <thead>
            <th>#</th>
            <th>Qty Productos</th>
            <th class="text-center">Total</th>
            <th>Orden</th>
            <th class="text-center">Fecha y Hora</th>
            <th></th>
        </thead>
        <tbody class="panel">
            @php
            $total = 0;
            @endphp
            @forelse ($data as $k => $item)
                <tr>
                    <td><a href="javascript:void(null)" data-toggle="collapse" data-target="#demo{{$item->id}}" data-parent="#myTable" class="btn btn-primary">[+]</a></td>
                    <td>{{ "Items: ".count($item->detail) }}</td>
                    <td class="text-center">{{ $item->net_amount_value }}</td>
                    <td>{{ "Orden N°".$item->id}}</td>
                    <td class="text-center">{{ $item->created_at }}</td>
                    <td><a href="javascript:eliminar('{{ $item->id }}')" class="btn btn-danger">Eliminar</a></td>
                </tr>
                <tr id="demo{{ $item->id }}" class="collapse">
                    <td colspan="5">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($item->detail as $i)
                                <tr>
                                    <td></td>
                                    <td>{{ $i->products->fullname }}</td>
                                    <td>{{ $i->qty }}</td>
                                    <td class="text-center">{{ $i->qty * $i->rate_price }}</td>
                                </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </td>
                </tr>
                @php
                    $total = $total + $item->net_amount_value;
                @endphp
            @empty
                <h2>Sin resultados</h2>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2"></td>
            <td colspan="3" class="text-left">Total S/.{{$total}}</td>
            </tr>
        </tfoot>
    </table>
</div>
