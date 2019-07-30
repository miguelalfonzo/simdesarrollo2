<section class="row reg-expense" style="margin:0">
	<div class="col-xs-12 col-sm-12 col-md-12">
		<div class="form-expense">
			<div class="table-responsive">
				<table id="table-seat-solicitude" class="table table-bordered">
					<thead>
						<tr>
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
						@foreach( $entries as $entry )
							<tr>
								<td>{{ $entry->account_name }}</td>
								<td>{{ $entry->account_number }}</td>
								<td>{{ $entry->origin }}</td>
								<td>{{ $entry->d_c }}</td>
								<td>S/.</td>
								<td>{{ $entry->import }}</td>
								<td>{{ $entry->caption }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>