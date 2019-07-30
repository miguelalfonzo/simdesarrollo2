<?php

namespace Seat;

use \Eloquent;
use \Expense\Entry;
use \Carbon\Carbon;

class Seat extends Eloquent
{
	
    protected $table = TB_BAGO_ASIENTO;
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    
    protected static function generateManualSeatCod( $year , $origen )
    {
        $lastSeat = Seat::select( 'substr( penclave , 0 , 5 ) numasiento' )->where( 'penaamovim' , $year )->where( 'penclave' , 'like' , $origen . '%' )
                    ->orderBy( 'penclave' , 'desc' )->first();
        if ( is_null( $lastSeat ) )
        {
            $seat = $origen . '0001';
        }   
        else
        {
            $seat = $origen . substr( str_pad( ( $lastSeat->numasiento + 1 ) , 4 , 0 , STR_PAD_LEFT ) , -4 );
        }
        return $seat;
    }

    protected static function registerSeat( $systemSeat , $seatPrefix , $key , $state )
    {
        $seat                = new Seat;
        $seat->penclave      = $seatPrefix . str_pad( ( $key + 1 ) , 4 , 0 , STR_PAD_LEFT ); //CLAVE DE BAGO DEL ASIENTO
        $seat->pencodtar1    = '1'; // '1' Codigo Fijo
        $seat->pentipomovim  = 'VS';

        //EL CAMPO PENAAMOVIM ES EL MISMO DIA DE LA TRANSFERENCIA O ES EL ULTIMO DIA DEL MES PARA LOS ASIENTOS CON PREFIJO 0 (MANUALES)
        $now = Carbon::now();

        $seat->penddmovim    = $now->format( 'd' );// DIA ( TEXTO DOS DIGITOS )DEL REGISTRO DEL ASIENTO , CIERRE CONTABLE GENERALMENTE 31 o 30 o 29 o 28
        $seat->penmmmovim    = $now->format( 'm' );// MES ( TEXTO DOS DIGITOS )DEL REGISTRO DEL ASIENTO , MES DEL REGISTRO DEL ASIENTO
        $seat->penaamovim    = $now->format( 'Y' );

        $seat->penctaextern  = $systemSeat->num_cuenta; //CUENTA CONTABLE 7 DIGITOS
        $seat->pencodcompor  = Self::blankspace( $systemSeat->cc ); // CODIGO DEL TIPO DE DOCUMENTO ESTABLECIDO POR SUNAT| '00' => NO SUSTENTABLE | CODIGOS DIFERENTES PARA FACTURAS , BOLETAS , TICKET , RECIBO POR HONORARIOS 
        $seat->penddcomporg  = str_pad( $systemSeat->fec_origen->day , 2 , 0 , STR_PAD_LEFT ); // DIA DEL DOCUMENTO | texto 2 digitos
        $seat->penmmcomporg  = str_pad( $systemSeat->fec_origen->month , 2 , 0 , STR_PAD_LEFT ); // MES DEL DOCUMENTO  | texto 2 digitos
        $seat->penaacomporg  = $systemSeat->fec_origen->year; // AÑO DEL DOCUMENTO
        $seat->pencodtar2    = '2'; // SETEADO UNICO VALOR
        $seat->penleyendafi  = Self::blankspace( $systemSeat->leyenda_fj ); // CENTRO DE COSTOS DE BAGO PARA LOS DOCUMENTOS 
        $seat->penleyendava  = substr( Self::blankspace( $systemSeat->leyenda ) , 0 , 50 ); // GLOSA , DESCRIPCION DEL ASIENTO | MAXIMO 50 CARACTERES
        $seat->pentipoimpor  = $systemSeat->d_c; // CODIGO CONTABLE DE LAS CUENTAS "T" | D => DEBE | H => HABER
        $seat->penimportemo  = ltrim( number_format( $systemSeat->importe , 2 , '.' , '' ) , 0 ); //MONTO SEPARADOR DECIMAL CON PUNTO , SOLO DOS DECIMALES Y SI ES 0 CON DECIMALES QUE NO APAREZCA EL 0.
        $seat->pencodigoiva  = Self::blankspace( $systemSeat->iva ); // Codigo del sistema para los asientos de documentos con IGV | N6 PARA ITEM Y I6 PARA IGB
        $seat->pencodprovee  = Self::blankspace( $systemSeat->cod_pro ); //CODigo del sistema para los asientos de documentos con IGV = "90000"
        $seat->pencoddivisi  = $systemSeat->cod_pro == 90000 ? 1 : ' '; // or '1' 
        $seat->penestado     = $state; //The First line has 'C'
        
        // 4 digitos para la serie del comprobante y al empezar tiene un espacio en blanco el cual solo aparece si tiene una letra la serie || y el numero del Comprobante
        if ( is_null( $systemSeat->prefijo ) || empty( trim( $systemSeat->prefijo ) ) )
        {
            $serie = ' ';
        }
        else
        {
            $tresDigitosSerie = str_pad( $systemSeat->prefijo , 3 , 0 , STR_PAD_LEFT );
            $cuatroDigitosSerie = str_pad( $tresDigitosSerie , 4 , ' ' , STR_PAD_LEFT );
            $serie = substr( $cuatroDigitosSerie , strlen( $cuatroDigitosSerie ) - 4 , 4 ) . $systemSeat->cbte_prov;
        }
        $seat->pennrocompro  = $serie;
        
        $razon = substr( Self::blankspace( $systemSeat->nom_prov ) , 0 , 50 ); // RAZON SOCIAL SOLO PARA DOCUMENTOS | maximo 50 digitos.
        $razon = str_replace( "," , " " , $razon );
        $razon = str_replace( "'" , " " , $razon );
        $seat->pennombrepro  = $razon;
        $seat->pencoddocpro  = Self::blankspace( $systemSeat->cod ); // CODIGO del sistema para documentos con igv = "80"
        $seat->pennrodocpro  = Self::blankspace( $systemSeat->ruc );  // RUC SOLO PARA DOCUMENTOS
        $seat->pencanthojas  = Self::blankspace( $systemSeat->tipo_responsable ); //CODIGO DE TIPO DE RESPONSABLE 1 , 2, 4 PARA LA SUNAT DE USUARIOS EFECTOS A OPERACION GRAVADA => 1  , NO GRAVADA => 2  O AMBOS = 4
        $seat->pencondiva    = $systemSeat->tipo_responsable == 1 ? 'IN' : ( $seat->tipo_responsable == 2 ? 'NI' : ( $seat->tipo_responsable == 4 ? 'MO' : ' ' ) ); //Depende de la anterior columna para 1 => IN , 2 => NI y 4 => MO
        $seat->pengentarjeta = 'N' ; // Flag 'N' y 'S' . indica estado para el sistema de bago N => NO Y S => SI
        $seat->penusuario    = 'MQUIROZ' ;// USUARIO 
    	$seat->pennrocompor  = Self::blankspace( $systemSeat->nro_origen ); //CORRELATIVO POR DOCUMENTO DE BAGO PARA CONTROL DOCUMENTARIO , SE INGRESARA EN EL SISTEMA DEV DE BAGO
        $seat->pencodmoneda  = ' '; // CODIGO DE REGISTRO PARA LOS ASIENTOS EN DOLARES => 02 
    	$seat->pentipocambio = ' '; // TIPO DE CAMBIO PARA CUANDO EL ASIENTO ES EN DOLARES
    	$seat->penfchmod     = Carbon::now()->format( 'd/m/Y' );;
        //$seat->penimporte2 = ' '; // IMPORTE EN SOLES DEL ASIENTO CUANDO 
    	if( $seat->save() )
        {
            $systemSeat->penclave = $seat->penclave;
            $systemSeat->estado   = 1;
            if( $systemSeat->save() )
            {
                return array( status => ok );
            }
            else
            {
                return array( status => error );
            }
        }
        else
        {
            return array( status => error );
        }
    }

    private static function blankspace( $value )
    {
        if ( is_null( $value ) || empty( trim( $value ) ) )
        {
            return ' ';
        }
        else
        {
            return $value;
        }
    }

}
    