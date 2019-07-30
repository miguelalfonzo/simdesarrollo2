<div class="modal fade" id="massive-revision-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="content">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="active">
                            <a href="#tab-massive-revision" role="tab" data-toggle="tab">
                                <icon class="fa fa-home"></icon>
                                Revision
                            </a>        
                        </li>
                        <li>
                            <a href="#tab-massive-depositSeat" role="tab" data-toggle="tab">
                                <icon class="fa fa-home"></icon>
                                Asiento de Deposito
                            </a>
                        </li>
                        <li>
                            <a href="#tab-massive-regularizationSeat" role="tab" data-toggle="tab">
                                <icon class="fa fa-home"></icon>
                                Asiento de Regularizacion
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="tab-massive-revision">
                            <div style="max-height:300px;overflow-y:auto">
                                @include( 'Dmkt.Cont.massive_table' , [ 'solicituds' => $revisionSolicituds ] )
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-massive-depositSeat">
                            <div style="max-height:300px;overflow-y:auto">
                                @include( 'Dmkt.Cont.massive_table' , [ 'solicituds' => $depositSeatSolicituds ] )
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-massive-regularizationSeat">
                            <div style="max-height:300px;overflow-y:auto">
                                @include( 'Dmkt.Cont.massive_table' , [ 'solicituds' => $regularizationSeatSolicituds ] )
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="register-revision-massive" class="btn btn-success ladda-button" data-style="zoom-in">Confirmar</button>
        	    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script>
    $( '.thead-checkbox' ).click( function()
    {
        var check = this.checked;
        $( this ).closest( 'table' ).find( 'tbody input[ type=checkbox ]' ).prop( 'checked' , check );
    });

    $( '#register-revision-massive' ).click( function()
    {
        var spin = Ladda.create( this );
        var solicituds = [];
        var indexs     = [];
        var revisionTable    = $( '#massive-revision-modal .active .massive-revision-table' );
        var revisionTableTrs = revisionTable.find( 'tbody' ).children();
        var solicitudCount   = revisionTableTrs.length;
        var checkedElement;
        var checked;
        var solicitud;
        for( var i = 0 ; i < solicitudCount ; i++ )
        {
            revisionTableTr = revisionTableTrs.eq( i );
            checkedElement  = revisionTableTr.find( 'input[ type=checkbox ]' );
            if( checkedElement.length == 1 )
            {
                checked = checkedElement.prop( 'checked' );
                if( checked )
                {
                    solicitud =
                    {
                        token : revisionTableTr.find( '.revision-solicitud-token' ).val(),
                        id    : revisionTableTr.find( 'td' ).eq( 1 ).text()
                    };
                    solicituds.push( solicitud );
                    indexs.push( i );
                }
            }
        }
        if( solicituds.length == 0 )
        {
            bootbox.alert( '<h4 class="text-warning"><b>No ha seleccionado al menos una solicitud</b></h4>' );
        }
        else
        {
            bootbox.confirm( '<h4 class="text-info"><b>¿ Esta seguro de procesar las solicitudes ?</b></h4>' , function( result )
            {
                var button = this.find( 'button[ data-bb-handler=confirm ]' )[ 0 ];
                button.disabled = true;

                if( result )
                {
                    spin.start();        
                    $.ajax(
                    {
                        url  : 'massive-solicitud-revision',
                        type : 'POST',
                        data : 
                        {
                            _token : GBREPORTS.token,
                            data   : solicituds
                        }
                    }).fail( function( statusCode , errorThrow )
                    {
                        ajaxError( statusCode , errorThrow );
                    }).done( function( response )
                    {
                        if( response.Status != ok )
                        {
                            bootboxMessage( response );
                        }
                        if( typeof response[ data ] !== 'undefined' )
                        {
                            var rowStatus;
                            for( var i = 0 ; i < indexs.length ; i++ )
                            {
                                revisionTableTr = revisionTableTrs.eq( indexs[ i ] );
                                revisionTableTds = revisionTableTr.find( 'td' );
                                id = revisionTableTds.eq( 1 ).text();
                                rowStatus = response[ data ][ id ];
                                if( rowStatus.Status === ok )
                                {
                                    revisionTableTr.removeClass( 'danger' ).addClass( 'success' ).attr( 'title' , 'Ok' );
                                    revisionTableTds.eq( 2 ).text( '' );
                                }
                                else
                                {
                                    revisionTableTr.removeClass( 'success' ).addClass( 'danger' ).attr( 'title' , rowStatus.Description );
                                }
                            }
                            window.location.href = response.location;
                            getSolicitudList();
                        }
                        spin.stop();
                    });
                }
            });
        }
    });
</script>