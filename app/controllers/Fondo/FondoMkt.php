<?php

namespace Fondo;

use \BaseController;
use \System\FondoMktHistory;
use \PPTO\PPTOSupervisor;
use \PPTO\PPTOGerente;
use \PPTO\PPTOInstitucion;
use \Expense\ChangeRate;
use \Dmkt\Solicitud;
use \Auth;
use \View;
use \Input;
use \Excel;
use \Carbon\Carbon;

class FondoMkt extends BaseController
{
    private static function actualizarFondo($idFondoMkt,$tipoFondoMkt,$solProductId){

        $row = \DB::transaction(function($conn) use($idFondoMkt,$tipoFondoMkt,$solProductId){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_ACTUALIZA_FONDOMKT(:idFondoMkt,:tipoFondoMkt,:solProductId); END;');
                $stmt->bindParam(':idFondoMkt', $idFondoMkt, \PDO::PARAM_STR);
                $stmt->bindParam(':tipoFondoMkt', $tipoFondoMkt, \PDO::PARAM_STR);
                $stmt->bindParam(':solProductId', $solProductId, \PDO::PARAM_STR);
                $stmt->execute();       
        });

       
    }

	public function setFondoMktHistories( $historiesFondoMkt , $idSolicitud )
    {
        foreach( $historiesFondoMkt as $historyFondoMkt )
            $this->setFondoMktHistory( $historyFondoMkt , $idSolicitud );
    }

    public function setFondoMktHistory( $historyFondoMkt , $idSolicitud )
    {
        $fondoMktHistory                          = new FondoMktHistory;
        $fondoMktHistory->id                      = $fondoMktHistory->nextId();
        $fondoMktHistory->id_solicitud            = $idSolicitud;
        $fondoMktHistory->id_to_fondo             = $historyFondoMkt[ 'idFondo' ];
        $fondoMktHistory->id_tipo_to_fondo        = $historyFondoMkt[ 'idFondoTipo' ];
        $fondoMktHistory->id_fondo_history_reason = $historyFondoMkt[ 'reason' ];
        $fondoMktHistory->to_old_saldo            = $historyFondoMkt[ 'oldSaldo' ];
        $fondoMktHistory->to_new_saldo            = $historyFondoMkt[ 'newSaldo' ];
        $fondoMktHistory->old_retencion           = $historyFondoMkt[ 'oldRetencion' ];
        $fondoMktHistory->new_retencion           = $historyFondoMkt[ 'newRetencion' ];
        $fondoMktHistory->save();
    }

    public function validateBalance( $userTypes , $fondos )
    {
        $userTypes = array_unique( $userTypes );
        if ( count( $userTypes ) != 1 )
            return $this->warningException( 'No es posible asignar Fondos de Roles Diferentes' , __FUNCTION__ , __LINE__ , __FILE__ );
        else
            $userType = $userTypes[ 0 ];

        /*if ( $userType == SUP )
            $fondoCategoria = array_unique( FondoSupervisor::whereIn( 'id' , array_keys( $fondos ) )->lists( 'subcategoria_id' ) );
        elseif ( $userType == GER_PROD )
            $fondoCategoria = array_unique( FondoGerProd::whereIn( 'id' , array_keys( $fondos ) )->lists( 'subcategoria_id' ) );

        if ( count( $fondoCategoria ) != 1 )
            return $this->warningException( 'No es posible seleccionar Fondos de Diferentes SubCategorias por Solicitud' , __FUNCTION__ , __LINE__ , __FILE__ );*/
        
        $msg = ' el cual no es suficiente para completar el registro , se requiere un saldo de S/.';
        $middleRpta = $this->validateFondoSaldo( $fondos , $userType , $msg , '_disponible' );
        
        if ( $middleRpta[ status ] == ok )
            return $this->setRpta( $userType );

        return $middleRpta;
    }

