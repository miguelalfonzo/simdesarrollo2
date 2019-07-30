@if( ! is_null( $detalle->id_deposito ) )    
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label class="control-label">Deposito Tasa de Cambio</label>
        <div>
            <div class="input-group">
                <span class="input-group-addon">C</span>
                <input type="text" value="{{$detalle->tcc}}" class="form-control input-md" readonly>
                <span class="input-group-addon">V</span>
                <input type="text" value="{{$detalle->tcv}}" class="form-control input-md" readonly>
            </div>
        </div>
    </div>
@endif