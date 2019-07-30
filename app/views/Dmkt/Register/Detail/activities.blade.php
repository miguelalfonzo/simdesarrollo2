@if ( isset( $solicitud) )
    <option value=0 disabled>SELECCIONE LA ACTIVIDAD</option>
@else
    <option value=0 disabled selected>SELECCIONE LA ACTIVIDAD</option>
@endif            
@foreach( $activities as $activity )
    @if ( isset( $solicitud ) )
        @if ( $solicitud->id_actividad == $activity['ID'] )
            <option selected value="{{ $activity['ID']}}" >{{ $activity['NOMBRE'] }}</option>
        @else
            <option value="{{ $activity['ID'] }}" disabled>{{ $activity['NOMBRE'] }}</option>
        @endif
    @else
        <option value="{{ $activity['ID'] }}">{{ $activity['NOMBRE'] }}</option>
    @endif
@endforeach