    public function discountBalance( $ids_fondo , $moneda , $tc , $idSolicitud , $userType = NULL )
    {
        if( $moneda == SOLES )
        {
            $tasaCompra        = 1;
            $tasaCompraAntigua = 1;
        }
        elseif( $moneda == DOLARES )
        {
            $tasaCompra          = $tc->venta;
            
            $lastApprovedHistory = Solicitud::find( $idSolicitud )->lastApprovedHistory;
            if( ! is_null( $lastApprovedHistory ) )
            {
                $tasaCompraAntigua   = ChangeRate::getLastDayDolar( $lastApprovedHistory->created_at );
            }
        }
        else
        {
            return $this->warningException( 'Moneda no identificada. Moneda #' .$moneda , __FUNCTION__ , __LINE__ , __FILE__ );
        }

        $historiesFondoMkt = [];
        foreach( $ids_fondo as $id_fondo )
        {
            if( ! is_null( $id_fondo[ 'old' ] ) )
            {
                switch( $id_fondo[ 'oldUserType' ] )
                {
                    case SUP:
                        $fondoMkt = FondoSupervisor::find( $id_fondo[ 'old' ] );
                        break;
                    case GER_PROD:
                        $fondoMkt = FondoGerProd::find( $id_fondo[ 'old' ] );
                        break;
                    case FONDO_SUBCATEGORIA_INSTITUCION:
                        $fondoMkt = FondoInstitucional::find( $id_fondo[ 'old' ] );
                        break;
                    default:
                        return $this->warningException( 'No se pudo identificar el fondo para realizar el retorno de la retencion. Fondo #' . $id_fondo[ 'old' ] , __FUNCTION__ , __LINE__ , __FILE__ );            
                }
                $this->setHistoryData( $historiesFondoMkt , $fondoMkt , $tasaCompraAntigua , $id_fondo[ 'oldMonto'] , $id_fondo[ 'oldUserType' ] , FONDO_LIBERACION );
                
            }
            if( ! is_null( $userType ) )
            {
                switch( $userType )
                {
                    case SUP:
                        $fondoMkt = FondoSupervisor::find( $id_fondo[ 'new' ] );
                        break;
                    case GER_PROD:
                        $fondoMkt = FondoGerProd::find( $id_fondo[ 'new' ] );
                        break;
                    case FONDO_SUBCATEGORIA_INSTITUCION:
                        $fondoMkt = FondoInstitucional::find( $id_fondo[ 'new' ] );
                        break;
                    default:
                        return $this->warningException( 'No se pudo identificar el fondo para realizar la retencion. Fondo #' . $id_fondo[ 'new' ] , __FUNCTION__ , __LINE__ , __FILE__ );
                }
                $this->setHistoryData( $historiesFondoMkt , $fondoMkt , $tasaCompra , $id_fondo[ 'newMonto' ] , $userType , FONDO_RETENCION );            
            }
        }
        $this->setFondoMktHistories( $historiesFondoMkt , $idSolicitud ); 
        return $this->setRpta();
    }

    public function validateFondoSaldo( $fondosData , $fondoType , $msg , $tipo = '' )
    {
        $totalAmountSup = [];
        foreach( $fondosData as $idFondo => $fondoMonto )
        {
            if ( $fondoType == SUP )
            {
                $fondo = FondoSupervisor::find( $idFondo );
                
                if( isset( $totalAmountSup[ $fondo->subcategoria_id ][ $fondo->supervisor_id ] ) )
                {
                    $totalAmountSup[ $fondo->subcategoria_id ][ $fondo->supervisor_id ] += $fondoMonto;  
                }
                else
                {
                    $totalAmountSup[ $fondo->subcategoria_id ][ $fondo->supervisor_id ] = $fondoMonto;  
                }
            }
            elseif ( $fondoType == GER_PROD )
            {
                $fondo = FondoGerProd::find( $idFondo );
                var_dump($fondo);
                if ( $fondo->{ 'saldo' . $tipo } < 0 )
                {
                    return $this->warningException( 'El Fondo ' . $fondo->full_name . ' solo cuenta con S/.' . ( $fondo->{ 'saldo' . $tipo } + $fondoMonto ) . 
                                                    $msg . $fondoMonto . ' en total' , __FUNCTION__ , __LINE__ , __FILE__ );
                }
            }
        }

        if( ! empty( $totalAmountSup ) )
        {
            foreach( $totalAmountSup as $subCategoryId => $sups )
            {
                foreach( $sups as $supId => $amount )
                {

                    $fondoSup = FondoSupervisor::totalAmount( $subCategoryId , $supId );
                    $fondoSubCategory = FondoSubCategoria::find( $subCategoryId );

                    if( $fondoSup->{ 'saldo' . $tipo } < 0 )
                    {
                        
                        return $this->warningException( 'El Fondo ' . $fondoSubCategory->descripcion . ' solo cuenta con S/.' . ( $fondoSup->{ 'saldo' . $tipo } + $amount ) . 
                                                    $msg . $amount . ' en total' , __FUNCTION__ , __LINE__ , __FILE__ );
                    }
                } 
            }
        }
        return $this->setRpta();
    }

