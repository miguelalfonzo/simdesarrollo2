<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row"> 
                <div class=" col-xs-12 col-sm-12 col-md-5">
                    Productos / Montos Asignados
                    @if( ! in_array( $solicitud->id_estado , array( PENDIENTE , CANCELADO ) ) )
                        / Fondo
                    @endif
                </div>
                <div class="col-xs-8 col-sm-10 col-md-5">
                    <span id="amount_error_families"></span>
                </div>
                <div class="col-xs-4 col-sm-2 col-md-2">
                    @if ( $politicStatus && isset( $tipo_usuario ) && in_array( $tipo_usuario , array( SUP , GER_PROD, GER_PROM , GER_COM , GER_GER  ) ) )
                        <label class="pull-right">
                            <input type="checkbox" name="modificacion-productos" id="is-product-change"> Modificar
                        </label>
                    @endif
                </div>
            </div>
        </div>
        <ul class="list-group" id="list-product">
            @foreach( $solicitud->products as $product )
                <li class="list-group-item">        
                    @if( $politicStatus )
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon" style="width:15%;">{{{ is_null( $product->marca ) ? '' : $product->marca->descripcion}}}</span>
                            @if ( in_array( $tipo_usuario , array( SUP , GER_PROD , GER_PROM , GER_COM , GER_GER ) ) )
                                <select name="fondo_producto[]" class="selectpicker form-control">
                                    @if( is_null( $product->id_fondo_marketing ) )
                                        <option selected disabled value="0">Seleccione el Fondo</option>
                                        @foreach( $product->getSubFondo( $tipo_usuario , $solicitud ) as $fondoMkt )
                                            <option value="{{ $fondoMkt->id . ',' . $fondoMkt->tipo }}">
                                                {{ $fondoMkt->detail_name . ' S/.' . $fondoMkt->saldo_disponible }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="{{ $product->id_fondo_marketing . ',' . $product->id_tipo_fondo_marketing }}" style="background-color:gold" selected>
                                            {{ $product->thisSubFondo->approval_product_name . ' ( Reservado S/. ' . $product->monto_asignado_soles . ' ) ' }}
                                        </option>    
                                        @foreach( $product->getSubFondo( $tipo_usuario , $solicitud ) as $fondoMkt )
                                            @if( $fondoMkt->id == $product->id_fondo_marketing )
                                                <option value="{{ $fondoMkt->id . ',' . $fondoMkt->tipo }}" style="background-color:#00FFFF">{{ $fondoMkt->detail_name . ' S/.' . $fondoMkt->saldo_disponible }}</option>
                                            @else   
                                                <option value="{{ $fondoMkt->id . ',' . $fondoMkt->tipo }}">{{ $fondoMkt->detail_name . ' S/.' . $fondoMkt->saldo_disponible }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            @else
                                <span class="input-group-addon" style="max-width:350px;overflow:hidden">
                                    {{ $product->thisSubFondo->approval_product_name . ' ( Reservado S/. ' . $product->monto_asignado_soles . ' )' }}
                                </span>
                                <input type="hidden" value="{{ $product->id_fondo_marketing . ',' . $product->id_tipo_fondo_marketing }}" name="fondo_producto[]">
                            @endif
                            <span class="input-group-addon">{{ $detalle->typemoney->simbolo }}</span>   
                            <input name="monto_producto[]" type="text" class="form-control text-right amount_families" value="{{ isset( $product->monto_asignado ) ? $product->monto_asignado : 
                            round( $detalle->monto_actual / count( $solicitud->products ) , 2 )}}" style="padding:0px;text-align:center">   
                        </div>
                    @else
                        {{{ $product->marca->descripcion or '-' }}}
                        <label class="label label-primary">
                            {{ $detalle->typemoney->simbolo . ( isset( $product->monto_asignado ) ? $product->monto_asignado : round( $detalle->monto_actual / count( $solicitud->products ) , 2 ) ) }}
                        </label>
                        @if( isset( $product->id_fondo_marketing ) )
                            <span class="badge">{{ $product->thisSubFondo->subCategoria->descripcion . ' | ' . $product->thisSubFondo->marca->descripcion }}</span>    
                        @endif
                    @endif     
                    <input type="hidden" name="producto[]" value="{{ $product->id }}">
                </li>
            @endforeach
        </ul>
        @if ( $politicStatus && isset( $tipo_usuario ) && in_array( $tipo_usuario , array( SUP , GER_PROD , GER_PROM , GER_COM , GER_GER ) ) )
            <ul class="list-group" id="list-product2" style="display: none">
                @foreach( $solicitud->products as $product )
                    <li class="list-group-item">        
                        @if( $politicStatus )
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon" style="width:15%;">{{{ is_null( $product->marca ) ? '' : $product->marca->descripcion}}}</span>
                                @if ( in_array( $tipo_usuario , array( SUP , GER_PROD , GER_PROM , GER_COM , GER_GER ) ) )
                                    <select name="fondo_producto[]" class="selectpicker form-control">
                                        @if ( is_null( $product->id_fondo_marketing ) )
                                            <option selected disabled value="0">Seleccione el Fondo</option>
                                            @foreach( $product->getSubFondo( $tipo_usuario , $solicitud ) as $fondoMkt )
                                                <option value="{{ $fondoMkt->id . ',' . $fondoMkt->tipo }}">
                                                    {{ $fondoMkt->detail_name . ' S/.' . $fondoMkt->saldo_disponible }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="{{ $product->id_fondo_marketing . ',' . $product->id_tipo_fondo_marketing }}" style="background-color:gold" selected>
                                                {{ $product->thisSubFondo->approval_product_name . ' ( Reservado S/. ' . $product->monto_asignado . ' ) ' }}
                                            </option>    
                                            @foreach( $product->getSubFondo( $tipo_usuario , $solicitud ) as $fondoMkt )
                                                @if ( $fondoMkt->id == $product->id_fondo_marketing )
                                                    <option value="{{ $fondoMkt->id . ',' . $fondoMkt->tipo }}" style="background-color:#00FFFF">{{$fondoMkt->detail_name . ' S/.' . $fondoMkt->saldo_disponible }}</option>
                                                @else   
                                                    <option value="{{ $fondoMkt->id . ',' . $fondoMkt->tipo }}">{{$fondoMkt->detail_name . ' S/.' . $fondoMkt->saldo_disponible }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                @else
                                    <span class="input-group-addon" style="max-width:350px;overflow:hidden">
                                        {{ $product->thisSubFondo->approval_product_name . ' ( Reservado S/. ' . $product->monto_asignado . ' )' }}
                                    </span>
                                    <input type="hidden" value="{{ $product->id_fondo_marketing . ',' . $product->id_tipo_fondo_marketing }}" name="fondo_producto[]">
                                @endif
                                <span class="input-group-addon">{{ $detalle->typemoney->simbolo }}</span>
                                <input name="monto_producto[]" type="text" class="form-control text-right amount_families2" value="{{ isset( $product->monto_asignado ) ? $product->monto_asignado : 
                                round( $detalle->monto_actual / count( $solicitud->products ) , 2 )}}" style="padding:0px;text-align:center">
                                <span class="input-group-btn">
                                     <button type="button" class="btn btn-default btn-remove-family" >
                                        <span class="glyphicon glyphicon-remove"></span>
                                    </button>   
                                </span>
                            </div>
                        @else
                            {{{ $product->marca->descripcion or '-' }}}
                            <label class="label label-primary">
                                {{ $detalle->typemoney->simbolo . ( isset( $product->monto_asignado ) ? $product->monto_asignado : round( $detalle->monto_actual / count( $solicitud->products ) , 2 ) ) }}
                            </label>
                            @if( isset( $product->id_fondo_marketing ) )
                                <span class="badge">{{ $product->thisSubFondo->subCategoria->descripcion . ' | ' . $product->thisSubFondo->marca->descripcion }}</span>    
                            @endif
                        @endif
                        <input type="hidden" name="producto[]" class="producto_value" value="{{ $product->id_producto  }}">
                    </li>
                @endforeach
            </ul>
            <button type="button" style="display:none" id="open_modal_add_product" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#approval-product-modal">
                Agregar Producto
            </button>
        @endif
    </div>
</div>
@if ( $politicStatus && isset( $tipo_usuario ) && in_array( $tipo_usuario , array( SUP , GER_PROD , GER_PROM , GER_COM , GER_GER ) ) )
    @include('Dmkt.Solicitud.Section.modal-select-producto')
    <script>
        function productChange( element )
        {
            if( element.checked ) 
            { 
                $("#open_modal_add_product").show();
                $("#list-product").hide();
                $('#list-product :input').attr('disabled', true);
                $('#list-product2 :input').removeAttr( 'disabled' );
                $("#list-product2").show();
            }
            else
            {
                $("#open_modal_add_product").hide();
                $("#list-product2").hide();
                $('#list-product2 :input').attr('disabled', true);
                $('#list-product :input').removeAttr('disabled');
                $("#list-product").show();
            }
            verifySum( 0 , 0 );
        }
        $("#is-product-change").change( function()
        {
            productChange( this )
        });
        $( document ).ready( function()
        {
            productChange( document.getElementById( 'is-product-change' ) );
        });
    </script>
@endif