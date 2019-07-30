<div class="table-responsive">
	<table 	class="table table-bordered table-hover table-condensed tb_style">
		<thead>
			<tr>
				<th>Asiento</th>
				<th>Cuenta</th>
				<th>N° de Cuenta</th>
				<th>Fecha de Origen</th>
				<th>D/C</th>
				<th>Moneda</th>
				<th>Importe</th>
				<th>Leyenda Variable</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{{ $solicitud->advanceCreditEntry->penclave }}</td>
				<td>{{ $solicitud->advanceCreditEntry->bago_account->ctanombrecta }}</td>
				<td>{{ $solicitud->advanceCreditEntry->num_cuenta }}</td> 
				<td>{{ $solicitud->advanceCreditEntry->fec_origen->format( 'd/m/Y' ) }}</td>
				<td>{{ ASIENTO_GASTO_BASE }}</td>
				<td>S/.</td>
				<td>{{ $solicitud->advanceCreditEntry->importe }}
				</td>
				<td>{{ $solicitud->advanceCreditEntry->leyenda }}</td>
			</tr>
			<tr>
				<td>{{{ $solicitud->advanceDepositEntry->penclave or '' }}}</td>
				<td>{{ $solicitud->detalle->deposit->bagoAccount->ctanombrecta }}</td>
				<td>{{ $solicitud->detalle->deposit->num_cuenta }}</td>
				<td>{{ $solicitud->detalle->deposit->updated_at }}</td>
				<td>{{ ASIENTO_GASTO_DEPOSITO }}</td>
				<td>S/.</td>
				<td>
					@if ( $solicitud->detalle->deposit->account->idtipomoneda == DOLARES )
						{{ round( $solicitud->detalle->deposit->total * $detalle->tcv , 2 , PHP_ROUND_HALF_DOWN ) }}
					@elseif ( $solicitud->detalle->deposit->account->idtipomoneda == SOLES )
						{{ $solicitud->detalle->deposit->total }}
					@endif
				</td>
				<td>{{ $solicitud->advanceDepositEntry->leyenda }}</td>
			</tr>
		</tbody>
	</table>
</div>