    public function setFondo( $fondoData , $solProduct , $detalle , $tc , &$userTypes , &$fondos )
    {
        $fondoData = explode( ',' , $fondoData );//convierte string (1029,s)en array 

        if(  is_null( $solProduct->id_fondo_marketing ) )
        {
            if ( ! ( in_array( Auth::user()->type , array( SUP , GER_PROD , GER_PROM , GER_COM , GER_GER ) ) ) )
            {
                if( $fondoData[ 1 ] == SUP && ! in_array( Auth::user()->type , array( SUP , GER_PROM , GER_COM , GER_GER ) ) )
                    return $this->warningException( 'Cancelado - No existe un fondo asignado y el usuario no puede asignar fondos' , __FUNCTION__ , __LINE__ , __FILE__ );
                else if( $fondoData[ 1 ] == GER_PROD && ! in_array( Auth::user()->type , array( GER_PROD , GER_COM , GER_GER ) ) )
                    return $this->warningException( 'Cancelado - No existe un fondo asignado y el usuario no puede asignar fondos' , __FUNCTION__ , __LINE__ , __FILE__ );
                else if( $fondoData[ 1 ] == GER_PROM && ! in_array( Auth::user()->type , array( GER_PROM , GER_COM , GER_GER ) ) )
                    return $this->warningException( 'Cancelado - No existe un fondo asignado y el usuario no puede asignar fondos' , __FUNCTION__ , __LINE__ , __FILE__ );
            }
        }

        if ( $fondoData[ 1 ] == SUP )
            $fondoMkt = FondoSupervisor::find( $fondoData[ 0 ] );
        elseif ( $fondoData[ 1 ] == GER_PROD )
            $fondoMkt = FondoGerProd::find( $fondoData[ 0 ] );

        if ( $detalle->id_moneda == DOLARES )
            $monto_soles = round( $solProduct->monto_asignado * $tc->venta , 2 , PHP_ROUND_HALF_DOWN );
        elseif ( $detalle->id_moneda == SOLES )
            $monto_soles = $solProduct->monto_asignado;

        if ( isset( $fondos[ $fondoMkt->id ] ) )
            $fondos[ $fondoMkt->id ] += $monto_soles;
        else
            $fondos[ $fondoMkt->id ] = $monto_soles;
        
        $userTypes[]                         = $fondoData[ 1 ];
        $solProduct->id_fondo_marketing      = $fondoData[ 0 ];
        $solProduct->id_tipo_fondo_marketing = $fondoData[ 1 ];

        self::actualizarFondo($solProduct->id_fondo_marketing,$solProduct->id_tipo_fondo_marketing,$solProduct->id);
        return $this->setRpta();
    }

    public function setPeriodHistoryData( $subCategoryId , $data )
    {
        $fondoPeriodHistory = FondoMktPeriodHistory::getNowFondoMktPeriod( $subCategoryId );

        if ( is_null( $fondoPeriodHistory ) ):
            $lastFondoPeriodHistory      = FondoMktPeriodHistory::getLastFondoMktPeriod( $subCategoryId );
            $fondoPeriodHistory          = new FondoMktPeriodHistory;
            $fondoPeriodHistory->id      = $fondoPeriodHistory->nextId();
            //$fondoPeriodHistory->periodo = $period;
            $fondoPeriodHistory->subcategoria_id = $subCategoryId;
            if ( is_null( $lastFondoPeriodHistory ) ):
                $fondoSubCategory                      = FondoSubCategoria::find( $subCategoryId );
                $fondos                                = $fondoSubCategory->fund;
                $fondoPeriodHistory->saldo_inicial     = $fondos->sum( 'saldo' );
                $fondoPeriodHistory->retencion_inicial = $fondos->sum( 'retencion' );    
            else:
                $fondoPeriodHistory->saldo_inicial     = $lastFondoPeriodHistory->saldo_final;
                $fondoPeriodHistory->retencion_inicial = $lastFondoPeriodHistory->retencion_final;
            endif;            
            $fondoPeriodHistory->saldo_final       =  $fondoPeriodHistory->saldo_inicial  + ( $data[ 'newSaldo' ] - $data[ 'oldSaldo' ] );
            $fondoPeriodHistory->retencion_final   = $fondoPeriodHistory->retencion_inicial + ( $data[ 'newRetencion' ] - $data[ 'oldRetencion' ] );
        else:
            $fondoPeriodHistory->saldo_final     += $data[ 'newSaldo' ] - $data[ 'oldSaldo' ];
            $fondoPeriodHistory->retencion_final += $data[ 'newRetencion' ] - $data[ 'oldRetencion' ];
        endif;

        $fondoPeriodHistory->save();

    }

