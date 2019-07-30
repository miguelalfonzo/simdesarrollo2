<style> 
    .select2-results .select2-disabled,  .select2-results__option[aria-disabled=true] {
    display: none;
    }
</style>
<form id="form-register-solicitude" class="" method="post" enctype="multipart/form-data" action="registrar-solicitud">
    {{ Form::token() }}

    @if( isset( $solicitud ) )
        <input value="{{$solicitud->id}}" name="idsolicitud" type="hidden">
    @endif
    
    <!-- MOTIVO DE LA SOLICITUD -->
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label class="control-label">Motivo</label>
        <div>
            <select class="form-control chosen-select" name="motivo" id="motivo">
                @foreach( $reasons as $reason )
                    @if( isset( $solicitud ) && $solicitud->idtiposolicitud == $reason['ID'])
                        <option selected value="{{$reason['ID']}}">{{$reason['NOMBRE']}}</option>
                    @else
                        <option value="{{$reason['ID']}}">{{$reason['NOMBRE']}}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>

    <!-- BUSQUEDA DE CLIENTES -->
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label class="control-label">Clientes</label>
        <div class="scrollable-dropdown-menu">
            <input class="form-control input-md cliente-seeker" type="text" style="display:inline">
        </div>
    </div>

    <!-- TIPO DE INVERSION DE LA SOLICITUD -->
    @include('Dmkt.Register.Detail.investment')
 

    @include('Dmkt.Register.Detail.activity')

    <!-- TIPO DE ACTIVIDAD DE LA SOLICITUD-->
    
    <!-- NOMBRE DE LA SOLICITUD -->
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label class="control-label" for="titulo">Nombre Solicitud</label>
        <div>
            <input class="form-control input-md" name="titulo" id="titulo" type="text"
            value="{{isset($solicitud->titulo)? $solicitud->titulo : null }}">
        </div>
    </div>

    <!-- TIPO DE MONEDA y MONTO -->
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label class="control-label">Monto Solicitado</label>
        <div class="clearfix"></div>
        <div class="input-group col-xs-3 col-sm-3 col-md-3 col-lg-3 pull-left">
            <select name="moneda" class="form-control chosen-select">
                @foreach( $currencies as $currency )
                    @if ( isset( $solicitud ) && $solicitud->id_moneda == $currency['ID'] )
                        <option value="{{$currency['ID']}}" selected>{{$currency['SIMBOLO']}}</option>
                    @else
                        <option value="{{$currency['ID']}}">{{$currency['SIMBOLO']}}</option>
                    @endif
                @endforeach 
            </select>
        </div>
        <div class="input-group col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left">
            <input class="form-control input-md" name="monto" id="monto" type="text" value="{{ isset( $detalle ) ? $detalle->monto_actual : null }}">
        </div>
    </div>

     <!-- TIPO DE PAGO -->
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label class="control-label">Tipo de Entrega</label>
        <select name="pago" class="form-control">
            <!--@foreach( $payments as $payment )
                @if( isset( $solicitud ) && $solicitud->detalle->id_pago == $payment['ID'] )
                    <option value="{{$payment['ID']}}" selected>{{$payment['NOMBRE']}}</option>
                @else
                    <option value="{{$payment['ID']}}">{{$payment['NOMBRE']}}</option>
                @endif
            @endforeach-->
            <option value="1">TRANSFERENCIA</option>
        </select>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label class="control-label" for="ruc">Ruc</label>
        @if( isset( $solicitud ) )
            <input class="form-control input-md" maxlength="11" name="ruc" type="text" value="{{ $detalle->num_ruc }}">
        @else
            <input class="form-control input-md" maxlength="11" name="ruc" type="text">
        @endif    
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label class="control-label" for="fecha">Fecha de Entrega de Dinero</label>
        <div>
            <div class="input-group date">
                <span class="input-group-addon">
                    <i class="glyphicon glyphicon-calendar"></i>
                </span>
                <input type="text" name="fecha" id="fecha" class="form-control" maxlength="10" readonly
                value="{{ isset( $solicitud ) ? $detalle->fecha_entrega : null }}">
            </div>
        </div>
    </div>

     @if ( isset( $reps ) )
        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <label class="control-label" for="fecha">Asignar A</label>
            <div>
                <select name="responsable" class="form-control">
                    <option value="0" selected disabled>Seleccione el Empleado Responsable</option>    
                    @foreach( $reps as $rep )
                        @if( isset( $solicitud ) && $solicitud->id_user_assign == $rep['user_id'])
                            <option value="{{ $rep['user_id']}}" selected>{{ $rep['nombres'] }} {{ $rep['apellidos'] }}</option>
                        @else
                            <option value="{{ $rep['user_id'] }}">{{$rep['nombres']}} {{ $rep['apellidos'] }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
    @endif    

    <div style="clear:both">
        <!-- PRODUCTOS -->
        @include('Dmkt.Register.Detail.productos')

        <!-- LISTA DE CLIENTES -->
        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <label class="control-label" for="ruc">Lista de Clientes</label>
            <div>
                <ul class="list-group" id="clientes">
                    @if ( isset( $solicitud ) )
                        @foreach ( $solicitud->clients as $client )
                            @include( 'Seeker.client' ,  
                            [ 
                                'label' => $client->{$client->clientType->relacion}->full_name  ,
                                'type'  => $client->clientType->descripcion ,
                                'value' => $client->id_cliente ,
                                'id_tipo_cliente' => $client->id_tipo_cliente
                            ])
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>

    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: 10px">
        <div class="form-group">
            <label class="control-label" for="descripcion">Descripcion de la Solicitud</label>
            <div>
                <textarea class="form-control" name="descripcion" maxlength="500" placeholder="(maximo 500 caracteres)">{{ isset( $solicitud->descripcion ) ? $solicitud->descripcion : null }}</textarea>
            </div>
        </div>
    </div>
 
</form>

