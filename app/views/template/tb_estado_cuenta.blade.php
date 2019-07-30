@extends('template.main')
@section('solicitude')
    <h3>Movimientos</h3>
    <div class="form-group col-sm-2 col-md-2">
        <select class="form-control filter">
            <option value="0" selected>Seleccione el Fondo</option>
            @foreach( $fondosMkt as $fondoMkt )
                <option value="{{ $fondoMkt['ID'] }}">{{ $fondoMkt['DESCRIPCION'] }}</option>
            @endforeach
        </select>
    </div>
    <div id="movimientos"></div>
    @if ( Auth::user()->type == TESORERIA )
        <div class="input-group">
            <span class="input-group-addon">S/.</span>
            <input type="text" class="estado-cuenta-deposito form-control input-md" readonly>
            <span class="input-group-addon">$</span>
            <input type="text" class="estado-cuenta-deposito form-control input-md" readonly>
        </div>
    @endif
    <script>
    $( document ).ready( function()
    {    
        GBREPORTS.changeDateRange('M');
        listTable( 'movimientos' , null );
    });
    </script>
@stop