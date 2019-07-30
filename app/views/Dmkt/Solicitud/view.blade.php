@extends('template.main')
@section('solicitude')
<div class="content">
    <ul class="nav nav-tabs" role="tablist">
        @if ( ( $solicitud->id_user_assign == Auth::user()->id  && ! is_null( $solicitud->expenseHistory ) ) || ( Auth::user()->type == CONT && $solicitud->id_estado == ENTREGADO )  )
            <li>
                 @include('Dmkt.Solicitud.tabSolicitud')
            </li>
            <li class="active">
                <a href="#document" role="tab" data-toggle="tab">
                    <icon class="fa fa-home"></icon>
                    Documentos
                </a>
            </li>
        @else
            <li class="active">
                @include('Dmkt.Solicitud.tabSolicitud')
            </li>    
        @endif
    </ul>
    <div class="tab-content">
        @if ( ( $solicitud->id_user_assign == Auth::user()->id  && ! is_null( $solicitud->expenseHistory ) ) || ( Auth::user()->type == CONT && $solicitud->id_estado == ENTREGADO )  )
            <div class="tab-pane fade" id="solicitud" style="margin-top:20px">    
        @else
            <div class="tab-pane fade active in" id="solicitud" style="margin-top:20px">
        @endif

            @if ( $solicitud->status == BLOCKED )
                <h4 style="color:darkred; padding-left:10px; margin-top:20px; margin-bottom:20px">LA SOLICITUD ESTA SIENDO EVALUADA</h4>
            @endif

            @if( $politicStatus && $regularizationStatus[ status ] !== ok )

                <div class="well well-sm">
                    @if( $regularizationStatus[ status ] === warning )
                        <h3 class="text-warning text-center"><b>No puede realizar la aprobacion de la solicitud</b></h3>
                        <h5 class="text-warning text-center"><b>{{ $regularizationStatus[ description ] }}</b></h5>
                    @else
                        <h3 class="text-danger text-center"><b>Error de Sistema comunicarse con Informatica</b></h5>
                    @endif
                </div>
            @endif

            <form id="form_make_activity" method="post">
                {{Form::token()}}
                <input name="idsolicitud" type="hidden" value="{{$solicitud->id}}">
                <input name="token" type="hidden" value="{{$solicitud->token}}">
                <!-- DETALLE DE LA SOLICITUD -->
                @if ( in_array( $solicitud->idtiposolicitud , array( SOL_REP , REEMBOLSO ) ) )
                    @include('Dmkt.Solicitud.Representante.detail')
                @elseif ( $solicitud->idtiposolicitud == SOL_INST )
                    @include('Dmkt.Solicitud.Institucional.detail')
                @endif
                <!-- ASIENTO DE ANTICIPO -->
                @if ( Auth::user()->type == CONT && $solicitud->id_estado == DEPOSITADO )
                    @include('template.Seat.advance_table')
                @endif
                <!-- Modal Deposito -->
                @include('template.Modals.deposit-min')
            </form>
        </div>
        @if ( ( $solicitud->id_user_assign == Auth::user()->id  && ! is_null( $solicitud->expenseHistory ) ) || ( Auth::user()->type == CONT && $solicitud->id_estado == ENTREGADO )  )
            <div class="tab-pane fade active in" id="document" style="margin-top:20px; margin-bottom:20px">
        @else
            <div class="tab-pane fade" id="document" style="margin-top:20px; margin-bottom:20px">
        @endif
            @include('Dmkt.Solicitud.Section.gasto')
        </div>
        <!-- Button (Double) -->
        @include('Dmkt.Solicitud.Detail.buttons')
    </div>
</div>
@stop