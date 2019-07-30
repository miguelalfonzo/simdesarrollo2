<!DOCTYPE html>
<html>
	<head>
	    <meta charset="UTF-8">
	</head>
	<body>
		<table>
			<thead>
				<tr>
					@foreach( $columns as $column )
						<th>{{ $column[ 'title' ] }}</th>
					@endforeach  
				</tr>
			</thead>
			<tbody>
				@foreach( $data as $row )
					<tr>
						@foreach( $columns as $column )
							<td>{{ $row->{ $column[ 'data' ] } }}</td>
						@endforeach  
					</tr>
				@endforeach  
			</tbody>
		</table>
	</body>
</html>