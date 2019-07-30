@extends( 'template.main' )
@section( 'solicitude' )
	@if( Auth::user()->type != ESTUD )            
	    @include('template.User.menu')
	    @include('template.Modals.temporal_user')
	    @if( Auth::user()->type == TESORERIA )
	        @include('template.Modals.deposit')
	        @include( 'template.Modals.massive-deposit' )
	    @elseif( Auth::user()->type == CONT )
		    @include( 'template.Modals.massive-revision' )    
	    @endif
		<button id="show_leyenda" type="button" class="btn btn-link">Ver leyenda</button>
	@endif
@stop