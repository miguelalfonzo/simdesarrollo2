@if( ! is_null( $solicitud->approvedHistory ) )    
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label class="control-label"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Aprobado por</label>
        <div>
            <div class="input-group">
                <span class="input-group-addon">{{ $solicitud->approvedHistory->user->type }}</span>  
                <input type="text" class="form-control input-md"  readonly value="{{ $solicitud->approvedHistory->user->personal->full_name }}">
            </div>
        </div>
    </div>
@endif