    public function setHistoryData( &$historyFondoMkt , $fondoMkt , $tasaCompra , $monto , $userType , $reason )
    {
        $oldSaldo     = $fondoMkt->saldo;
        $oldRetencion = $fondoMkt->retencion;
        if ( $reason == FONDO_LIBERACION )
            $fondoMkt->retencion  -= $monto * $tasaCompra ;
        elseif ( $reason == FONDO_RETENCION )
            $fondoMkt->retencion  += $monto * $tasaCompra ;

        $data = array( 
            'idFondo'      => $fondoMkt->id , 
            'idFondoTipo'  => $userType ,
            'oldSaldo'     => $oldSaldo , 
            'oldRetencion' => $oldRetencion ,
            'newSaldo'     => $fondoMkt->saldo , 
            'newRetencion' => $fondoMkt->retencion ,
            'reason'       => $reason );
        $historyFondoMkt[] = $data;
        $this->setPeriodHistoryData( $fondoMkt->subcategoria_id , $data );
        $fondoMkt->save();
    }

    public function getFondoHistorial()
    {
        $data = array( 
            'fondoSubCategories' => FondoSubCategoria::order()
        );
        return View::make( 'Tables.fondo_mkt_history' , $data );
    }

    public function getFondoSubCategoryHistory()
    {
        $inputs = Input::all();
        $start = $inputs[ 'start' ];
        $end   = $inputs[ 'end' ]; 

        if( substr( $start , 0 , 4) != substr( $end , 0 , 4 ) )
        {
            return $this->warningException( 'No se puede generar el reporte para años diferentes.' , __FUNCTION__ , __LINE__ , __FILE__ );
        }

        $data = $this->getFondoHistoryData( $inputs[ 'start' ] , $inputs[ 'end' ] , $inputs[ 'id_subcategoria' ] );
        return $this->setRpta( [ 'View' => View::make( 'Tables.table_fondo_mkt_history' , $data )->render() ] );
    }

