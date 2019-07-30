<table id="table-expense" class="table table-bordered">
	<thead>
		<tr>
			<th>Comprobante</th>
			<th>RUC</th>
			<th>Razón Social</th>
			<th>Nro. Comprobante</th>
			<th>Fecha</th>
			<th>Monto Total</th>
			@if ( ! isset( $view ) && ( in_array( $solicitud->id_estado , [ GASTO_HABILITADO , ENTREGADO ] ) || Auth::user()->type == CONT ) )
				<th>Editar</th>
				<th>Eliminar</th>
			@endif
		</tr>
	</thead>
	<tbody>
		@if ( $solicitud->expenses->count() == 0 )
			<tr>
				@if ( ! isset( $view ) && ( in_array( $solicitud->id_estado , [ GASTO_HABILITADO , ENTREGADO ] ) || Auth::user()->type == CONT ) )
					<td colspan="8">No se registro documentos</td>
				@else
					<td colspan="6">No se registro documentos</td>
				@endif
			</tr>
		@else
			@foreach( $solicitud->expenses as $expense )
				<tr data-id="{{$expense->id}}">
					<td class="proof-type text-center">{{ $expense->proof->descripcion }}</td>
					<td class="ruc text-center">{{{ $expense->ruc or '' }}}</td>
					<td class="razon text-center">{{{ $expense->razon or '-' }}}</td>
					<td class="voucher_number text-center">{{$expense->num_prefijo.'-'.$expense->num_serie}}</td>
					<td class="date_movement text-center">{{date('d/m/Y',strtotime($expense->fecha_movimiento))}}</td>
					<td class="total text-center">
						<span class="type_money">{{$solicitud->detalle->typemoney->simbolo}}</span>
						<span class="total_expense">{{(real)$expense->monto}}</span>
					</td>
					@if ( ! isset( $view ) && ( in_array( $solicitud->id_estado , [ GASTO_HABILITADO , ENTREGADO ] ) || Auth::user()->type == CONT ) )
						<td class="text-center">
							<a href="#" class="edit-expense">
								<span class="glyphicon glyphicon-pencil "></span>
							</a>
						</td>
						<td class="text-center">
							<a href="#" class="delete-expense">
								<span class="glyphicon glyphicon-remove"></span>
							</a>
						</td>
					@endif
				</tr>	
			@endforeach
		@endif
	</tbody>
</table>