<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
    <label class="control-label" for="textinput"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Solicitante</label>
    <div class="input-group">
        @if( ! is_null( $solicitud->createdBy ) )
            <span class="input-group-addon">{{$solicitud->createdBy->type}}</span>
            <input id="textinput" class="form-control" name="titulo" type="text" readonly value="{{ $solicitud->createdBy->personal->full_name }}">
        @endif
    </div>
</div>