<div class="form-group col-sm-12 col-md-12 col-lg-12" style="margin-top: 20px">
    <div class="col-sm-12 col-md-12 col-lg-12" style="text-align: center">  
        @if ( $politicStatus ) 
            @if( $regularizationStatus[ status ] === ok )
                <a id="search_responsable" class="btn btn-success ladda-button" data-style="slide-up">
                    Aceptar
                </a>
                @if ( $tipo_usuario === SUP )
                    <a id="derivar_solicitud" class="btn btn-warning ladda-button" data-style="slide-up">
                        Derivar
                    </a>
                @endif
            @endif
            <a id="deny_solicitude" class="btn btn-danger">
                Rechazar
            </a>
        @endif

        @if ( $solicitud->id_user_assign == Auth::user()->id ) 
            @if ( $solicitud->id_estado == GASTO_HABILITADO )
                <a id="finish-expense" class="btn btn-success">Terminar</a>
            @elseif( $solicitud->id_estado == ENTREGADO && $solicitud->devolutions()->where( 'id_estado_devolucion' , DEVOLUCION_POR_REALIZAR )->get()->count() !== 0 )
                <a class="btn btn-info get-devolution-info" data-type="do-inmediate-devolution">Registro de la Devolución</a>
            @endif
        @endif
        @if ( Auth::user()->type == CONT )
            @if ( in_array( $solicitud->idtiposolicitud , array( SOL_REP , REEMBOLSO ) ) )            
                @if($solicitud->id_estado == APROBADO )
                    <a id="enable-deposit" class="btn btn-success">Confirmar</a>
                @endif
            @endif
            @if( $solicitud->id_estado == DEPOSITADO )
                <a id="seat-solicitude" data-loading-text="Generando..." class="btn btn-success">Generar Asiento</a>
            @elseif( $solicitud->id_estado == ENTREGADO )
                @if( $solicitud->idtiposolicitud != REEMBOLSO )
                    @if( $solicitud->pendingRefund->count() == 0 )
                        @if( $solicitud->pendingPayrollRefund->count() == 0 )
                            <a id="confirm-payroll-discount" class="btn btn-info">Registro de Descuento por Planilla</a>
                        @endif
                        <a class="btn btn-info get-devolution-info" data-type="register-inmediate-devolution">Solicitud de Descuento Inmediato</a>
                    @endif
                @endif
                <a id="finish-expense-record" class="btn btn-success">Terminar Registro de Gasto</a>    
            @endif
        @endif
        <a href="{{URL::to('show_user')}}" class="btn btn-primary">
            Regresar
        </a>
    </div>
</div>