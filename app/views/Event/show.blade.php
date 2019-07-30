@extends('template.main')
@section('solicitude')
	<div class="container-fluid">
		{{ Form::token() }}
		<div class="page-header">
			<h3>Eventos</h3>
			<div>
			<label>Busqueda por Usuario</label>
			<select id="user-id">
				<option value=0>Todos</option>
				@foreach( $reps as $rep )
					<option value="{{ $rep['user_id'] }}">{{ $rep['nombres'] }} {{ $rep['apellidos'] }}</option>
				@endforeach
			</select>
		</div>
		<div>
			<label>Busqueda por Zona</label>
			<select id="zone-id">
				<option value=0>Todas</option>
				@foreach( $zones as $zone )
					<option value="{{ $zone['N3GNIVEL3GEOG'] }}">{{ $zone['N3GDESCRIPCION'] }}</option>
				@endforeach
			</select>
		</div>
		<div>
			<button id="search-events" class="btn btn-primary ladda-button" data-style="zoom-out">
				<span class="glyphicon glyphicon-search"></span>
			</button>
		</div>
		</div>

		<div class="row" id="container-event"></div>
	</div>
	<script>
		$( document ).ready( function()
		{	
			getEvents();
			GBREPORTS.changeDateRange( 'M' );
		});
		function getEvents()
		{
		    var url   = URL_BASE + 'eventos/list';
		    var spin = Ladda.create( document.querySelector( '#search-events' ) );
		    spin.start();
		    $.ajax(
		    {
		        type        : 'POST',
		        url         : url,
		        ContentType : false,
		        cache       : false,
		        data: 
		        {
		        	_token     : GBREPORTS.token,
		        	date_start : $( '#drp_menubar' ).data( 'daterangepicker' ).startDate.format( "L" ),
		            date_end   : $( '#drp_menubar' ).data( 'daterangepicker' ).endDate.format( "L" ),
		            usuario    : $( '#user-id' ).val(),
		            zona       : $( '#zone-id' ).val()
		    	}
		    }).done( function( dataResult )
		    {
		    	spin.stop();
		    	$( "#container-event" ).empty();
		     	$( "#container-event" ).html(dataResult);
		    }).fail( function( statusCode , errorThrown )
		    {
		    	spin.stop();
		    	ajaxError( statusCode , errorThrown );
		    });
		}
	</script>
@endsection