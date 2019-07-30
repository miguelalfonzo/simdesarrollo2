@extends( 'template.main' )
@section( 'solicitude' )
	@if( $rpta[ 'Status' ] != 'Ok' )
		<nav class="navbar navbar-default container-fluid">
			<h4 class="text-danger"><b>Error - {{ $rpta[ 'Description' ] }}</b></h4>
		</nav>
	@endif
			
	<div class="form-group col-lg-6">
		<h4><b>Sincronizacion de Supervisores y Representantes</b></h4>
	</div>

	<div class="form-group col-lg-2 pull-right">
		<button id="synchro" type="button" class="btn btn-success btn-lg" style="margin-top:20px">Sincronizar</button>
	</div>

	<div class="container-fluid">
		<table class="table table-striped table-hover table-bordered">
			<thead>
				<tr>
					<th>Supervisor</th>
					<th>Representante</th>
				</tr>
			</thead>
			<tbody>
				@foreach( $rpta[ 'Data' ] as $row )
					<tr>
						<td>{{ $row->supervisor }}</td>
						<td>{{ $row->visitador }} </td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<script>
		$( '#synchro' ).click( function()
		{
			bootbox.confirm( '<h4 class="text-danger"><b>¿ Esta seguro de ejecutar la sincronizacion ?</b></h4>' , function( result )
			{
				if( result )
				{
					$.ajax(
					{
						type : 'POST' ,
						url  : 'update-sup-rep',
						data : { _token : GBREPORTS.token }
					}).fail( function( statusCode , errorThrown )
					{
						bootbox.alert( '<h4 class="text-danger"><b>No se pudo invocar el proceso de sincronizacion. Codigo: ' + statusCode.status + '</b></h4>' );
					}).done( function( response )
					{
						var i = 0;
						var j = 0;
						var ul = '<ul class="list-group" style="max-height:150px;overflow:scroll">';
						response[ 'Data' ].forEach( function( currentValue , index , array )
						{
							if( typeof currentValue[ 'status' ] == 'undefined' )
							{
								ul += '<li class="list-group-item list-group-item-danger">Represenante:' + currentValue[ 'visvisitador' ] + '. Error - No se realizo la sincronizacion.</li>';
								++i;		
							}
							else if( currentValue[ 'status' ] == 0 )
							{
								ul += '<li class="list-group-item list-group-item-info">Represenante:' + currentValue[ 'visvisitador' ] + '. Info - El usuario no esta registrado en el sistema de inversion de marketing.</li>';
								++i;
							}
							else if( currentValue[ 'status' ] > 1 )
							{
								ul += '<li class="list-group-item list-group-item-warning">Represenante:' + currentValue[ 'visvisitador' ] + '. Cancelado - Se encontro mas de 1 registro con el mismo codigo de representante.</li>';
								++i;
							}
							else if( currentValue[ 'status' ] == 1 )
							{
								++j;
							}
						});
						ul += '</ul>';
						
						if( i == 0 )
						{
							ul = '';
						}

						if( response[ 'Status' ] != 'Ok' )
						{
							var status = '<h4 class="text-danger"><b>' + response[ 'Description' ] + '</b></h4>' + ul ;
						
						}
						else
						{
							if( i == 0 )
							{
								if( j == 0 )
								{
									var status = '<h4 class="text-success">No se encontro diferencias en las relaciones de los representantes con los supervisores</h4>';
								}
								else
								{
									var status = '<h4 class="text-success">Sincronizacion realizada correctamente</h4>';
								}
							}
							else
							{
								var status = '<h4 class="text-warning">La sincronizacion tuvo las siguientes observaciones:</h4>' + ul; 
							}
						}
						bootbox.alert( status , function()
						{
							window.location.href = 'view-sup-rep';
						});
					});
				}
			});
		});
	</script>
@stop
