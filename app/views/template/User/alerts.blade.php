@extends('template.main')
@section('solicitude')

<div class="page-header">
 	<h3>Alertas</h3>
</div>

<div class="panel panel-default">
 	<ul class="list-group" style="overflow-y:scroll;max-height:400px">
		<!-- List group -->
		@if(count($alerts)>0)
  			@foreach($alerts as $alert)
				@if( $alert[ 'typeData' ] == 'clientAlert' )
					@foreach($alert[ 'data' ] as $clientAlert )
						<li class="list-group-item">
							La 
							@foreach($clientAlert['solicitude'] as $id )
								<span class="label label-success open-details2" rel="{{$id}}" style="cursor:pointer">Solicitud N° {{$id}}</span>
							@endforeach 
							{{ $clientAlert[ 'msg' ] }}</br>
							@foreach( $clientAlert[ 'cliente' ] as $client )
								<span class="label label-info">{{$client }}</span>
							@endforeach
						</li>
					@endforeach
				@elseif( $alert[ 'typeData' ] == 'expenseAlert' )
					@foreach( $alert[ 'data' ] as $expenseAlert )
						<li class="list-group-item">
							La
							<span class="label label-success open-details2" rel="{{$expenseAlert["solicitude"]}}" style="cursor:pointer">Solicitud N° {{$expenseAlert['solicitude'] }}</span> 
							{{ $expenseAlert[ 'msg' ] }}
						</li>
					@endforeach
				@endif
			@endforeach
		@else
			<li class="list-group-item">No se encontraron alertas</li>
 		@endif
 	</ul>
</div>
@stop