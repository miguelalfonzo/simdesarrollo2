<table class="table table-hover table-bordered table-condensed dataTable" id="table_rentabilidad" style="width: 100%">
    <thead>
        <tr>
            <th>#</th>
            <th>Estado</th>
            <th>Tiempo Promedio</th>
            <th>N°</th>
        </tr>
    </thead>
    <tbody>
        @foreach ( $fondos as $fondo )
            <tr row-id="{{$fondo->id}}" type="fondo-cuenta">
                <td style="text-align:center">{{$fondo->id}}</td>
                <td class="nombre" style="text-align:center">{{$fondo->nombre}}</td>
                <td class="idtipomoneda" editable=1 style="text-align:center">{{$fondo->typeMoney->simbolo}}</td>
                <td class="num_cuenta" editable=3 style="text-align:center">{{$fondo->num_cuenta}}</td>
                <td editable=2 style="text-align:center">
                    <a class="maintenance-edit">
                        <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                    <!-- <a class="elementDelete" href="#"><span class="glyphicon glyphicon-remove"></span></a> -->
                </td>
            </tr>
        @endforeach
    </tbody>
</table>