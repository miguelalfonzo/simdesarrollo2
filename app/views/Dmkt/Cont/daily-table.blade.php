<div class="table-responsive">
	<table class="table table-bordered table-hover table-condensed tb_style">
		<thead>
			<tr>
				<th>Asiento</th>
				<th>N° Cuenta</th>
				<th>CC</th>
				<th>N° Origen</th>
				<th>Fec. Origen</th>
				<th>IVA</th>
				<th>Cod.  Prov.</th>
				<th>Nombre Del proveedor</th>
				<th>Cod.</th>
				<th>RUC</th>
				<th>Prefijo</th>
				<th>Cbte. Proveedor</th>
				<th>D/C</th>
				<th>Importe</th>
				<th>Leyenda Fj</th>
				<th>Leyenda Variable</th>
				<th>Tipo  Resp.</th>
			</tr>
		</thead>
		<tbody>
			@foreach( $solicitud->dailyEntries as $dailyEntry )
				<tr>
					<td>{{{ $dailyEntry->penclave or '' }}}</td>
					<td>{{ $dailyEntry->num_cuenta }}</td>
					<td>{{ $dailyEntry->cc }}</td>
					<td>{{ $dailyEntry->nro_origen }}</td>
					<td>{{ Carbon\Carbon::createFromFormat( 'Y-m-d H:i:s' , $dailyEntry->fec_origen )->format( 'd/m/Y' ) }}</td>
					<td>{{ $dailyEntry->iva }}</td>
					<td>{{ $dailyEntry->cod_pro }}</td>
					<td>{{ $dailyEntry->nom_prov }}</td>
					<td>{{ $dailyEntry->cod }}</td>
					<td>{{ $dailyEntry->ruc }}</td>
					<td>{{ $dailyEntry->prefijo }}</td>
					<td>{{ $dailyEntry->cbte_prov }}</td>
					<td>{{ $dailyEntry->d_c }}</td>
					<td>{{ $dailyEntry->importe }}</td>
					<td>{{ $dailyEntry->leyenda_fj }}</td>
					<td>{{ $dailyEntry->leyenda }}</td>
					<td>{{ $dailyEntry->tipo_resp }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>