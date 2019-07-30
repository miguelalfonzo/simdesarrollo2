<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
    <label class="control-label">Motivo</label>
    <div>
        <select class="form-control" name="motivo">
            @foreach( $reasons as $reason )
                @if( isset( $solicitud ) && $solicitud->idtiposolicitud == $reason->id)
                    <option selected value="{{$reason->id}}">{{$reason->nombre}}</option>
                @else
                    <option value="{{$reason->id}}">{{$reason->nombre}}</option>
                @endif
            @endforeach
        </select>
    </div>
</div>