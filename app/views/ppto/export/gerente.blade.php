<!DOCTYPE html>
<html>
	<head>
	    <meta charset="UTF-8">
	</head>
	<body>
		<table>
			<thead>
				<tr>
					<td>Año</td>
					<td>Version</td>
					<td>Categoria</td>
					<td>Familia</td>
					<td>Monto</td>
				</tr>
			</thead>
			<tbody>
				@foreach( $Data as $row )
					<tr>
						<td>{{ $row->anio}}</td>
						<td>{{ $row->version }}</td>
						<td>{{ $row->sub_category->descripcion }}</td>
						<td>{{ $row->family->descripcion }}</td>
						<td>{{ $row->monto }}</td>
					</tr>
				@endforeach  
			</tbody>
		</table>
	</body>
</html>