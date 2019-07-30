@if(!is_null($solicitud->detalle->id_deposito) )    
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label class="control-label" for="depositado">
            Depositado
        </label>
        <div>
            <div class="input-group">
                <span class="input-group-addon">{{$solicitud->detalle->deposit->account->typeMoney->simbolo}}</span>
                <input id="depositado" type="text" value="{{$solicitud->detalle->deposit->total}}" class="form-control input-md" readonly>
            </div>
        </div>
    </div>
@endif