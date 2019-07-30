<?php

namespace Deposit;

use \BaseController;
use \View;
use \Session;
use \Auth;
use \Common\Deposit;
use \Dmkt\Solicitud;
use \Input;
use \DB;
use \Exception;
use \Expense\ChangeRate;
use \Expense\PlanCta;
use \Validator;
use \Fondo\FondoMkt;
use \User;
use \Devolution\DevolutionController;
use \Carbon\Carbon;
use \Excel;
use \File;
use \Response;

class DepositController extends BaseController
{

    private function objectToArray($object)
    {
        $array = array();
        foreach ( $object as $member => $data )
            $array[$member] = $data;
        return $array;
    }

    private function verifyMoneyType( $solIdMoneda , $bankIdMoneda , $monto , $tc , $jDetalle )
    {
        $jDetalle->tcc = $tc->compra;
        $jDetalle->tcv = $tc->venta;
        if ( $solIdMoneda != $bankIdMoneda )
        {
            if ( $solIdMoneda == SOLES )
                $monto = $monto / $tc->compra;
            elseif ( $solIdMoneda == DOLARES )
                $monto = $monto * $tc->venta;
            else
                return $this->warningException( 'Tipo de Moneda no Registrada con Id: '.$solIdMoneda , __FUNCTION__ , __LINE__ , __FILE__ );
            return $this->setRpta( array( 'monto' => $monto , 'jDetalle' => $jDetalle ) );
        }
        else
            return $this->setRpta( array( 'monto' => $monto , 'jDetalle' => $jDetalle ) );
    }

    private function getBankAmount( $detalle , $bank , $tc )
    {
        $jDetalle = json_decode( $detalle->detalle );
        return $this->verifyMoneyType( $detalle->id_moneda , $bank->idtipomoneda , $jDetalle->monto_aprobado , $tc , $jDetalle );
    }

