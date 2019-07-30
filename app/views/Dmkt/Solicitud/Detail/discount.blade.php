@if ( ! is_null( $detalle->monto_descuento ) )
	<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 has-warning">
	    @if( is_null( $detalle->descuento ) )
	    	<label class="control-label">Monto de Devolucion</label>
	    @else
    		<label class="control-label">Periodo de Descuento / Monto de Descuento</label>
		@endif    
	    <div class="input-group">
	    	@if ( is_null( $detalle->descuento ) )
    		    <span class="input-group-addon">{{ $detalle->typeMoney->simbolo }}</span>
		        <input type="text" class="form-control" disabled value="{{ $detalle->monto_descuento }}">	
	    	@else
		        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
		        <input type="text" class="form-control" disabled value="{{$detalle->descuento}}">
		        <span class="input-group-addon">{{ $detalle->typeMoney->simbolo }}</span>
		        <input type="text" class="form-control" disabled value="{{ $detalle->monto_descuento }}">
	    	@endif
	    </div>
	</div>
@endif