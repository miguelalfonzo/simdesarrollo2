<section class="row" style="padding:0.3em 1em" style="border:1px solid black">
    <!-- TITULO -->
    <div class="page-header">
        <h2>#{{ $solicitud->id}} {{$solicitud->titulo}} <span class="label label-default">{{$solicitud->activity->nombre}}</span></h2>
    </div>
    
    <!-- MOTIVO  -->
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label class="control-label">Motivo</label>
        <input type="text" class="form-control" value="{{$solicitud->typeSolicitude->nombre}}" readonly>
    </div>

    <!-- INVERSION -->
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label class="control-label">Tipo de Inversión</label>
        <input type="text" class="form-control input-md" value="{{$solicitud->investment->nombre}}" readonly>
    </div>

    <!-- MONTO -->
    @include('emails.monto')

    <!-- TIPO DE PAGO -->
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4" >
        <label class="control-label">Tipo de Entrega</label>
        @if( isset( $payments ) )
            <input type="text" value="TRANSFERENCIA" readonly="" class="form-control">
        @else
            <select name="pago" style="display:none"><option value="{{ $detalle->id_pago }}" selected></option></select>
            <input class="form-control" value="{{ $detalle->typePayment->nombre }}" disabled>
        @endif
    </div>

    <!-- PAGO CHEQUE => RUC -->
    @if ( $politicStatus )
        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <label class="control-label">Ruc</label>
            <input type="text" name="ruc" class="form-control input-md" value="{{ $detalle->num_ruc }}" maxlength="11" readonly>
        </div>
    @endif
    
    <!-- FECHA DE CREACION / FECHA DE ENTREGA -->
    @include('emails.fecha')

    <!-- Solicitante -->
    @include('Dmkt.Solicitud.Detail.solicitante')

    <!-- Asignado a -->
    @include('emails.asignado')

    <!-- Aceptador Por -->
    @include('Dmkt.Solicitud.Detail.accepted')

    <!-- Fondo Contable -->
    @include('Dmkt.Solicitud.Detail.fondo')

    <!-- Depositado -->
    @include('Dmkt.Solicitud.Detail.depositado')

    <!-- N° de Operacion relacionada al deposito -->
    @if( Auth::user()->type == TESORERIA && !is_null( $detalle->id_deposito ) )
        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <label class="control-label">Deposito Nº de Operación</label>
            <div>
                <input type="text" class="form-control" value="{{$detalle->deposit->num_transferencia}}" disabled>
            </div>
        </div>
    @endif

    
    <!-- Tasa de Cambio del Dia del Deposito -->
    @include('Dmkt.Solicitud.Detail.tasa')

    @if( ! is_null( $detalle->numero_operacion_devolucion ) )
        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <label class="control-label">Devolucion Nº de Operación</label>
            <div>
                <input type="text" class="form-control" value="{{ $detalle->numero_operacion_devolucion }}" disabled>
            </div>
        </div>
    @endif
    
    <!-- Fecha de Descuento al Responsable del Gasto -->
    @include('Dmkt.Solicitud.Detail.discount')

    <div class="clearfix"></div>
    
    <!-- PRODUCTOS -->
    @include('emails.products')

    <!-- CLIENTES -->
    @include('emails.clients')
        
    <!-- Lista de Devoluciones -->
    @include( 'Dmkt.Solicitud.Detail.devolucion2')

    <div class="clearfix"></div>
    <!-- Description Solicitude -->
    @if ( ! is_null( $solicitud->descripcion ) && ! empty( trim( $solicitud->descripcion ) ) )
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label class="control-label">
                Descripcion de la Solicitud
            </label>
            <textarea disabled class="form-control" rows="5" style="resize:both" readonly>{{$solicitud->descripcion}}</textarea>
        </div>
    @endif

    <!-- Observation
    @include('emails.anotation')-->

</section>
