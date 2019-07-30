<?php

	#use \DB;
	use \Carbon\Carbon;

	/*$qryProducts =  
	        DB::table( '( SELECT DISTINCT CODARTICULO FROM VTA.PPTO_VTA WHERE ANIO = ' . Carbon::now()->year . ') P' )
                        ->select( 'F.CODIGO ID' , 'F.NOMBRE DESCRIPCION' )
                        ->leftJoin( 'VTA.FOPRTE A' , 'P.CODARTICULO' , '=' , 'A.FOALIAS' )
                        ->leftJoin( TB_VTA_TABLAS.' F' , function( $join )
                        {
                	        $join->on( 'F.TIPO' , '=' , TIPO_FAMILIA )->on( 'F.CODIGO' , '=' , 'A.FOFAMILIA' );
                        })->distinct()->orderBy( 'F.NOMBRE' , 'ASC' );*/

        $year = Carbon::now()->year;

        $sql = 'SELECT DISTINCT 
                        familia descripcion, 
                        marca_id id 
                FROM 
                ( 
                        SELECT 
                                ( SELECT nombre from vta.tablas where tipo = 129 and codigo = marca_id ) familia,
                                marca_id,
                                anio
                        FROM FONDO_SUPERVISOR
                        WHERE 
                                saldo - retencion > 0
                        UNION ALL
                        SELECT 
                                ( SELECT nombre from vta.tablas where tipo = 129 and codigo = marca_id ) familia,
                                marca_id,
                                anio
                        FROM FONDO_GERENTE_PRODUCTO
                        WHERE saldo - retencion > 0  
                )
                WHERE 
                        anio = ' . $year;
        
        $qryProducts = DB::table( DB::raw( "( $sql )" ) )->orderBy( 'descripcion' , 'ASC' );

?>
