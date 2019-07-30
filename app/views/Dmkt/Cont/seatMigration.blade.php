<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
		<style type="text/css">
			.table-striped > tbody > tr:nth-of-type(odd) 
			{
				background-color: #f9f9f9;
			}
			.table > thead > tr > th 
			{
				vertical-align: bottom;
				border-bottom: 2px solid #ddd;
			}
		</style>
	</head>
	<body>
		<table class="table table-striped" cellspacing="0">
			<thead>
				<tr>
					<th>Estado</th>
					<th>Solicitud</th>
					<th>Tipo de Asiento</th>
					<th>N° Asiento / Descripcion</th>
				</tr>
			</thead>
			<tbody>
				@foreach( $ok as $idSolicitud => $seatTypeOk )
					@foreach( $seatTypeOk as $seatType => $seatNumber )
						<tr>
							<td>Correcto</td>
							<td>{{ $idSolicitud }}</td>
							<td>{{ $seatType == 'A' ? 'TELECREDITO' : 'MANUAL'  }}</td>
							<td>{{ $seatNumber }}</td>
						</tr>
					@endforeach	
				@endforeach
				@foreach( $error as $idSolicitud => $seatTypeerror )
					@foreach( $seatTypeOk as $seatType => $description )
						<tr>
							<td>Error</td>
							<td>{{ $idSolicitud }}</td>
							<td>{{ $seatType == 'A' ? 'TELECREDITO' : 'MANUAL'  }}</td>
							<td>{{ $description }} </td>
						</tr>
					@endforeach
				@endforeach
			</tbody>
		</table>
	</body>
</html>