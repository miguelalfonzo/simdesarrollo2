<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
    <label class="control-label">Tipo de Actividad <span style="display: none;" id="esperaActividad"><i class="text-success fas fa-spinner fa-spin"></i></span></label>
    <div>
        <select data-placeholder="SELECCIONE LA ACTIVIDAD" class="form-control" name="actividad" id="actividad">
            @include( 'Dmkt.Register.Detail.activities' )
        </select>
    </div>
</div>