    public function exportFondoHistorial( $start , $end , $subCategoryId )
    {
        try
        {
            if( substr( $start , 0 , 4) != substr( $end , 0 , 4 ) )
            {
                return $this->warningException( 'No se puede generar el reporte para años diferentes.' , __FUNCTION__ , __LINE__ , __FILE__ );
            }

            $data = $this->getFondoHistoryData( $start , $end , $subCategoryId );

            $subCategoryName = FondoSubCategoria::find( $subCategoryId )->descripcion;
            $title = 'SIM ' . $subCategoryName . ' Movimiento ' . substr( $start , 0 , 6 ) . ' a ' . substr( $end , 0 , 6 );

            return Excel::create( $title , function( $excel ) use ( $data )
            {
                $excel->setTitle( 'Historial del Presupuesto SIM' );
                $excel->setCreator( 'Laboratorios Bago | Peru' )->setCompany( 'Laboratorios Bago | Peru' );
                $excel->sheet( 'Historial del Fondo' , function( $sheet ) use ( $data )
                {
                    $sheet->loadView( 'Tables.export_fondo_mkt_history' , $data );
                });
            })->download( 'xlsx' );
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    private function getFondoHistoryData( $start , $end , $subCategoryId )
    {
        $subCategory = FondoSubCategoria::find( $subCategoryId );
        
        $startPeriod                = substr( $start , 0 , 4 ) . substr( $start , 4 , 2 );
        $fondoMktPeriodHistoryModel = new FondoMktPeriodHistory;
        $lastPeriod                 = $fondoMktPeriodHistoryModel->maxFundPeriod( $subCategoryId , $startPeriod );
        
        if( is_null( $lastPeriod ) )
        {
            if( trim( $subCategory->tipo ) == SUP )
            {
                $pptoSupervisorModel = new PPTOSupervisor;
                $saldo_inicial       = $pptoSupervisorModel->sumCategoryAmount( $subCategoryId , substr( $start , 0 , 4 ) ); 
                $retencion_inicial   = 0;
            }
            else if( trim( $subCategory->tipo ) == GER_PROD || trim( $subCategory->tipo ) == GER_PROM )
            {
                $pptoGerenteModel  = new PPTOGerente;
                $saldo_inicial     = $pptoGerenteModel->sumCategoryAmount( $subCategoryId , substr( $start , 0 , 4 ) ); 
                $retencion_inicial = 0;
            }
            else if( trim( $subCategory->tipo ) == 'I' )
            {
                $pptoInsModel      = new PPTOInstitucion;
                $saldo_inicial     = $pptoInsModel->sumCategoryAmount( $subCategoryId , substr( $start , 0 , 4 ) ); 
                $retencion_inicial = 0;
            }
            else
            {
                $saldo_inicial     = 0;
                $retencion_inicial = 0;
            }
        }
        else
        {
            $fondoMktPeriodHistory            = new FondoMktPeriodHistory;
            $fondoMktHistoriesStartPeriodData = $fondoMktPeriodHistory->getPeriodData( $subCategoryId , $lastPeriod );
            $saldo_inicial                    = $fondoMktHistoriesStartPeriodData->saldo_final;
            $retencion_inicial                = $fondoMktHistoriesStartPeriodData->retencion_final;
        }

        $subCategoryType        = $subCategory->fondoMktType;
        $fundHistoryModel       = new FondoMktHistory;
        $fundHistoryData        = $fundHistoryModel->getSubCategoryData( $subCategoryId , $subCategory->tipo , $subCategoryType->relacion , $start , $end );
        $periodTotal            = $fundHistoryData->diff_saldo;
        $periodTotalRetencion   = $fundHistoryData->diff_retencion;

        $saldoContable  = $saldo_inicial     - $periodTotal;
        $saldoRetencion = $retencion_inicial - $periodTotalRetencion;

        $fundHistoryBalanceData = $fundHistoryModel->getSubCategoryBalanceData( $subCategoryId , $subCategory->tipo , $subCategoryType->relacion , $start , $end );
        
        return 
        [ 
            'FondoMktHistories' => $fundHistoryBalanceData ,
            'saldo'             => $saldo_inicial ,
            'saldoContable'     => $saldoContable ,
            'saldoNeto'         => $saldoContable - $saldoRetencion
        ];
    }

    public function refund( $solicitud , $monto_renovado , $type )
    {
        $tc = ChangeRate::getTc();
        $detalle = $solicitud->detalle;
        
        $tasaCompra = $this->getExchangeRate( $solicitud );
        $fondoDataHistories = array();
        if ( $solicitud->idtiposolicitud == SOL_REP )
        {
            $solicitudProducts = $solicitud->products;
            $fondo_type        = $solicitud->products[ 0 ]->id_tipo_fondo_marketing;
            $monto_aprobado    = $solicitud->detalle->monto_aprobado;
            foreach( $solicitudProducts as $solicitudProduct )
            {
                $fondo          = $solicitudProduct->thisSubFondo;
                $oldSaldo       = $fondo->saldo;
                $oldRetencion   = $fondo->retencion;
                $monto_renovado_final = ( $monto_renovado / $monto_aprobado ) * $solicitudProduct->monto_asignado;
                $monto_renovado_final = $monto_renovado_final * $tasaCompra;
                
                $fondo->saldo       += $monto_renovado_final ;
                
                $data = array( 
                    'idFondo'      => $fondo->id , 
                    'idFondoTipo'  => $fondo_type ,
                    'oldSaldo'     => $oldSaldo , 
                    'newSaldo'     => $fondo->saldo ,
                    'oldRetencion' => $oldRetencion , 
                    'newRetencion' => $fondo->retencion ,
                    'reason'       => $type );
                $fondoDataHistories[] = $data;
                $this->setPeriodHistoryData( $fondo->subcategoria_id , $data );
                $fondo->save();
            }
        }
        elseif ( $solicitud->idtiposolicitud == SOL_INST )
        {
            $fondo = $solicitud->detalle->thisSubFondo;
            $oldSaldo = $fondo->saldo;
            $oldRetencion = $fondo->retencion;
            $fondo->saldo       += $monto_renovado;
            $data = array( 
               'idFondo'      => $fondo->id , 
               'idFondoTipo'  => INVERSION_INSTITUCIONAL ,
               'oldSaldo'     => $oldSaldo , 
               'newSaldo'     => $fondo->saldo , 
               'oldRetencion' => $oldRetencion ,
               'newRetencion' => $fondo->retencion ,
               'reason'       => $type );
            $fondoDataHistories[] = $data;
            $this->setPeriodHistoryData( $fondo->subcategoria_id , $data ); 
            $fondo->save();
        }
        $this->setFondoMktHistories( $fondoDataHistories , $solicitud->id );  
    }

}
