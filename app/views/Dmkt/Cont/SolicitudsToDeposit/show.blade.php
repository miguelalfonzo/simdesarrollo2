@extends('template.main')
@section('solicitude')
	<div class="page-header">
		<h3>Reporte de Solicitudes a Depositar</h3>
    </div>
    <div class="form-group col-xs-12 col-md-12 col-sm-12 col-lg-12">
	    <button type="button" class="btn btn-default dropdown-toggle ladda-button" data-style="expand-left" data-toggle="dropdown">
	        <span class="ladda-label"></span> Exportar solicitudes a depositar <span class="caret"></span>
	    </button>
        <ul class="dropdown-menu" role="menu">
	        <li><a href="{{ URL::to('export/solicitudToDeposit-pdf') }}" target="_blank">PDF</a></li>
	        <li class="divider" style="display: block;"></li>
	        <li><a href="{{ URL::to('export/solicitudToDeposit-excel') }}">Excel</a></li>
	    </ul>
	</div>
	@include( 'Dmkt.Cont.SolicitudsToDeposit.table' )
@endsection
