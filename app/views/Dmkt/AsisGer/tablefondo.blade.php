<table class="table table-hover table-bordered table-condensed dataTable" id="table_reporte_institucional" style="width: 100%">
    <thead>
        <tr>
            <th>#</th>
            <th>SISOL - Hospital</th>
            <th>Depositar a</th>
            <th>Nº Cuenta Bagó</th>
            <th>Total a depositar</th>
            <!-- <th>Supervisor</th> -->
            @if ( isset( $state ) && $state != BLOCKED )
                <th>Edicion</th>
            @endif
         </tr>
    </thead>
    <tbody>
        @if ( isset($solicituds) )
            @foreach( $solicituds as $solicitud )
                <tr>
                    <td>{{$solicitud->id}}</td>
                    <td style="text-align:center">
                        {{{ $solicitud->clients()->where( 'id_tipo_cliente' , 3 )->first()->institution->pejrazon  or $solicitud->titulo  }}}
                    </td>
                    <td style="text-align:center">{{$solicitud->personalTo->full_name}}</td>
                    <td style="text-align:center">
                        {{json_decode($solicitud->detalle->detalle)->num_cuenta}}
                    </td>
                    <td style="text-align:center">{{ 'S/.' . $solicitud->detalle->monto_actual}}</td>
                    <!-- <td style="text-align:center">$solicitud->detalle->supervisor</td> -->
                    @if ( isset( $state ) && $state != BLOCKED )
                        <td style="text-align:center">
                            @if ( $state == ACTIVE )
                                <div class="div-icons-solicituds">
                                    <a class="edit-fondo">
                                        <span class="glyphicon glyphicon-pencil"></span>
                                    </a>
                                    <a  class="delete-fondo">
                                        <span class="glyphicon glyphicon-remove"></span>
                                    </a>
                                </div>
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach
        @endif
    </tbody>
</table>