<?php

namespace Dmkt;

use \BaseController;
use Users\Personal;
use \View;
use \DB;
use \Input;
use \Redirect;
use \Auth;
use \Validator;
use \Excel;
use \Expense\Entry;
use \Expense\ProofType;
use \Exception;
use \Users\Rm;
use \Users\Sup;
use \Common\TypeMoney;
use \yajra\Pdo\Oci8\Exceptions\Oci8Exception;
use \Client\Institution;
use \Fondo\FondoMkt;

class FondoController extends BaseController
{
    
    private function period( $date )
    {
        $period = explode('-', $date);
        return $period[1].str_pad($period[0], 2, '0', STR_PAD_LEFT);
    }

    private function nextPeriod( $periodo )
    {
        $anio = substr( $periodo , 0 , 4 );
        $mes = substr( $periodo , 4 , 2 );
        if ( $mes == 12 )
        {
            $anio = $anio + 1;
            $mes = '01';
        }
        else
            $mes = str_pad( ( $mes + 1 ) , 2 , '0' , STR_PAD_LEFT);
        return $mes.'-'.$anio;
    }

    public function getSolInst()
    {
        try
        {
            $inputs = Input::all();
            $rules = array( 'idsolicitud' => 'required|integer|min:1|exists:solicitud,id' );
            $validator = Validator::make( $inputs , $rules );
            if ( $validator->fails() )
                return $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );
            $solInst = Solicitud::find( $inputs['idsolicitud']);
            $detalle = $solInst->detalle;
            $jDetalle = json_decode($detalle->detalle);
            $rm = $solInst->assignedTo->personal;
            $institution = $solInst->clients()->where( 'id_tipo_cliente' , 3)->first();
            $data = array( 'rm'              => strtoupper( $rm->nombres.' '.$rm->apellidos ) ,
                           'idrm'            => $rm->bago_id ,
                           'monto'           => $jDetalle->monto_solicitado ,
                           'periodo'         => $detalle->periodo->aniomes ,
                           'idfondo'         => $detalle->id_fondo ,
                           'institucion'     => $institution->institution->pejrazon ,
                           'institucion-cod' => $institution->id_cliente , 
                           'idinversion'     => $solInst->id_inversion );
            return $this->setRpta( $data );
        }
        catch ( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    private function sumSolicitudInst( $solicitud )
    {
        $monedas = TypeMoney::all();
        $totales = array();
        foreach ( $monedas as $moneda )
            $totales[$moneda->simbolo] = 0 ;
        foreach ( $solicitud as $sol )
        {
            $detalle = $sol->detalle;
            $totales[ 'S/.' ] += $detalle->monto_actual ;
        }
        return $this->setRpta( $totales );
    }

    private function getStringTotal( $totales )
    {
        $rpta = 'Total: ' ;
        foreach ( $totales as $key => $total )
            if ( $total != 0 )
                $rpta .= $key.' '.$total.' , ';
        return substr( $rpta , 0 , -2 );
    }

    public function endFondos( $start )
    {
        try
        {
            $periodo = $this->period( $start );
            $periodos = Periodo::where( 'aniomes' , $periodo )->first();
            if ( is_null( $periodos ) )
                return $this->warningException( 'El periodo seleccionado no ha sido activado: '.$periodo , __FUNCTION__ , __LINE__ , __FILE__ );
            elseif ( $periodos->status == BLOCKED )
                return $this->warningException( 'El periodo ya se encuentra Terminado' , __FUNCTION__ , __LINE__ , __FILE__ );
            
            $solicituds = Solicitud::solInst( $periodo );
            if ( $solicituds->count() === 0 )
                return $this->warningException( 'No se encontro solicitudes para el periodo especificado: ' . $periodo , __FUNCTION__ , __LINE__ , __FILE__);
        
            DB::beginTransaction();
            $middleRpta = $this->endSolicituds( $solicituds );
            if ( $middleRpta[status] == ok )
            {
                $periodos->status = BLOCKED ;
                $periodos->save();
                DB::commit();
            }
            else
            {
                DB::rollback();
            }
            return $middleRpta;
        }
        catch (Exception $e)
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    private function endSolicituds( $solicituds )
    {
        $fondoMktController = new FondoMkt;
        $msgWarning   = '';
        $historiesFondoMkt = array();
        foreach( $solicituds as $solicitud )
        {
            $detalle = $solicitud->detalle;
            $fondo = $detalle->thisSubFondo;
            if ( $fondo->saldo_disponible < $detalle->monto_solicitado )
                return $this->warningException( 'No se cuenta con saldo en el fondo ' . $fondo->subCategoria->descripcion . ' para terminar los Fondos Institucionales.'  , __FUNCTION__ , __LINE__ , __FILE__ );
            else
                $fondoMktController->setHistoryData( $historiesFondoMkt , $fondo , 1 , $detalle->monto_solicitado , 'I' , FONDO_RETENCION );
               
            $jDetalle                 = json_decode( $detalle->detalle );
            $jDetalle->monto_aprobado = $jDetalle->monto_solicitado;
            $detalle->detalle         = json_encode( $jDetalle );
            $detalle->save();

            $oldIdestado          = $solicitud->id_estado;
            $solicitud->id_estado = DEPOSITO_HABILITADO;
            $solicitud->save();            
            
            $middleRpta = $this->setStatus( $oldIdestado , DEPOSITO_HABILITADO , Auth::user()->id , USER_TESORERIA , $solicitud->id );
            if ( $middleRpta[status] != ok )
                return $middleRpta;
            
            $inputs = array( 'institucion-cod' => $solicitud->clients()->where( 'id_tipo_cliente' , 3 )->first()->id_cliente ,
                             'codrepmed'       => $solicitud->assignedTo->personal->bago_id,
                             'total'           => $solicitud->detalle->monto_aprobado,
                             'fondo_producto'  => $solicitud->detalle->id_fondo,
                             'mes'             => $this->nextPeriod( $solicitud->detalle->periodo->aniomes ) ,
                             'inversion'       => $solicitud->id_inversion );
            
            $middleRpta = $this->processsInstitutionalSolicitud( $inputs );
            if ( $middleRpta[ status ] != ok )
                $msgWarning .= $middleRpta[ description ];

        }
        $fondoMktController->setFondoMktHistories( $historiesFondoMkt , $solicitud->id );
        if ( $msgWarning === '' )
            return $this->setRpta();
        else
            return array( status => warning , description => $msgWarning );
    }
    
    private function validateOneInstitution( $idPeriodo , $cod )
    {
        $listsClients = SolicitudClient::wherehas( 'solicitud' , function( $query ) use( $idPeriodo )
                        {
                            $query->where( 'id_estado' , PENDIENTE )->whereHas( 'detalle' , function( $query ) use( $idPeriodo )
                            {
                                $query->where( 'id_periodo' , $idPeriodo );
                            });
                        })->lists( 'id_cliente');

        if ( in_array( $cod , $listsClients ) )
            return $this->warningException( 'Solo puedo asignar la institucion una sola vez por periodo' , __FUNCTION__ , __LINE__ , __FILE__ );
        else
            return $this->setRpta();
    }

    private function setDetalleInst( $detalle , $inputs , $idPeriodo , $idSolicitud )
    {
        $jDetalle = array( 'supervisor'      => $inputs[ 'supervisor' ] ,
                           'codsup'          => $inputs[ 'codsup' ] ,
                           'num_cuenta'      => $inputs[ 'cuenta' ] ,
                           'monto_solicitado'=> $inputs[ 'total' ] );
        $detalle->id_fondo   = $inputs[ 'fondo_producto' ] ;
        $detalle->id_periodo = $idPeriodo;
        $detalle->id_moneda  = SOLES;
        $detalle->detalle = json_encode($jDetalle);
        $detalle->save();
        SolicitudClient::where( 'id_solicitud' , $idSolicitud )->delete();
        $middleRpta = $this->validateOneInstitution( $idPeriodo , $inputs[ 'institucion-cod' ] );
        if ( $middleRpta[ status ] == ok )
        {
            $solicitudCliente     = new SolicitudClient;
            $solicitudCliente->id = $solicitudCliente->lastId() + 1;
            $solicitudCliente->id_tipo_cliente = 3;
            $solicitudCliente->id_solicitud = $idSolicitud;
            $solicitudCliente->id_cliente = $inputs[ 'institucion-cod' ];
            $solicitudCliente->save();
            return $this->setRpta();                            
        }
        return $middleRpta;
    }

    public function registerInstitutionalApplication()
    {
        try
        {
            DB::beginTransaction();
            $inputs = Input::all();
            $middleRpta = $this->validateInputSolInst( $inputs );
            if ( $middleRpta[status] == ok )
            {
                $middleRpta = $this->processsInstitutionalSolicitud( $inputs );
                if ( $middleRpta[ status] == ok )
                {
                    DB::commit();
                }
                else
                    DB::rollback();
                return $middleRpta;    
            }
            DB::rollback();
            return $middleRpta;
        }
        catch( Oci8Exception $e )
        {
            DB::rollback();
            return $this->internalException( $e , __FUNCTION__ , DB );
        }
        catch( Exception $e )
        {
            DB::rollback();
            return $this->internalException( $e , __FUNCTION__ );
        }        
    }

    private function processsInstitutionalSolicitud( $inputs )
    {
        $periodo = $this->period( $inputs['mes'] );
        $middleRpta = $this->verifyPeriodo( $periodo );
        if( $middleRpta[status] == ok )
        {
            $idPeriodo = $middleRpta[data];
            $middleRpta = $this->validateRm( $inputs['codrepmed'] );

            if( $middleRpta[status] == ok )
            {
                if ( isset( $inputs[ 'idsolicitud' ]) &&  $inputs[ 'idsolicitud'] != null )
                {
                    $solicitud = Solicitud::find( $inputs['idsolicitud'] );
                    $detalle = $solicitud->detalle;
                }
                else
                {
                    $solicitud               = new Solicitud;
                    $solicitud->id           = $solicitud->nextId();
                    $solicitud->token        = sha1( md5( uniqid( $solicitud->id , true ) ) );
                    $solicitud->id_inversion = $inputs[ 'inversion' ];
                    $detalle                 = new SolicitudDetalle;
                    $detalle->id             = $detalle->nextId();
                    $solicitud->id_detalle   = $detalle->id;
                }
                $inputs[ 'supervisor' ]     = $middleRpta[ data ][ 'sup' ];
                $inputs[ 'cuenta' ]         = $middleRpta[ data ][ 'cuentaRep' ];
                $inputs[ 'codsup']          = $middleRpta[ data ][ 'codsup' ];
                $razonInstitucion           = Institution::find( $inputs[ 'institucion-cod' ] )->pejrazon;
                $solicitud->titulo          = TITULO_INSTITUCIONAL . ' ' . $inputs[ 'mes' ] . ' - ' . $razonInstitucion;
                $solicitud->id_estado       = PENDIENTE;
                $solicitud->idtiposolicitud = SOL_INST;
                $solicitud->id_user_assign  = $middleRpta[data]['rm'];
                $solicitud->save();
                $middleRpta   = $this->setDetalleInst( $detalle , $inputs , $idPeriodo , $solicitud->id );
                if ( $middleRpta[status] == ok )
                {
                    $userid = Auth::user()->id;
                    $middleRpta = $this->setStatus( 0 , PENDIENTE, $userid , $userid, $solicitud->id );
                    if ( $middleRpta[status] == ok )
                        return $middleRpta;
                }
            }
        }
        return $middleRpta;
    }

    public function listDocuments()
    {
        $docs = ProofType::order();
        $view = View::make('Dmkt.Cont.list_documents_type')->with( 'docs' , $docs );
        return $view;
    }

    private function solicitudToArray( $solicituds )
    {
        $rpta = array();
        foreach ( $solicituds as $solicitud )
        {
            $detalle  = $solicitud->detalle;
            $data     = array();
            $data[]   = ( isset( $solicitud->clients()->where( 'id_tipo_cliente' , 3 )->first()->institution->pejrazon ) ? $solicitud->clients()->where( 'id_tipo_cliente' , 3 )->first()->institution->pejrazon : $solicitud->titulo );
            $data[]   = $solicitud->assignedTo->personal->full_name;
            $data[]   = $detalle->num_cuenta;
            $data[]   = 'S/.' . $detalle->monto_actual;
            $rpta[]   = $data ;
        }
        return $rpta;
    }

    public function exportExcelFondos( $start )
    {
        $solicituds = Solicitud::solInst( $this->period( $start ) );
        $data = $this->solicitudToArray( $solicituds );
        $sum  = $this->sumSolicitudInst( $solicituds );
        $mes  = array(
            '01' => 'enero',
            '02' => 'febrero',
            '03' => 'marzo',
            '04' => 'abril',
            '05' => 'mayo',
            '06' => 'junio',
            '07' => 'julio',
            '08' => 'agosto',
            '09' => 'setiembre',
            '10' => 'octubre',
            '11' => 'noviembre',
            '12' => 'diciembre'
        );
        $st   = explode('-', $start);
        $mes  = $mes[str_pad($st[0], 2, '0', STR_PAD_LEFT)];
        $anio = $st[1];
        
        Excel::create('Fondo - '.str_pad($st[0], 2, '0', STR_PAD_LEFT).$anio , function($excel) use ( $data , $sum , $mes , $anio )
        {  
            $excel->sheet($mes, function($sheet) use ( $data , $sum , $mes , $anio )
            {
                $sheet->mergeCells('A1:D1');
                $sheet->row( 1 , array( 'FONDO INSTITUCIONAL ' . strtoupper($mes) . ' ' . $anio ));
                $sheet->row( 1 , function( $row )
                {
                    $row->setAlignment('center');
                    $row->setFont( array(
                        'family' => 'Calibri',
                        'size' => '20',
                        'bold' => true
                    ) );
                    $row->setBackground('#339246'); 
                });
                $sheet->setHeight(1, 30);
                $count = count( $data ) + 2;
                $sheet->setBorder( 'A1:D' . $count, 'thin' );
                $sheet->setHeight( 2 , 20 );
                $sheet->row( 2 , array(
                    'SISOL  - Hospital',
                    'Depositar a:',
                    'Nº Cuenta Bagó Bco. Crédito',
                    //'SUPERVISOR',
                    //'Moneda',
                    'Monto Real'
                ) );
                $sheet->row(2, function($row)
                {
                    $row->setFont( array(
                        'family' => 'Calibri',
                        'size' => '15',
                        'bold' => true
                    ) );
                    $row->setBackground('#D2E718');
                    $row->setAlignment('center');
                });      
                $sheet->fromArray( $data , null , 'A3' , false , false );
                $i= 0 ;
                foreach( $sum[data] as $moneda => $total )
                {
                    if ( $total != 0)
                    {
                        $i++;
                        $sheet->row( $count + $i , array( '' , '' , 'Total' , $moneda . $total ) );
                    }   
                } 
            });  
        })->download('xls');    
    }


    private function validateRm( $codrepmed )
    {
        $repmed = Personal::getRM($codrepmed);
        if ( is_null( $repmed ) )
        {
            return $this->warningException( 'El representante Medico no esta registrado en el sistema. Codigo de Representante: ' . $codrepmed , __FUNCTION__ , __LINE__ , __FILE__ );
        }
        $bagoRepmed = $repmed->bagoVisitador;
        if( is_null( $repmed->bagoVisitador ) )
        {
            return $this->warningException( 'No se ha encontrado el codigo de representante en el Fichero Medico. Codigo del Representante: ' . $codrepmed , __FUNCTION__ , __LINE__ , __FILE__ );
        }
        $bagoRepmedCuenta = $bagoRepmed->cuenta;
        if( is_null( $bagoRepmedCuenta ) ) 
        {
            return $this->warningException( 'No se ha encontrado el registro de la cuenta BAGO del representante. Codigo del Representante: ' . $codrepmed , __FUNCTION__ , __LINE__ , __FILE__ );
        }

        $codsup = $repmed->referencia_id;
        $sup = Personal::getSupvervisor( $codsup );
        if ( is_null( $sup) )
        {
            return $this->warningException( 'El Supervisor no esta registrado en el sistema. Codigo de Supervisor: ' . $codsup , __FUNCTION__ , __LINE__ , __FILE__ );
        }
        return $this->setRpta( array( 'rm' => $repmed->user_id , 'sup' => $sup->user_id , 'codsup' => $codsup , 'cuentaRep' => $bagoRepmedCuenta->cuenta ) );
    }

    private function verifyPeriodo( $periodo )
    {
        $periodos = Periodo::periodoInst( $periodo );
        if( count( $periodos ) == 0 )
        {
            $newPeriodo = new Periodo;
            $newPeriodo->id = $newPeriodo->lastId() + 1 ;
            $newPeriodo->aniomes = $periodo ;
            $newPeriodo->status  = ACTIVE ;
            $newPeriodo->idtiposolicitud = SOL_INST ;
            $newPeriodo->save();
            return $this->setRpta( $newPeriodo->id );
        }

        if ( $periodos->status == BLOCKED )
            return $this->warningException( 'El periodo ingresado ya ha sido terminado: '.$periodo , __FUNCTION__ , __LINE__ , __FILE__ );
        elseif( $periodos->status == INACTIVE )
        {
            $periodos->status = ACTIVE ;
            $periodos->save();
            return $this->setRpta( $periodos->id );           
        }
        elseif ( $periodos->status == ACTIVE )
            return $this->setRpta( $periodos->id );
        else
            return $this->warningException( 'Estado: '.$periodos->status.' no registrado' , __FUNCTION__ , __LINE__ , __FILE__ );
    }


    private function validateInputSolInst( $inputs )
    {
        $rules = array( 'idsolicitud'     => 'sometimes|integer|min:1|exists:'.TB_SOLICITUD.',id,id_estado,1,idtiposolicitud,'.SOL_INST ,
                        'institucion-cod' => 'required|numeric|min:1',
                        'codrepmed'       => 'required|integer|min:1|exists:'.TB_VISITADOR.',visvisitador',
                        'total'           => 'required|numeric|min:1',
                        'fondo_producto'  => 'required|string|min:1',
                        'mes'             => 'required|string|date_format:m-Y|after:'.date("Y-m"),
                        'inversion'       => 'required|exists:tipo_inversion,id' );
        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) 
            return $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );

        return $this->setRpta();
    }

    public function listInstitutionalSolicitud( $start )
    {
        try
        {
            $periodo  = $this->period( $start );
            $periodos = Periodo::where('aniomes', $periodo )->first();
            if ( is_null( $periodos ) )
                return $this->setRpta( View::make('Tables.solicitud_institucional')->with( 'total' , '' )->render() );
            
            $solicitud = Solicitud::solInst( $periodo );
            $middleRpta = $this->sumSolicitudInst( $solicitud );
            if ( $middleRpta[status] == ok )
            {   
                $data = array( 'solicituds' => $solicitud ,
                               'state'      => $periodos->status , 
                               'total'      => $this->getStringTotal( $middleRpta[data] ) );
               return $this->setRpta( array( 'View' => View::make('Tables.solicitud_institucional' )->with( $data )->render() ) );
            }
            return $middleRpta;
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }
}