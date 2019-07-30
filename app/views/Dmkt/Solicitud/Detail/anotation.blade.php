@if ( ( ! is_null( $solicitud->anotacion ) || ! is_null( $solicitud->observacion ) ) || $politicStatus )
    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
        @if ( is_null( $solicitud->observacion ) )
            <label class="control-label" for="anotacion"><strong>Anotación</strong></label>
        @else
            <label class="control-label" for="observacion"><strong>Observación</strong></label>
        @endif
        @if ( is_null( $solicitud->observacion ) )
            @if ( isset( $politicStatus ) && $politicStatus )
                <textarea class="form-control" rows="5" name="anotacion" maxlength="500">{{ $solicitud->anotacion }}</textarea>
            @else
                <textarea class="form-control" rows="5" name="anotacion" maxlength="500" disabled style="resize:both">{{ $solicitud->anotacion }}</textarea>
            @endif
        @else
            <textarea class="form-control" rows="5" name="anotacion" maxlength="500" disabled style="resize:both">{{ $solicitud->observacion }}</textarea>
        @endif        
    </div>
@endif
