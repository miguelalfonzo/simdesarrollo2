<form id="form-register-solicitude" class="" method="post" enctype="multipart/form-data" action="registrar-solicitud">
    {{ Form::token() }}

    @if( isset( $solicitud ) )
        <input value="{{$solicitud->id}}" name="idsolicitud" type="hidden">
    @endif
    
    <!-- MOTIVO DE LA SOLICITUD -->
    @include('Dmkt.Register.Detail.reason')

    <!-- BUSQUEDA DE CLIENTES -->
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label class="control-label">Clientes</label>
        <div class="scrollable-dropdown-menu">
            <input class="form-control input-md cliente-seeker" type="text" style="display:inline">
        </div>
    </div>

    <!-- TIPO DE INVERSION DE LA SOLICITUD -->
    @include('Dmkt.Register.Detail.investment')

    <!-- TIPO DE ACTIVIDAD DE LA SOLICITUD-->
    @include('Dmkt.Register.Detail.activity')

    <!-- NOMBRE DE LA SOLICITUD -->
    @include('Dmkt.Register.Detail.title')
    
    <!-- TIPO DE MONEDA y MONTO -->
    @include('Dmkt.Register.Detail.currencyamount')
    
    <!-- TIPO DE PAGO -->
    @include('Dmkt.Register.Detail.payment')

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
                <input type="text" name="fecha" class="form-control" maxlength="10" readonly
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
                        @if( isset( $solicitud ) && $solicitud->id_user_assign == $rep->user_id )
                            <option value="{{ $rep->user_id }}" selected>{{ $rep->full_name }}</option>
                        @else
                            <option value="{{ $rep->user_id }}">{{ $rep->full_name }}</option>
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

    <!-- Button (Double) -->
    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: 20px">
        <div style="text-align: center">
            @if( isset( $solicitud ) && $solicitud->blocked == 0 )
                <button type="button" id="registrar" class="btn btn-success">Actualizar</button>
            @else
                <button type="button" id="registrar" class="btn btn-success">Crear</button>
            @endif
            <a href="{{ URL::to('show_user') }}" class="btn btn-primary">Regresar</a>    
        </div>
    </div>
</form>
