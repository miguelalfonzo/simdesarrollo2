@extends('template.main')
@section('solicitude')
    <div class="page-header">
        <h3>{{ $titulo }}</h3>
        @if( $export )
            <button type="button" id="maintenance-export" class="btn btn-primary" data-type="{{ $type }}">Exportar</button>
        @endif
    </div>
    <div id="{{ $type }}">
        @include( 'Maintenance.table')
    </div>
    <div>
        @if( is_null( $add ) )
            <input class="btn btn-primary maintenance-add" type="button" case="{{ $type }}" value="Agregar" style="display:none">
        @else
            <input class="btn btn-primary maintenance-add" type="button" case="{{ $type }}" value="Agregar">
        @endif
    </div>
    <script>
        $(document).on( 'ready' , function(){
            dataTable( '{{ $type }}' , null , 'registros' )
        })
    </script>
@stop