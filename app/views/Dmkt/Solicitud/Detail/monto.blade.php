<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
    <label class="control-label" for="monto">Monto Solicitado / Monto Aprobado</label>
    <div class="input-group">
        <span class="input-group-addon">{{$solicitud->detalle->typeMoney->simbolo}}</span>
        <input class="form-control input-md" value="{{ $detalle->monto_solicitado }}" type="text" readonly>
        <span class="input-group-addon" id="type-money">{{$solicitud->detalle->typeMoney->simbolo}}</span>
        @if ( $politicStatus )
            <input id="amount" value="{{$detalle->monto_actual}}" class="form-control input-md" name="monto" type="text">
        @else
            <input id="amount" value="{{$detalle->monto_actual}}" class="form-control input-md" name="monto" type="text" readonly>
        @endif   
    </div>
</div>