<div class="clearfix" style="border-bottom: 1px solid #E5E5E5"></div>
@if( isset( $devolucion) && $devolucion )
	<h4 class="text-warning">Si Realizo la devolucion del saldo de S/.{{ $balance}} ingrese la siguiente informacion para culminar el descargo caso contrario vuelva a ingresar cuando culmine con la devolucion del saldo</h4>
@else
	<h4 class="text-warning">Ingrese la siguiente informacion para culminar el descargo</h4>
@endif

<div class="row">
	<!-- DEVOLUCION -->
	@if( isset( $devolucion ) && $devolucion )
		<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
		    <label class="control-label">N° Operacion / Transferencia / Cheque de la Devolución</label>
		    <div>
		        <input type="text" class="form-control input-md" name="numero_operacion_devolucion">
		    </div>
		</div>
	@endif

	<!-- TIPO DE INVERSION DE LA SOLICITUD -->
	@if( isset( $investments ) )
		<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
		    <label class="control-label">Tipo de Inversion</label>
		    <div>
		        <select class="form-control" name="inversion">
	                <option value="0" selected disabled>SELECCIONE LA INVERSIÓN</option>
		            @foreach( $investments as $investment )
	                    <option value="{{$investment->id}}">{{$investment->nombre}}</option>
		            @endforeach
		        </select>
		    </div>
		</div>
	@endif

	<!-- TIPO DE ACTIVIDAD DE LA SOLICITUD-->
	@if( isset( $activities ) )
		<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
		    <label class="control-label">Tipo de Actividad</label>
		    <div>
		        <select class="form-control" name="actividad">
	                <option value="0" selected disabled>SELECCIONE LA ACTIVIDAD</option>
		            @foreach( $activities as $activity )
	                    <option value="{{ $activity->id }}">{{ $activity->nombre }}</option>
		            @endforeach
		        </select>
		    </div>
		</div>
	@endif
</div>
