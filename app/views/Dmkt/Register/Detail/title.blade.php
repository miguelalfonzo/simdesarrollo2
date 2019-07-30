<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
    <label class="control-label" for="titulo">Nombre Solicitud</label>
    <div>
        <input class="form-control input-md" name="titulo" type="text"
        value="{{isset($solicitud->titulo)? $solicitud->titulo : null }}">
    </div>
</div>