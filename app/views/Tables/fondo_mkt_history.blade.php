@extends('template.main')
@section('solicitude')
    <div class="page-header">
        <h3>Historial de los Fondos</h3>
    </div>
    <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-4">
        <select id="fondoMkt" class="form-control">
            <option value="0" selected disabled>Seleccione un Fondo</option>
            @foreach( $fondoSubCategories as $fondoSubCategory )
                <option value="{{ $fondoSubCategory->id }}">{{ $fondoSubCategory->descripcion }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-4">
        <button type="button" id="export-fund-history" class="btn btn-primary">Exportar
            <span class="glyphicon glyphicon glyphicon-export"></span>
        </button>
    </div>
    <div id="fondo_mkt_history" class="form-group col-md-12">
    </div>
    <script>
        $(document).on( 'ready' , function()
        {
            GBREPORTS.changeDateRange('M');
            $( '#fondoMkt' ).on( 'change' , function()
            {
                getSubCategoryHistory( $( this ) );
            });
        });

        var exportButton = document.getElementById( 'export-fund-history' );
        exportButton.onclick = function()
        {
            var subCategoryId = document.getElementById( 'fondoMkt' ).value;
            if( subCategoryId == 0 )
            {
                bootbox.alert( '<h4 class="text-info"><b>Seleccione un Fondo</b></h4>' );
            }
            else
            {
                var start = $( '#drp_menubar' ).data( 'daterangepicker' ).startDate.startOf( 'month' ).format( 'YYYYMMDD' );
                var end   = $( '#drp_menubar' ).data( 'daterangepicker' ).endDate.endOf( 'month' ).format( 'YYYYMMDD' );
                var url = 'export-fondoHistorial-' + start + '-' + end + '-' + subCategoryId;
                window.location.href = url;
            }
        }

        function getSubCategoryHistory( option )
        {
            data = 
            {
                _token          : GBREPORTS.token,
                id_subcategoria : $( option ).val(),
                start           : $('#drp_menubar').data('daterangepicker').startDate.startOf( 'month' ).format( 'YYYYMMDD' ),
                end             : $('#drp_menubar').data('daterangepicker').endDate.endOf( 'month' ).format( 'YYYYMMDD' )
            };

            $( '#loading' ).show( 'slow' );
            $.post( server + 'fondo-subcategoria-history' , data )
            .fail( function( statusCode , errorThrow )
            {
                $( '#loading' ).hide( 'slow' );
                ajaxError( statusCode , errorThrow );
            }).done( function ( response )
            {
                $( '#loading' ).hide( 'slow' );
                if( response.Status == 'Ok' )
                    dataTable( 'fondo_mkt_history' , response.Data.View , 'registros' )
                else
                    bootbox.alert( '<h4 class="red">' + response.Status + ' : ' + response.Description + '</h4>' );
            });
        }
    </script>
    </script>
@stop