    private function validateInputsDeposit( $inputs )
    {
        $rules = array(
                'token'     => 'required|exists:solicitud,token,id_estado,' . DEPOSITO_HABILITADO ,
                'cuenta'    => 'required|numeric|exists:'.TB_PLAN_CUENTA.',ctactaextern',
                'operacion' => 'required|string|min:1'
            );
        $messages = array(
                'token.exists' => 'La solicitud no se encuentra en la etapa de deposito'
            );
        $validator = Validator::make( $inputs , $rules , $messages );
        if ( $validator->fails() )
        {
            return $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );
        }
        return $this->setRpta();
    }

    public function massiveSolicitudDeposit()
    {
        try
        {
            $inputs = Input::all();
            $responses = [];
            foreach( $inputs[ 'data' ] as $data )
            {
                $inputs =
                    [
                        'token'     => $data[ 'token' ],
                        'cuenta'    => $inputs[ 'cuenta' ],
                        'operacion' => $data[ 'operacion' ]
                    ];
                $middleRpta  = $this->validateInputsDeposit( $inputs );
                if( $middleRpta[ status ] === ok )
                {
                    $middleRpta = $this->depositOperation( $data[ 'token' ] , $data[ 'operacion' ] , $inputs[ 'cuenta' ] );
                }
                $middleRpta[ description ]  = trim( $middleRpta[ description ] , '<br>' );
                $middleRpta[ 'operacion' ]  = $data[ 'operacion' ];
                $responses[ $data[ 'id' ] ] = $middleRpta;
            }
            Session::put( 'depositos' , $responses );
            $status = array_unique( array_pluck( $responses , status ) );
            if( count( $status ) === 1 && $status[ 0 ] === ok )
            {
                return $this->setRpta( $responses , 'Registro realizado correctamente' );
            }
            elseif( in_array( ok , $status , 1 ) )
            {
                return $this->setRpta( $responses , 'Registro realizado parcialmente' );
            }
            else
            {
                $rpta = $this->warningException( 'No se pudo realizar el registro. Existen las siguientes observaciones' , __FUNCTION__ , __LINE__ , __FILE__ );
                $rpta[ data ] = $responses;
                return $rpta;
            }
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    public function solicitudDeposit()
    {
        try
        {
            $inputs      = Input::all();
            $middleRpta  = $this->validateInputsDeposit( $inputs );
            if( $middleRpta[ status ] === ok )
            {
                return $this->depositOperation( $inputs[ 'token' ] , $inputs[ 'operacion' ] , $inputs[ 'cuenta' ] );
            }
            return $middleRpta;
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ , __LINE__ , __FILE__ );
        }
    }

    public function depositOperation( $solicitudToken , $operationCode , $bankAccount )
    {
        try
        {
            $modelAccount    = PlanCta::find( $bankAccount );
            $modelSIMAccount = $modelAccount->account;

            if ( $modelSIMAccount->idtipocuenta != BANCO )
            {
                return $this->warningException( 'Cancelado - La cuenta N°: ' . $bankAccount . ' no ha sido registrada en el Sistema como Cuenta de Banco' , __FUNCTION__ , __LINE__ , __FILE__ );
            }

            return $this->depositTransaction( $solicitudToken , $modelSIMAccount , $operationCode );
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    private function depositTransaction( $solicitudToken , $modelSIMAccount , $operationCode )
    {
        DB::beginTransaction();
        $solicitud  = Solicitud::findByToken( $solicitudToken );
        $detalle    = $solicitud->detalle;
        $tc         = ChangeRate::getTc();
        $middleRpta = $this->getBankAmount( $detalle , $modelSIMAccount , $tc );
        if ( $middleRpta[ status ] === ok )
        {
            $newDeposit                     = new Deposit;
            $newDeposit->id                 = $newDeposit->lastId() + 1;
            $newDeposit->num_transferencia  = $operationCode;
            $newDeposit->num_cuenta         = $modelSIMAccount->num_cuenta;
            $newDeposit->total              = $middleRpta[data]['monto'];
            $newDeposit->save();

            $detalle->id_deposito = $newDeposit->id;
            $detalle->detalle     = json_encode( $middleRpta[ data ][ 'jDetalle' ] );
            $detalle->save();

            $oldIdestado          = $solicitud->id_estado;
            $solicitud->id_estado = DEPOSITADO;
            $solicitud->save();

            $middleRpta = $this->discountFondoBalance( $solicitud );

            if ( $middleRpta[ status ] == ok )
            {
                $middleRpta = $this->setStatus( $oldIdestado, DEPOSITADO , Auth::user()->id , USER_CONTABILIDAD , $solicitud->id );

                if ( $middleRpta[status] == ok )
                {
                    Session::put( 'state' , R_REVISADO );
                    DB::commit();
                    return $middleRpta;
                }
            }
        }
        DB::rollback();
        return $middleRpta;
    }

    private function discountFondoBalance( $solicitud )
    {
        $fondoMktController = new FondoMkt;
        $fondoDataHistories = array();
        $fondosData         = array();

        $tasaCompra           = $this->getExchangeRate( $solicitud );
        $tasaCompraAprobacion = $this->getApprovalExchangeRate( $solicitud );
        $msg                = ' el cual no es suficiente para completar el deposito , se requiere un saldo de S/.';

        if ( in_array( $solicitud->idtiposolicitud , array( SOL_REP , REEMBOLSO ) ) )
        {
            $products = $solicitud->products;
            $fondo_type = $products[ 0 ]->id_tipo_fondo_marketing;
            foreach( $products as $solicitudProduct )
            {
                $fondo            = $solicitudProduct->thisSubFondo;
                #dd($fondo);
                #die();

                $oldSaldo         = $fondo->saldo;
                $oldRetencion     = $fondo->retencion;
                $fondo->saldo     -= $solicitudProduct->monto_asignado * $tasaCompra;
                $fondo->retencion -= $solicitudProduct->monto_asignado * $tasaCompraAprobacion;
                if ( isset( $fondoData[ $fondo->id ] ) )
                    $fondosData[ $fondo->id ] += $solicitudProduct->monto_asignado;
                else
                    $fondosData[ $fondo->id ] = $solicitudProduct->monto_asignado;
                $data = array(
                   'idFondo'      => $fondo->id ,
                   'idFondoTipo'  => $fondo_type ,
                   'oldSaldo'     => $oldSaldo ,
                   'oldRetencion' => $oldRetencion ,
                   'newSaldo'     => $fondo->saldo ,
                   'newRetencion' => $fondo->retencion ,
                   'reason'       => FONDO_DEPOSITO );
                $fondoDataHistories[] = $data;
                $fondoMktController->setPeriodHistoryData( $fondo->subcategoria_id , $data );
                $fondo->save();
            }
            // EL DEPOSITO SE REGISTRARA AUNQUE EL SALDO DEL FONDO QUEDE EN NEGATIVO SE COMENTA LA FUNCION DE VALIDACION DEL SALDO
            /* $middleRpta = $fondoMktController->validateFondoSaldo( $fondosData , $fondo_type , $msg );
            if ( $middleRpta[ status] != ok )
                return $middleRpta; */
        }
        elseif ( $solicitud->idtiposolicitud == SOL_INST )
        {
            $detalle          = $solicitud->detalle;
            $fondo            = $detalle->thisSubFondo;
            $oldSaldo         = $fondo->saldo;
            $oldRetencion     = $fondo->retencion;
            $fondo->saldo     -= $detalle->monto_aprobado * $tasaCompra;
            $fondo->retencion -= $detalle->monto_aprobado * $tasaCompraAprobacion;

            if ( $fondo->saldo < 0 )
                return $this->warningException( 'El Fondo ' . $fondo->full_name . ' solo cuenta con S/.' . ( $fondo->saldo + $fondoMonto ) .
                                                $msg . $fondoMonto . ' en total' , __FUNCTION__ , __LINE__ , __FILE__ );
            $data = array(
                'idFondo'      => $fondo->id ,
                'idFondoTipo'  => INVERSION_INSTITUCIONAL ,
                'oldSaldo'     => $oldSaldo ,
                'oldRetencion' => $oldRetencion ,
                'newSaldo'     => $fondo->saldo ,
                'newRetencion' => $fondo->retencion ,
                'reason'       => FONDO_DEPOSITO );
            $fondoDataHistories[] = $data;
            $fondoMktController->setPeriodHistoryData( $fondo->subcategoria_id , $data );
            $fondo->save();
        }
        $fondoMktController->setFondoMktHistories( $fondoDataHistories , $solicitud->id );
        return $this->setRpta();
    }

    public function modalExtorno()
    {
        $inputs = Input::all();
        $solicitud = Solicitud::where( 'token' , $inputs[ 'token' ] )->first();
        if ( is_null ( $solicitud ) )
            return $this->warningException( 'No se encontro la informacion de la solicitud' , __FUNCTION__ , __LINE__ , __FILE__ );
        else
            return $this->setRpta( array( 'View' => View::make( 'template.Modals.extorno' , array( 'solicitud' => $solicitud ) )->render() ) );
    }

    public function confirmExtorno()
    {
        $inputs    = Input::all();
        $rules     = array( 'numero_operacion' => 'required|min:1' );
        $validator = Validator::make( $inputs , $rules );

        if ( $validator->fails() )
            return $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );

        $solicitud = Solicitud::where( 'token' , $inputs[ 'token' ] )->first();

        if ( is_null( $solicitud ) )
            return $this->warningException( 'No se encontro la informacion de la solicitud' , __FUNCTION__ ,  __LINE__ , __FILE__ );
        elseif( $solicitud->id_estado != DEPOSITADO )
            return $this->warningException( 'La solicitud ya ha sido validada por contabilidad' , __FUNCTION__ , __LINE__ , __FILE__ );
        else
        {
            $deposito = $solicitud->detalle->deposit;
            $deposito->num_transferencia = $inputs[ 'numero_operacion' ];
            $deposito->save();
            return $this->setRpta();
        }
    }

    public function modalLiquidation()
    {
        $inputs = Input::all();
        $solicitud = Solicitud::where( 'token' , $inputs[ 'token' ] )->first();
        if ( is_null ( $solicitud ) )
            return $this->warningException( 'No se encontro la informacion de la solicitud' , __FUNCTION__ , __LINE__ , __FILE__ );
        else
            return $this->setRpta( array( 'View' => View::make( 'template.Modals.liquidation' , array( 'solicitud' => $solicitud ) )->render() ) );
    }

    public function confirmLiquidation()
    {
        try
        {
            $inputs    = Input::all();
            $solicitud = Solicitud::where( 'token' , $inputs[ 'token' ] )->first();
            $oldIdestado = $solicitud->id_estado;

            if ( is_null( $solicitud ) )
            {
                return $this->warningException( 'No se encontro la informacion de la solicitud' , __FUNCTION__ , __LINE__ , __FILE__ );
            }
            elseif( ! in_array( $solicitud->id_estado, [DEPOSITADO, GASTO_HABILITADO] ) )
            {
                return $this->warningException( 'No se puede Cancelar por Liquidacion' , __FUNCTION__ , __LINE__ , __FILE__ );
            }
            else
            {
                DB::beginTransaction();

                //REGISTRO DE LA DEVOLUCION
                $devolucion = new DevolutionController;
                $periodo = Carbon::createFromFormat( 'm-Y' , $inputs[ 'periodo' ] )->format( 'Ym' );
                $devolucion->setDevolucion( $solicitud->id , $periodo , $solicitud->detalle->monto_aprobado , DEVOLUCION_CONFIRMADA , DEVOLUCION_LIQUIDACION );

                //RETORNO DE SALDO AL FONDO
                $fondo = new FondoMkt;
                $fondo->refund( $solicitud , $solicitud->detalle->monto_aprobado , 8 );

                //ACTUALIZACION DEL ESTADO DE LA SOLICITUD
                $solicitud->id_estado = 30;
                $solicitud->save();

                if ( $solicitud->idtiposolicitud != SOL_INST )
                {
                    $middleRpta = $this->setStatus( $oldIdestado , 30 , Auth::user()->id, $solicitud->approvedHistory->created_by , $solicitud->id );
                }
                else
                {
                    $middleRpta = $this->setStatus( $oldIdestado , 30 , Auth::user()->id, $solicitud->created_by , $solicitud->id );
                }

                if ( $middleRpta[ status ] == ok )
                {
                    Session::put( 'state' , R_FINALIZADO );
                    DB::commit();
                    return $this->setRpta();
                }
                else
                {
                    DB::rollback();
                    return $middleRpta;
                }
            }

        }
        catch( Exception $e )
        {
            DB::rollback();
            return $this->internalException($e,__FUNCTION__);
        }
    }

    public function depositExport()
    {
        try
        {
            $now  = Carbon::now();
            $date = $now->toDateString();
            $title = 'Detalle del Deposito-';
            $directoryPath  = 'files/depositos';
            $filePath = $directoryPath . '/' . $title . $date . '.xls';

            $data = [];
            if( File::exists( public_path( $filePath ) ) )
            {
                $oldResponses = Excel::load( public_path( $filePath ) )->get();
                $data[ 'oldResponses' ] = $oldResponses;
            }

            if( Session::has( 'depositos' ) )
            {
                $responses = Session::pull( 'depositos' );
                $data[ 'responses' ] = $responses;
            }

            if( ! isset( $oldResponses ) && ! isset( $responses ) )
            {
                return $this->warningException( 'No se pudo exportar el excel con las observaciones del deposito' , __FUNCTION__ , __LINE__ , __FILE__ );
            }

            Excel::create( $title . $date , function( $excel ) use( $data )
            {
                $excel->sheet( 'solicitudes' , function( $sheet ) use( $data )
                {
                    $sheet->freezeFirstRow();
                    $sheet->setStyle(
                        array(
                            'font' =>
                                array(
                                    'bold' => true
                                )
                            )
                        );
                    $sheet->loadView( 'Dmkt.Treasury.excelDepositDetail' , $data );
                });
            })->store( 'xls' , public_path( $directoryPath ) );
            return Response::download( $filePath );
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    public function adminSolicitudDeposit()
    {
      try
      {
        $user = User::find(2);
        Auth::login($user);
        $inputs      = Input::all();
        $middleRpta  = $this->validateInputsDeposit( $inputs );
        if( $middleRpta[ status ] === ok )
        {
            return $this->depositOperation( $inputs[ 'token' ] , $inputs[ 'operacion' ] , $inputs[ 'cuenta' ] );
        }
        Auth::logout();
        return $middleRpta;
      }
      catch( Exception $e )
      {
        return $this->internalException( $e , __FUNCTION__ , __LINE__ , __FILE__ );
      }
    }
}