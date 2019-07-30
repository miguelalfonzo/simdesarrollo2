<?php

namespace Movements;

use \BaseController;
use \Input;
use \Log;
use \DateTime;
use \Auth;
use \View;
use \Expense\ChangeRate;
use \Expense\ProofType;
use \Expense\Expense;
use \Custom\DataList;
use \Validator;
use \Exception;
use \Dmkt\Solicitud;
use \Carbon\Carbon;
use \Fondo\FondoSubCategoria;
use \Session;

class MoveController extends BaseController
{
    
    public function __construct()
    {
        parent::__construct();
        $this->beforeFilter('active-user');
    }

    private function searchMove( $start , $end , $subCategoriaId )
    {
    	
        $dates = $this->setDates( $start , $end );
        $data = $this->searchSolicituds( R_TODOS , $dates , $subCategoriaId , 'MOVIMIENTOS' );
        
        foreach ( $data as $solicitud )
        { 
            $detalle = $solicitud->detalle;
            $jDetalle = json_decode($detalle->detalle);
            $deposito = $detalle->deposit;
            
            if ( $detalle->id_moneda == DOLARES )
                $solicitud->saldo = $detalle->typeMoney->simbolo . ' ' . ( $detalle->monto_actual - $solicitud->expenses->sum('monto') );
            elseif ( $detalle->id_moneda == SOLES )
                $solicitud->saldo = $detalle->typeMoney->simbolo . ' ' . ( $detalle->monto_actual - $solicitud->expenses->sum('monto') );
            else
                $solicitud->saldo = 'El Tipo de Moneda es: '.$detalle->id_moneda ;
        }
        $view = View::make('Tables.movimientos')->with( array( 'solicituds' => $data , 'subCategoriaId' =>$subCategoriaId ) )->render();
        $rpta = $this->setRpta( [ 'View' => $view ] );
        
        if ( Auth::user()->type == TESORERIA )
        {
            $soles = $data->sum( function( $solicitud )
            {
                $deposito = $solicitud->detalle->deposit;
                $moneda = $deposito->account->typeMoney;
                if ( $moneda->id == SOLES )
                    return $solicitud->detalle->deposit->total;
            });
            $dolares = $data->sum( function( $solicitud )
            {
                $deposito = $solicitud->detalle->deposit;
                $moneda = $deposito->account->typeMoney;
                if ( $moneda->id == DOLARES )
                    return $solicitud->detalle->deposit->total;
            });
            $rpta[ data ][ 'Total' ] = array( 'Soles' => $soles , 'Dolares' => $dolares );
        }
        return $rpta;
    }

    private function setDates( $start , $end )
    {
        $dates = array(
            'start' => Carbon::createFromFormat( 'd/m/Y' , $start )->format( '01/m/Y' ) ,
            'end'   => Carbon::createFromFormat( 'd/m/Y' , $end )->format( 't/m/Y' )
        );
        return $dates;
    }

