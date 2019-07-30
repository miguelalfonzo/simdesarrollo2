<select class="form-control input-sm" style="width:100%">
	@foreach( $datos as $dato )
		@if ( isset( $val ) && $dato->$key == $val )
			<option value="{{$dato->id}}" selected style="background-color:#A9E2F3">{{$dato->$key}}</option>
		@else
			<option value="{{$dato->id}}">{{$dato->$key}}</option>
		@endif
	@endforeach
</select>