<table class="table table-hover table-bordered table-condensed dataTable" id="table_fondo_mkt_history" width="100%">
    <thead>
        <tr>
            <th>#</th>
            <th>Periodo</th>
            <th>Fecha</th>
            <th>Descripcion</th>
            <th>Fondo</th>
            <th>Autorizado Por</th>
            <th>Abono</th>
            <th>Cargo</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-center">Saldo del Mes Anterior</td>
            <td class="text-center">{{ $saldo }}</td>
        </tr>
        @foreach( $FondoMktHistories as $FondoMktHistory )
            <tr>
                <td class="text-center">{{ $FondoMktHistory->id }}</td>
                <td class="text-center">{{ $FondoMktHistory->periodo }}</td>
                <td class="text-center">{{ $FondoMktHistory->created_at }}
                <td class="text-center">
                    <span class="label label-primary">{{ $FondoMktHistory->fondoMktHistoryReason->descripcion }}</span>
                    @if ( ! is_null( $FondoMktHistory->id_solicitud ) )
                        <span class="label label-success open-details2" rel="{{ $FondoMktHistory->id_solicitud }}" style="cursor:pointer;display:ruby-base">
                            Solicitud #{{ $FondoMktHistory->id_solicitud }}
                        </span>
                    @endif
                </td>
                <td>{{ $FondoMktHistory->toFund->middle_name }}</td>
                <td class="text-center">{{ $FondoMktHistory->createdByPersonal->full_name }}</td>
                <td class="text-center">
                    @if ( $FondoMktHistory->to_old_saldo > $FondoMktHistory->to_new_saldo )
                        {{ $FondoMktHistory->to_old_saldo - $FondoMktHistory->to_new_saldo }}
                    @endif
                </td>
                <td class="text-center">
                    @if ( $FondoMktHistory->to_new_saldo > $FondoMktHistory->to_old_saldo )
                        {{ $FondoMktHistory->to_new_saldo - $FondoMktHistory->to_old_saldo }}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-center">Saldo Disponible Final</td>
            <td class="text-center">{{ $saldoNeto }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-center">Saldo Contable Final</td>
            <td class="text-center">{{ $saldoContable }}</td>    
        </tr>
    </tfoot>
</table>