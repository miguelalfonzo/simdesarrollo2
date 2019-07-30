<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
    <div class="panel panel-default">
        <div class="panel-heading">Clientes
             @if ( isset( $tipo_usuario ) && in_array( $tipo_usuario , array( SUP , GER_PROD, GER_PROM , GER_COM , GER_GER ) ) )
                <label class="pull-right">
                    <input type="checkbox" name="modificacion-clientes" id="is-client-change"> Modificar
                </label>
            @endif
        </div>
        <ul class="list-group" id="list-client">                 
            @foreach( $solicitud->clients as $client )
                <li class="list-group-item">
                    @if ( is_null( $client->id_cliente) )
                       No hay cliente Asignado
                    @else
                        {{ $client->{$client->clientType->relacion}->full_name }}
                    @endif
                    <span class="badge">{{$client->clientType->descripcion}}</span>
                </li>
            @endforeach
        </ul>
        @if ( isset( $tipo_usuario ) && in_array( $tipo_usuario , array( SUP , GER_PROD, GER_PROM , GER_COM , GER_GER ) ) )
            <ul class="list-group" id="clientes" style="display: none">                 
                @foreach( $solicitud->clients as $client )
                    @include( 'Seeker.client' ,  
                        [ 
                            'label' => $client->{$client->clientType->relacion}->full_name  ,
                            'type'  => $client->clientType->descripcion ,
                            'value' => $client->id_cliente ,
                            'id_tipo_cliente' => $client->id_tipo_cliente
                        ])    
                @endforeach
            </ul>
            <button type="button" style="display:none" id="open_modal_add_client" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#approval-client-modal">
                Agregar Clientes
            </button>
        @endif
    </div>
</div>
@if ( $politicStatus && isset( $tipo_usuario ) && in_array( $tipo_usuario , array( SUP , GER_PROD , GER_PROM , GER_COM , GER_GER ) ) )
    @include('Dmkt.Solicitud.Section.modal-select-client')
    <script>
        function clientChange( element )
        {
            if( element.checked ) 
            {        
                $("#open_modal_add_client").show();
                $("#list-client").hide();
                //$('#clientes :input').removeAttr('disabled');
                $("#clientes").show();
            }
            else
            {
                $("#open_modal_add_client").hide();
                $("#clientes").hide();
                //$('#clientes :input').attr('disabled', true);
                $("#list-client").show();
            }
        }
        $("#is-client-change").change( function()
        {
            clientChange( this )
        });
        $( document ).ready( function()
        {
            clientChange( document.getElementById( 'is-client-change' ) );
        });
    </script>
@endif