<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
    <label class="control-label">Monto Solicitado</label>
    <div class="clearfix"></div>
    <div class="input-group col-xs-3 col-sm-3 col-md-3 col-lg-3 pull-left">
        <select name="moneda" class="form-control">
            @foreach( $currencies as $currency )
                @if ( isset( $solicitud ) && $solicitud->detalle->id_moneda == $currency->id )
                    <option value="{{$currency->id}}" selected>{{$currency->simbolo}}</option>
                @else
                    <option value="{{$currency->id}}">{{$currency->simbolo}}</option>
                @endif
            @endforeach 
        </select>
    </div>
    <div class="input-group col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left">
        <input class="form-control input-md" name="monto" type="text" value="{{ isset( $detalle ) ? $detalle->monto_actual : null }}">
    </div>
</div>