<div class="clearfix" style="border-bottom: 1px solid #E5E5E5"></div>
<h4 class="text-info">Ingrese el monto para registrar la solicitud de devolucion</h4>
<div class="row">
	<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <label class="control-label">Monto del Descuento {{ $solicitud->detalle->typeMoney->simbolo }}</label>
    	<div>
            <input type="text" id="monto_devolucion_inmediata" class="form-control" placeholder="Ingrese el monto">
        </div>
    </div>
</div>
