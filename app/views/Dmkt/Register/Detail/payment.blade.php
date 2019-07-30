<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
    <label class="control-label">Tipo de Entrega</label>
    <select name="pago" class="form-control">
        <!--@foreach( $payments as $payment )
            @if( isset( $solicitud ) && $solicitud->detalle->id_pago == $payment->id )
                <option value="{{$payment->id}}" selected>{{$payment->nombre}}</option>
            @else
                <option value="{{$payment->id}}">{{$payment->nombre}}</option>
            @endif
        @endforeach-->
        <option value="1">TRANSFERENCIA</option>
    </select>
</div>

<!-- descomentar -->