@extends('template.main')
@section('solicitude')
    <div class="page-header">
        <h3>Reporte {{ str_replace( '_' , ' ' , $type ) }}</h3>
        <div class="col-xs-6 col-md-6 col-sm-4 col-lg-4 form-group">
            <select id="fund-category" class="form-control input-md">
                <option value="0">TODOS</option>
                @foreach( $funds as $fund )
                    <option value="{{ $fund['ID'] }}">{{ $fund['DESCRIPCION']}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <button type="button" id="report-export" class="btn btn-primary btn-md ladda-button" data-style="zoom-in">Exportar</button>
            <button type="button" id="search-report" class="btn btn-primary btn-md ladda-button" data-style="zoom-in">
                <span class="glyphicon glyphicon-search"></span>
            </button>
        </div>
        <input type="hidden" id="report-type" value="{{ $type }}">
    </div>
    <div id="reporte_{{ $type }}" class="container-fluid">
        <table id="table_reporte_{{ $type }}" class="table table-striped table-hover table-bordered text-center" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Familia</th>
                    <th>Saldo S/.</th>
                    <th>Retencion S/.</th>
                    <th>Saldo Disponible S/.</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th></th>
                    <th>Total</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <script>
        function columnDataTable( element , data , columns ,  message )
        {
            if( $.fn.dataTable.isDataTable( element ) )
            {
                element.DataTable().clear().destroy();
            }
            dataTable = element.DataTable(
            {
                bDestroy        :true ,
                scrollX         : 99,
                columns         : columns,
                data            : data ,
                dom             : "<'row'<'col-xs-6'><'col-xs-6 pull-right'f>r>t<'row'<'col-xs-6'i><'col-xs-6'p>>",
                stateSave       : true,
                bScrollCollapse : true,
                iDisplayLength  : 20 ,
                language        :
                {
                    search       : 'Buscar',
                    zeroRecords  : 'No hay ' + message ,
                    infoEmpty    : 'No ha encontrado ' + message +' disponible',
                    info         : 'Mostrando _END_ de _TOTAL_ ' + message ,
                    lengthMenu   : "Mostrando _MENU_ registros por página",
                    infoEmpty    : "No ha encontrado información disponible",
                    infoFiltered : "(filtrado de _MAX_ regitros en total)",
                    paginate     : 
                    {
                        sPrevious : 'Anterior',
                        sNext     : 'Siguiente'
                    }
                },
                footerCallback: function() 
                {
                    this.api().column( '.sum-saldo' , { search: 'applied' } ).every( function () 
                    {
                        if( this.data().length != 0 )
                        {
                            var sum  =  
                                this.data().reduce( function ( a , b ) 
                                {
                                    return ( Number( a ) + Number( b ) ).toFixed( 2 );
                                });
                            $( this.footer() ).html( sum );
                        }
                    });
                    

                    this.api().column( '.sum-retencion' , { search: 'applied' } ).every( function () 
                    {
                        if( this.data().length != 0 )
                        {
                            var sum  =  this.data().reduce( function ( a , b ) 
                                        {
                                            return Number( a ) + Number( b );
                                        });
                            $( this.footer() ).html( sum );
                        }
                    });

                    this.api().column( '.sum-saldo-disponible' , { search: 'applied' } ).every( function () 
                    {
                        if( this.data().length != 0 )
                        {
                            var sum  =  this.data().reduce( function ( a , b ) 
                                        {
                                            return ( Number( a ) + Number( b ) ).toFixed( 2 );
                                        });
                            $( this.footer() ).html( sum );
                        }
                    });
                }
            });

        }
    
        function getReportData()
        {
            var eType = $( '#report-type' );
            if( eType.length !== 0 )
            {
                var spin = Ladda.create( $( '#search-report' )[ 0 ] );
                eType    = eType.val();
                spin.start();
                $.ajax(
                {
                    type : 'post' ,
                    url  : server + 'report/sup/data' ,
                    data : 
                    {
                        _token   : GBREPORTS.token,
                        type     : eType,
                        category : $( '#fund-category' ).val()
                    }
                }).done( function( response )
                {
                    spin.stop();
                    name = '#table_reporte_' + eType;
                    var element = $( name );
                    columnDataTable( element , response.Data , response.columns , response.message );
                }).fail( function( statusCode , errorThrow )
                {
                    spin.stop();
                    ajaxError( statusCode , errorThrow );
                });
            }
        }

        $( '#search-report' ).on( 'click' , function()
        {
            getReportData();
        });

        $( '#report-export' ).on( 'click' , function()
        {
            var url = 'report/sup/export-';
            url += $( '#report-type' ).val() + '-';
            url += $( '#fund-category' ).val();
            window.location.href = server + url;
        });

        $(document).on( 'ready' , function()
        {
            getReportData();
        })
    </script>
@stop