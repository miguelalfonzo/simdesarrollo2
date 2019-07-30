<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
    <label class="control-label col-xs-12 col-sm-12 col-md-12 col-lg-12">Fecha de Creacion / Deposito</label>
    <div class="input-group col-xs-12 col-sm-5 col-md-5 col-lg-5 pull-left">
        <input type="text" class="form-control" maxlength="10" disabled value="{{ $solicitud->created_at_date }}">
    </div>
    <div class="input-group col-xs-12 col-sm-7 col-md-7 col-lg-7 pull-left">
    	<div class="input-group solicitud-date" style="width:100%">
        	<input type="text" class="form-control" maxlength="10" disabled value="{{$detalle->fecha_entrega}}">				
        </div>
        @if ( isset( $tipo_usuario ) && in_array( $tipo_usuario , array( SUP , GER_PROD, GER_PROM , GER_COM , GER_GER ) ) )
            <div class="input-group date edit-date" style="display: none">           
                <input type="text" name="fecha" id="fecha-value"class="form-control" maxlength="10" readonly="" value="{{$detalle->fecha_entrega}}" style="background-color: rgb(255, 255, 255);">
    			<span class="input-group-addon">
    				<i class="glyphicon glyphicon-calendar"></i>
    			</span>
            </div>
    		<span  class="input-group-btn solicitud-date">
    			<button class="btn btn-default" type="button" id="edit-date-activate">
    				<i class="glyphicon glyphicon-edit"></i>
    			</button>						
    		</span>
    		<span  class="input-group-btn  edit-date" style="display:none">
                <button class="btn btn-default" type="button" id="edit-date-deactivate">
    				<i class="glyphicon glyphicon-ban-circle"></i>
    			</button>
    		</span>
        @endif
    </div>
</div>