    public function searchDocs()
    {
        try
        {
            $inputs = Input::all();
            $rules = array( 'date_start' => 'required|date_format:"d/m/Y"' , 'date_end' => 'required|date_format:"d/m/Y"' );
            $validator = Validator::make( $inputs , $rules );
            if ( $validator->fails() ) 
                return $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );
            
            $middleRpta = $this->getDocs( $inputs[ 'idProof' ] , $inputs[ 'date_start' ] , $inputs[ 'date_end' ] , $inputs[ 'val' ] );
        
            if ( $middleRpta[status] == ok )
                return $this->setRpta( View::make('Dmkt.Cont.list_documents')->with( 'proofs' , $middleRpta[data] )->render() );
            else
                return $middleRpta;    
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    private function getDocs( $idProof , $start , $end , $val )
    {
        $documents = Expense::orderBy( 'updated_at' , 'desc');
        $documents->where( 'idcomprobante' , $idProof );

        if ( ! empty( trim( $val ) ) )
        {
            $documents->where( function ( $q ) use ( $val )
            {
                if ( is_numeric( $val) )
                    $q->where( 'num_serie' , 'like' , '%' . $val . '%' )->orWhere( 'RUC' , 'like' , '%' . $val . '%' );
                $q->orWhere( 'UPPER( razon )' , 'like' , '%' . mb_strtoupper( $val ) . '%' )->orWhere( 'UPPER( num_prefijo )' , 'LIKE' , '%' . mb_strtoupper( $val ) . '%' );
            });
        }
        $documents->whereRaw( "fecha_movimiento between to_date('$start','DD/MM/YYYY') and to_date('$end','DD/MM/YYYY') ");
        return $this->setRpta( $documents->get() );
    }

    public function getTable()
    {
        try
        {
            $inputs = Input::all();
            switch( $inputs['type'] )
            {
                case 'movimientos':
                    return $this->searchMove( $inputs['date_start' ] , $inputs[ 'date_end' ] , $inputs[ 'filter' ] );
            }
        }
        catch ( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    private function formatAnioMes( $date )
    {
        return Carbon::createFromFormat( 'd/m/Y' , $date )->format( 'Ym' );
    }

    protected function searchSolicituds( $estado , array $dates , $filter )
    {
        $solicituds = Solicitud::where( function( $query ) use( $dates )
        {
            $query->where( function( $query ) use( $dates )
            {
                $query->where( 'idtiposolicitud' , '<>' , SOL_INST )->whereRaw( "created_at between to_date( '" . $dates[ 'start' ] . "','DD-MM-YY') and to_date( '" . $dates[ 'end' ] . "' ,'DD-MM-YY')+1" );
            })->orWhere( function( $query ) use( $dates )
            {
                $query->where( 'idtiposolicitud' , SOL_INST )->wherehas( 'detalle' , function ( $query ) use( $dates )
                {
                    $query->whereHas(TB_PERIODO, function( $query ) use( $dates )
                    {
                        $query->where( 'aniomes' , '>=' , $this->formatAnioMes( $dates[ 'start' ] ) )->where( 'aniomes' , '<=' , $this->formatAnioMes( $dates[ 'end' ] ) );
                    });
                });  
            });
        });
        
        if( Auth::user()->type == REP_MED )
        {
            $solicituds->where( 'id_user_assign' , Auth::user()->id );
        }
        elseif( in_array( Auth::user()->type , array ( SUP , GER_PROD , GER_PROM ) ) )
        {
            $solicituds->where( function ( $query )
            {
                $userIds = array( Auth::user()->id , Auth::user()->tempId() );
                $query->whereHas( 'gerente' , function( $query ) use ( $userIds )
                {
                    $query->whereIn( 'id_gerprod' , $userIds )->where( 'tipo_usuario' , Auth::user()->type );
                })->orWhereIn( 'created_by' , $userIds )
                ->orWhereIn( 'id_user_assign' , $userIds )
                ->orWhereIn( 'created_by' , Auth::user()->personal->employees->lists( 'user_id' ) )
                ->orWhereIn( 'id_user_assign' , Auth::user()->personal->employees->lists( 'user_id' ) );
            });
        }

        if ( $filter != 0 )
        {
            $solicituds->where( function ( $query ) use( $filter )
            {
                $query->where( 'idtiposolicitud' , SOL_REP )->whereHas( 'products' , function( $query ) use( $filter )
                {
                    $query->where( function( $query ) use( $filter )
                    {
                        $query->where( 'id_tipo_fondo_marketing' , SUP )->whereHas( 'fondoSup' , function( $query ) use( $filter )
                        {
                            $query->where( 'subcategoria_id' , $filter );
                        })->orWhere( function( $query ) use( $filter )
                        {
                            $query->where( 'id_tipo_fondo_marketing' , GER_PROD )->whereHas( 'fondoGerProd' , function( $query) use( $filter )
                            {
                                $query->where( 'subcategoria_id' , $filter );
                            });
                        });
                    });
                })->orWhere( function( $query ) use( $filter )
                {
                    $query->where( 'idtiposolicitud' , SOL_INST )->whereHas( 'detalle' , function( $query ) use( $filter )
                    {
                        $query->whereHas( 'thisSubFondo' , function( $query ) use( $filter )
                        {
                            $query->where( 'subcategoria_id' , $filter );
                        });
                    });
                });
            });
        }
        
        if( $estado != R_TODOS )
        {
            $solicituds->whereHas( 'state' , function ( $q ) use( $estado )
            {
                $q->whereHas( 'rangeState' , function ( $t ) use( $estado )
                {
                    $t->where( 'id' , $estado );
                });
            });
        }
        $solicituds->with( 'activity' );

        return $solicituds->orderBy('id', 'ASC')->get();
    }

    public function getSolicitudDetail()
    {
        try
        {
            $inputs    = Input::all();
            $solicitud = Solicitud::find( $inputs[ 'id_solicitud'] );
            $data      = array(
                'solicitud'     => $solicitud, 
                'politicStatus' => false, 
                'detalle'       => $solicitud->detalle, 
                'view'          => true  );
            return $this->setRpta( array(
                'View' => View::make( 'Dmkt.Solicitud.Tab.tabs' )->with( $data )->render() )
            );
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    // public function getStatement()
    // {
    //     if ( in_array( Auth::user()->type , array( REP_MED , GER_PROM , GER_COM , CONT ) ) )
    //         $fondos = FondoSubCategoria::all();
    //     elseif( in_array( Auth::user()->type , array( SUP , GER_PROD ) ) )
    //         $fondos = FondoSubCategoria::where( 'trim( tipo )' , Auth::user()->type )->get();
        
    //     return View::make( 'template.tb_estado_cuenta' , array( 'fondosMkt' => $fondos ) );
    // }


    public function getStatement()
    {   
        $fondos=FondoSubCategoria::FondoSP();
       
        return View::make( 'template.tb_estado_cuenta' , array( 'fondosMkt' => $fondos ) );
    }

    public function getSolicituds()
    {
        try
        {
            $inputs = Input::all();
            $dates  = [ 'start' => $inputs[ 'fecha_inicio' ] , 'end' => $inputs[ 'fecha_final' ] ];
            $data   = $this->searchUserSolicituds( $inputs[ 'estado' ] , $dates , null );

            if( isset( $data[ status ] ) && $data[ status ] == error )
            {
                return $data;
            }

            $columns =
                [
                    [ 'title' => '#' , 'data' => 'id' , 'className' => 'text-center' , 'width' => '5%'],
                    [ 'title' => 'Solicitud' , 'data' => 'actividad_titulo' ],
                    [ 'title' => 'Solicitador por' , 'data' => 'crea_nom' , 'width' => '5%' , 'className' => 'text-center' ],
                    [ 'title' => 'Fecha de Solicitud' , 'data' => 'crea_fec' , 'width' => '5%' ,  'className' => 'text-center' ],
                    [ 'title' => 'Aprobado por' , 'data' => 'rev_nom' , 'width' => '5%' , 'className' => 'text-center' ],
                    [ 'title' => 'Fecha de Aprobación' , 'data' => 'rev_fec' , 'width' => '5%' , 'className' => 'text-center' ],
                    [ 'title' => 'Monto' , 'data' => 'monto' , 'className' => 'text-center' , 'width' => '5%' ],
                    [ 'title' => 'Estado' , 'data' => 'estado' , 'className' => 'text-center' , 'width' => '5%'],
                    [ 'title' => 'Tipo' , 'data' => 'sol_tip_nom' , 'className' => 'text-center' , 'width' => '10%'],
                    [ 'title' => 'Edición' , 'data' => 'opciones' , 'width' => '15%' , 'className' => 'text-center'  ],
                ];
            $user = Auth::user();
            if( $user->type == GER_COM )
            {
                $columns[] = [ 'title' => 'X' , 'data' => 'aprobacion_masiva' , 'className' => 'text-center' , 'defaultContent' => ''  ];
            }
            elseif( $user->type == CONT )
            {
                $columns[ 3 ] = [ 'title' => 'Fecha de Deposito' , 'data' => 'entr_fec' , 'className' => 'text-center' , 'width' => '5%'];
            }
            elseif( $user->type == TESORERIA )
            {
                $columns[ 2 ] = [ 'title' => 'Responsable' , 'data' => 'resp_nom' , 'className' => 'text-center' ];
                $columns[ 3 ] = [ 'title' => 'Fecha de Deposito' , 'data' => 'entr_fec' , 'className' => 'text-center' , 'width' => '10%' ];
                $columns[ 4 ] = [ 'title' => 'Deposito' , 'data' => 'monto' , 'className' => 'text-center' , 'width' => '5%' ];
                unset( $columns[ 5 ] , $columns[ 6 ] );
                $columns = array_values( $columns );
                      
            }
            
            $rpta = $this->setRpta( $data );
            $rpta[ 'usuario' ] = [ 'tipo' => $user->type ];
            $now = Carbon::now();
            $rpta[ 'now' ] = [ 'year' => $now->year , 'month' => $now->month , 'day' => $now->day ];
            $rpta[ 'columns' ] = $columns;
            
            Session::put( 'state' , $inputs[ 'estado' ] );
            Session::put( 'start_date' ,  $inputs[ 'fecha_inicio' ] );
            Session::put( 'end_date' ,  $inputs[ 'fecha_final' ] );
            
            return $rpta;
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    protected function searchUserSolicituds( $estado , array $dates , $filter , $type = 'FLUJO' )
    {
        return DataList::getSolicituds( $dates , $estado );
    }

}