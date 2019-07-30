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
						<th>{{ $column[ 'name' ] }}</th>
					@endforeach  
				</tr>
			</thead>
			<tbody>
				@foreach( $Data as $row )
					<tr>
						@foreach( $columns as $column )
							@if( isset( $column[ 'relations' ] ) )
								<td>{{ $row->{ $column[ 'relations' ][ 0 ] }->{ $column[ 'relations' ][ 1 ] } }}</td>
							@else
								<td>{{ $row->{ $column[ 'data' ] } }}</td>
							@endif
						@endforeach  
					</tr>
				@endforeach  
			</tbody>
		</table>
	</body>
</html>