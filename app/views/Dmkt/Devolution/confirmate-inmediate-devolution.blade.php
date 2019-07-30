<div class="modal-header">
	<h4 class="modal-title">Confirmacion de la operacion de devolucion</h4>
</div>
<div style="border-bottom: 1px solid rgb(229, 229, 229);" class="clearfix"></div>
<h4 class="text-info">Se requiere su confirmacion de la siguiente operacion de devolucion: N°
	<span class="label label-info">{{ htmlspecialchars( $numero_operacion ) }}</span> , con monto de 
	<span class="label label-info">{{ $solicitud->detalle->typeMoney->simbolo . $devolucion }}</span> por parte del empleador
	<span class="label label-info">{{ $solicitud->assignedTo->personal->full_name }}</span> con cargo de
	<span class="label label-info">{{ $solicitud->assignedTo->userType->descripcion }}</span>. Confirmar si se realizo la operacion indicada.
</h4>