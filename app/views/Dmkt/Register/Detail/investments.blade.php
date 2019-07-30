@if ( isset( $solicitud ) )
    <option value=0 disabled style="display:none">SELECCIONE LA INVERSION</option>
@else
    <option value=0 disabled selected>SELECCIONE LA INVERSION</option>
@endif
@foreach( $investments as $investment )
    @if( isset( $solicitud ) )
        @if ( $solicitud->id_inversion == $investment['ID'] )
            <option selected value="{{$investment['ID']}}">{{$investment['NOMBRE']}}</option>
        @else
            <option value="{{$investment['ID']}}" disabled>{{$investment['NOMBRE']}}</option>
        @endif
    @else
        <option value="{{$investment['ID']}}">{{$investment['NOMBRE']}}</option>
    @endif
@endforeach