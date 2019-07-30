<div class="row">
	<div class="clearfix"></div>
	
	<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <label class="control-label">Periodo del Descuento</label>
    	<div>
            <input type="text" id="periodo" class="form-control date" size=7 placeholder="Seleccione el Periodo MM-YYYY" readonly>
        </div>
    </div>

	<!-- N° de Operacion del Deposito Actual -->
	<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
	    <label class="control-label">N° de Tranferencia del Deposito</label>
	    <div>
	        <input type="text" class="form-control input-md" value="{{$solicitud->detalle->deposit->num_transferencia}}" disabled>
	    </div>
	</div>

	<!-- N° de Operacion del Deposito Actual -->
	<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
	    <label class="control-label">Monto depositado</label>
	    <div class="input-group">
            <span class="input-group-addon">{{$solicitud->detalle->deposit->account->typeMoney->simbolo}}</span>
            <input type="text" class="form-control input-md" value="{{$solicitud->detalle->deposit->total}}" disabled>
        </div>
	</div>

</div>
