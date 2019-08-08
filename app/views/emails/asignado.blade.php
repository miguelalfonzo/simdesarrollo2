@if ( ! is_null( $solicitud->id_user_assign ) )
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label class="control-label"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Asignado a</label>
        <div class="input-group">
            <span class="input-group-addon">{{ $solicitud->assignedTo->type }}</span>
            <input type="text" class="form-control input-md solicitud-resp" readonly value="{{ $solicitud->assignedTo->personal->full_name }}">
            @if ( isset( $tipo_usuario ) && in_array( $tipo_usuario , array( SUP , GER_PROD, GER_PROM , GER_COM , GER_GER  ) ) )    
				<span class="input-group-btn edit-resp" style="display: none">
	                <button class="btn btn-default" type="button" id="edit-resp-deactivate">
						<i class="glyphicon glyphicon-ban-circle"></i>
					</button>
				</span>
			@endif
        </div>
    </div>
@endif