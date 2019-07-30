<table class="table table-hover table-bordered table-condensed dataTable" id="table_movimientos" width="100%">
    <thead>
        <tr>
            <th>#</th>
            <th>Solicitud</th>
            <th>Responsable</th>
            <th>Aprobado por</th>
            <th>Fecha de Creación</th>
            <th>Ultima Edición</th>
            <th>Productos</th>
            <th>Fondo</th>
            <th>Deposito</th>
            <th>Descargo</th>
            <th>Devoluciones</th>
        </tr>
    </thead>
    <tbody>
        @if ( $solicituds->count() !== 0 )
            @foreach( $solicituds as  $solicitud )
                <tr>
                    <td class="text-center">{{ $solicitud->id }}</td>
                    <td class="text-left">
                        @if ( ! is_null( $solicitud->activity ) )
                        <span class="label label-info" style="margin-right:1em;background-color:{{$solicitud->activity->color}}">
                            {{$solicitud->activity->nombre}}
                        </span>
                    @endif
                        <label>{{ $solicitud->titulo }}</label>
                    </td>
                    <td class="text-center">
                        {{ $solicitud->assignedTo->personal->full_name }}
                    </td>
                    <td class="text-center">
                        @if ( $solicitud->idtiposolicitud == SOL_INST )
                            {{ $solicitud->createdBy->personal->full_name }}
                        @elseif ( in_array( $solicitud->idtiposolicitud , array( SOL_REP , REEMBOLSO ) ) )
                            {{{ $solicitud->approvedHistory->user->personal->full_name or '' }}}
                        @endif
                    </td>
                    <td class="text-center">{{$solicitud->created_at_parse }}</td>
                    <td class="text-center">{{$solicitud->updated_at}}</td>
                    <td class="text-center">
                        @if ( $solicitud->products->count() !== 0 )
                            @foreach( $solicitud->products as $product )
                                <span class="label label-info">{{ $product->marca->descripcion }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @if ( in_array( $solicitud->idtiposolicitud , array( SOL_REP , REEMBOLSO ) ) )
                            {{ is_null( $solicitud->products[ 0 ]->thisSubFondo ) ? '' : $solicitud->products[ 0 ]->thisSubFondo->full_name }}
                        @elseif( $solicitud->idtiposolicitud == SOL_INST )
                            {{ $solicitud->detalle->thisSubFondo->full_name }}
                        @endif
                    </td>
                    <td class="text-center">
                        {{{ is_null( $solicitud->detalle->deposit ) ? '' : $solicitud->detalle->deposit->money_amount }}}
                    </td>
                    <td class="text-center">
                        {{ $solicitud->expenses->count() === 0 ? '' : $solicitud->detalle->typeMoney->simbolo . ' ' . $solicitud->expenses->sum( 'monto' ) }}
                    </td>
                    <td class="text-center">
                        {{ $solicitud->devolutions->count() === 0 ? '' : $solicitud->detalle->typeMoney->simbolo . ' ' . $solicitud->devolutions->sum( 'monto' ) }}
                    </td>          
                </tr>
            @endforeach
        @endif
    </tbody>
</table>