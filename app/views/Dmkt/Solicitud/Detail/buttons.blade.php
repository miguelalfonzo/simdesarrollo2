<div class="form-group col-sm-12 col-md-12 col-lg-12" style="margin-top: 5px">
    <div class="col-sm-12 col-md-12 col-lg-12" style="text-align: right">  
        @if ( $politicStatus ) 
            @if( $regularizationStatus[ status ] === ok )
                <a id="search_responsable" class="btn btn-success ladda-button" data-style="slide-up">
                    <i class="fas fa-check"></i> Aceptar
                </a>
                @if ( $tipo_usuario === SUP )
                    <a id="derivar_solicitud" class="btn btn-warning ladda-button" data-style="slide-up">
                        <i class="fas fa-random"></i> Derivar
                    </a>
                @endif
            @endif
            <a id="deny_solicitude" class="btn btn-danger">
                <i class="fas fa-ban"></i> Rechazar
            </a>
        @endif

        @if ( $solicitud->id_user_assign == Auth::user()->id ) 
            @if ( $solicitud->id_estado == GASTO_HABILITADO )
                <a id="finish-expense" class="btn btn-success"><i class="fas fa-hourglass-end"></i> Terminar</a>
            @elseif( $solicitud->id_estado == ENTREGADO && $solicitud->devolutions()->where( 'id_estado_devolucion' , DEVOLUCION_POR_REALIZAR )->get()->count() !== 0 )
                <a class="btn btn-info get-devolution-info" data-type="do-inmediate-devolution"><i class="fas fa-hand-holding-usd"></i> Registro de la Devolución</a>
            @endif
        @endif
        @if ( Auth::user()->type == CONT )
            @if ( in_array( $solicitud->idtiposolicitud , array( SOL_REP , REEMBOLSO ) ) )            
                @if($solicitud->id_estado == APROBADO )
                    <a id="enable-deposit" class="btn btn-success"><i class="fas fa-check"></i> Confirmar</a>
                @endif
            @endif
            @if( $solicitud->id_estado == DEPOSITADO )
                <a id="seat-solicitude" data-loading-text="Generando..." class="btn btn-success"><i class="fas fa-calculator"></i> Generar Asiento</a>
            @elseif( $solicitud->id_estado == ENTREGADO )
                @if( $solicitud->idtiposolicitud != REEMBOLSO )
                    @if( $solicitud->pendingRefund->count() == 0 )
                        @if( $solicitud->pendingPayrollRefund->count() == 0 )
                            <a id="confirm-payroll-discount" class="btn btn-info"><i class="fas fa-user-tag"></i> Registro de Descuento por Planilla</a>
                        @endif
                        <a class="btn btn-info get-devolution-info" data-type="register-inmediate-devolution"><i class="fas fa-tag"></i> Solicitud de Descuento Inmediato</a>
                    @endif
                @endif
                <a id="finish-expense-record" class="btn btn-success"><i class="fas fa-hourglass-end"></i> Terminar Registro de Gasto</a>    
            @endif
        @endif
        <a href="{{URL::to('show_user')}}" class="btn btn-primary">
            <i class="fas fa-door-closed"></i> Regresar
        </a>
    </div>
</div>