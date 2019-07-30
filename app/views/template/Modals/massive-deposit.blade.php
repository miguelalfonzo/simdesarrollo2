<div class="modal fade" id="massive-deposit-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Registro del Depósito - Masivo</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Bancos</label>
                    <select id="massive-bank-account" class="form-control">
                        @foreach ( $banks as $bank )
                            <option value="{{ $bank['NUM_CUENTA'] }}">
                                {{ $bank['SIMBOLO'] . '-' . $bank['CTANOMBRECTA'] }}
                            </option>    
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Busqueda ( Presionar Enter )</label>
                    <input type="text" id="filter-solicitud-id" class="form-control" style="font-weight:bold;text-transform:uppercase">
                </div>
                <div style="max-height:300px;overflow-y:scroll">
                	<table id="massive-deposit-table" class="table table-striped table-hover table-bordered table-condensed" cellspacing="0" width="100%">
                		<thead>
                			<tr>
                                <th>N°</th>
    		            		<th># Solicitud</th>
                                <th>Colaborador</th>
                                <th>Cuenta</th>
                                <th>Monto</th>
    		            		<th>N° Operacion</th>
                			</tr>
                		</thead>
                		<tbody>
                            @foreach( $depositIds as $key => $depositId )
                                <tr>
                                    <td><b>{{ $key + 1 }}</b></td>
                                    <td class="deposit-solicitud-cell"><b>{{ $depositId->id }}</b></td>
                                    <td><b>{{ mb_strtoupper( $depositId->personalTo->full_name ) }}</b></td>
                                    <td>
                                        <b>
                                            @if( $depositId->id_inversion == 36 )
                                                @if( $depositId->detalle->id_moneda == 1 )
                                                    194-1732292-098
                                                @elseif( $depositId->detalle->id_moneda == 2 )
                                                    194-1809102-167
                                                @endif
                                            @elseif( $depositId->id_inversion == 38 )
                                                @if( $depositId->detalle->id_moneda == 1 )
                                                    194-233-9351007
                                                @elseif( $depositId->detalle->id_moneda == 2 )
                                                    194-229-5288135
                                                @endif
                                            @else
                                                @if( in_array( $depositId->assignedTo->type , [ REP_MED , SUP ] ) )
                                                    {{ $depositId->personalTo->getAccount() }}
                                                @endif
                                            @endif
                                        </b>
                                    </td>
                                    <td><b>{{ $depositId->detalle->currency_money }}</b></td>
                                    <td class="deposit-operacion-cell"><b><input type="text" class="form-control" autocomplete="off"></b></td>
                                    <input type="hidden" class="deposit-solicitud-token" value="{{ $depositId->token }}"> 
                                </tr>
                            @endforeach
                        </tbody>
                	</table>
                </div>
            </div>
            <div class="modal-footer">
        	    <button type="button" id="register-deposit-massive" class="btn btn-success ladda-button" data-style="zoom-in">Confirmar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script>
    $( '#register-deposit-massive' ).click( function()
    {
        var spin = Ladda.create( this );
        var solicituds = [];
        var indexs     = [];
        var depositTableTrs = $( '#massive-deposit-table tbody tr' );
        var depositTableTr;
        var depositTableTd;
        var solicitudCount = depositTableTrs.length;
        var solicitud;
        var depositNumber;
        for( var i = 0 ; i < solicitudCount ; i++ )
        {
            depositTableTr    = depositTableTrs.eq( i );
            depositTableTds   = depositTableTr.find( 'td' );
            depositNumberCell = depositTableTr.find( '.deposit-operacion-cell input' );
            if( depositNumberCell.length != 0 )
            {
                depositNumber = depositNumberCell.val().trim();
                if( depositNumber != '' )
                {
                    solicitud = 
                    { 
                        id        : depositTableTr.find( '.deposit-solicitud-cell' ).text(),
                        token     : depositTableTr.find( '.deposit-solicitud-token' ).val(),
                        operacion : depositNumber
                    };
                    solicituds.push( solicitud );
                    indexs.push( i );
                }
            }
        }
        if( solicituds.length == 0 )
        {
            bootbox.alert( '<h4 class="text-warning"><b>No ingreso al menos un numero de operacion</b></h4>' );
        }
        else
        {
            bootbox.confirm( '<h4 class="text-info"><b>¿ Esta seguro de registrar los depositos ?</b></h4>' , function( result )
            {
                var button = this.find( 'button[ data-bb-handler=confirm ]' )[ 0 ];
                button.disabled = true;

                if( result )
                {
                    spin.start();
        
                    $.ajax(
                    {
                        url  : 'massive-solicitud-deposit',
                        type : 'POST',
                        data : 
                        {
                            _token : GBREPORTS.token,
                            cuenta : $( '#massive-bank-account' ).val(),
                            data   : solicituds
                        }
                    }).fail( function( statusCode , errorThrow )
                    {
                        ajaxError( statusCode , errorThrow );
                    }).done( function( response )
                    {   
                        if( response.Status != ok )
                        {   console.log(response);
                            bootboxMessage( response );
                        }
                        
                        if( typeof response[ data ] !== 'undefined' )
                        {
                            var rowStatus;
                            for( var i = 0 ; i < indexs.length ; i++ )
                            {
                                depositTableTr = depositTableTrs.eq( indexs[ i ] );
                                depositTableTds = depositTableTr.find( 'td' );
                                id = depositTableTr.find( '.deposit-solicitud-cell' ).text();
                                rowStatus = response[ data ][ id ];
                                if( rowStatus.Status === ok )
                                {
                                    depositTableTr.addClass( 'success' ).attr( 'title' , 'Ok' );
                                    depositTableTr.find( '.deposit-operacion-cell b' ).text( rowStatus.operacion );
                                }
                                else
                                {
                                    depositTableTr.addClass( 'danger' ).attr( 'title' , rowStatus.Description );
                                }
                            }
                            spin.stop();
                            window.location.href = 'deposit-export';
                            getSolicitudList();
                        }
                        else
                        {
                            spin.stop();
                            bootboxMessage( response );
                        }
                    });
                }
            });
        }
    });

    $( '#filter-solicitud-id' ).on( 'keypress' , function( event )
    {
        if( event.keyCode === 13 )
        {
            $( '#massive-deposit-table tbody tr' ).hide();
            $( '#massive-deposit-table tbody tr:contains(' + this.value.toUpperCase() + ')' ).show();
        }
    });

    function inputUp( tableBody , rowIndex , columIndex , i )
    {
        ++i;
        var tr = tableBody.find( 'tr' ).eq( rowIndex - i );
        aaa = tr;
        if( tr.css( 'display' ) == 'none' )
        {
            inputUp( tableBody , rowIndex , columIndex , i );
        }
        else
        {
            var input = tr.find( 'td' ).eq( columIndex ).find( 'input' );
            if( input.length == 0 )
            {
               inputUp( tableBody , rowIndex , columIndex , i );
            }
            else
            {
                input.focus();
            }
        }
    }

    function inputDown( tableBody , rowIndex , columIndex )
    {
        var trs = tableBody.children();
        if( ( rowIndex + 1 ) == trs.length )
        {
            var newRowIndex = 0;
        }
        else
        {
            var newRowIndex = rowIndex + 1;
        }
        var tr    = tableBody.find( 'tr' ).eq( newRowIndex );
        aaa = tr;
        
        if( tr.css( 'display' ) == 'none' )
        {
           inputDown( tableBody , newRowIndex , columIndex );
        }
        else
        {
            var input = tr.find( 'td' ).eq( columIndex ).find( 'input' );
            if( input.length == 0 )
            {
               inputDown( tableBody , newRowIndex , columIndex );        
            }
            else
            {
                input.focus();
            }
        }
    }

    $( '.deposit-operacion-cell' ).on( 'keydown' , function( event )
    {
        var cell = $( this );
        var e = event;
        var rowIndex = cell.closest( 'tr' ).index();
        var columIndex = cell.closest( 'td' ).index();
        var tableBody = $( '#massive-deposit-table tbody' );

        var i = 0;
        if( e.keyCode === 38 )
        {
            inputUp( tableBody , rowIndex , columIndex , i );
        }
        else if( e.keyCode === 40 )
        {
            inputDown( tableBody , rowIndex , columIndex );
        }
    });
</script>