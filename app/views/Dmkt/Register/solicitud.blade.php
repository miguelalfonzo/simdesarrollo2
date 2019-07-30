@extends('template.main')
@section('solicitude')
<div class="content">
    <input type="hidden" id="state_view" value="{{isset($state) ? $state : PENDIENTE}}">
    <div class="page-header" style="margin-top: -3em;">
        @if( isset( $solicitud ) )
        <h3><i class="far fa-edit"></i> Editar Solicitud</h3>
        @else
        <h3><i class="far fa-file"></i> Nueva Solicitud @if(isset($solicitude) && $solicitude->blocked == 1)<small>LA SOLICITUD ESTA SIENDO EVALUADA</small>@endif</h3>
        @endif

        <!-- Button (Double) -->
        <div class="" style="float: right;margin-top: -3em;">
            <div style="text-align: center">
                @if( isset( $solicitud ) && $solicitud->blocked == 0 )
                    <button type="button" id="registrar" class="btn btn-success"><i class="far fa-save"></i> Actualizar</button>
                @else
                    <button type="button" id="registrar" class="btn btn-success"><i class="far fa-save"></i> Crear</button>
                @endif
                <a href="{{ URL::to('show_user') }}" class="btn btn-primary"><i class="fas fa-times-circle"></i> Regresar</a>    
            </div>
        </div>
    </div>
       {{--  @include('Dmkt.Register.detail') --}}
       @include('Dmkt.Register.detailNew')
</div>
@stop
