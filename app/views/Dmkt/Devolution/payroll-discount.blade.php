<div class="clearfix" style="border-bottom: 1px solid #E5E5E5"></div>
<h4 class="text-warning">Ingrese el periodo y el monto del descuento por Planilla</h4>
<div class="row">
	<!-- DEVOLUCION -->

	<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <label class="control-label">Periodo del Descuento</label>
    	<div>
            <input type="text" id="periodo" class="form-control date" size=7 placeholder="Seleccione el Periodo MM-YYYY" readonly>
        </div>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <label class="control-label">Monto del Descuento {{ $solicitud->detalle->typeMoney->simbolo }}</label>
    	<div>
            <input type="text" id="monto_descuento_planilla" class="form-control" placeholder="Ingrese el monto">
        </div>
    </div>
</div>
