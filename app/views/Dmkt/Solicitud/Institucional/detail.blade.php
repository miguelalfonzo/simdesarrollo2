<section class="row" style="padding:0.3em 1em">
	<div class="page-header">
        <h2>{{$solicitud->titulo}} <span class="label label-default">{{{ $solicitud->activity->nombre or '' }}}</span></h2>
    </div>

    <!-- INVERSION -->
    @if ( ! is_null( $solicitud->id_inversion ) )
	    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
	        <label class="control-label">Tipo de Inversión</label>
	        <input type="text" class="form-control input-md" value="{{$solicitud->investment->nombre}}" readonly>
	    </div>
    @endif

	<!-- Monto -->
	@include('Dmkt.Solicitud.Detail.monto')

	<!-- Periodo -->
	<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
		<label class="control-label">Periodo</label>
		<div>
			<div class="input-group">
				<span class="input-group-addon">
		            <i class="glyphicon glyphicon-calendar"></i>
		        </span>
				<input class="form-control date_month" type="text" value="{{$detalle->periodo->periodo}}" disabled>
			</div>
		</div>
	</div>

	<!-- Solicitante -->
	@include('Dmkt.Solicitud.Detail.solicitante')

	<!-- Asignado A -->
	@include('Dmkt.Solicitud.Detail.asignado')

	<!-- Supervisor -->
	<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
		<label class="control-label"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Supervisor</label>
		<div class="input-group">
		 	<span class="input-group-addon">S</span>
	        <input type="text" class="form-control input-md" value="{{$detalle->supervisor}}" disabled>
		</div>
	</div>

	@include('Dmkt.Solicitud.Detail.fondo')

	<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
		<label class="control-label">Nº de Cuenta Bagó del Representante</label>
		<div>
			<input type="text" class="form-control" value="{{$detalle->num_cuenta}}" disabled>
		</div>
	</div>

	<!-- DEPOSITADO -->
	@include('Dmkt.Solicitud.Detail.depositado')

	<!-- N° de Operacion relacionada al deposito -->
	@if( ( Auth::user()->type == TESORERIA || Auth::user()->type == CONT ) && !is_null( $detalle->id_deposito ) )
		<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<label class="control-label">Nº de Operación</label>
			<div>
				<input type="text" class="form-control" value="{{ $detalle->deposit->num_transferencia }}" disabled>
			</div>
		</div>
	@endif

	<!-- Tasa del Día del Deposito -->
	@include('Dmkt.Solicitud.Detail.tasa')

	<!-- MONTO de DEVOLUCION -->
	<!-- nclude('Dmkt.Solicitud.Detail.devolucion') -->

    <div class="clearfix"></div>

    <!-- CLIENTES -->
    @include('Dmkt.Solicitud.Detail.clients')

    <!-- Lista de Devoluciones -->
    @include( 'Dmkt.Solicitud.Detail.devolucion2')

    <!-- Observation-->
    @include('Dmkt.Solicitud.Detail.anotation')

    {{-- if ( ! is_null( $solicitud->observacion) )
		<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label class="control-label"><strong>Observación</strong></label>
			<div>
				<textarea class="form-control" rows="5" disabled>{{$solicitud->observacion}}</textarea>
			</div>
		</div>
	endif --}}

</section>
