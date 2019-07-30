<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
    <label class="col-xs12 col-sm-12 col-md-12 col-lg-12 control-label" for="titulo">
        Titulo
    </label>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="input-group">
            <span class="input-group-addon">{{$solicitud->activity->nombre}}</span>
            <input id="titulo" class="form-control input-md" type="text"
            value="{{$solicitud->titulo}}" readonly>
        </div>
    </div>
</div>