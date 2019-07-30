<?php

namespace Devolution;

use \BaseController;
use \Input;
use \Exception;
use \Parameter\Parameter;
use \Dmkt\Solicitud;
use \View;
use \DB;
use \Validator;
use \Carbon\Carbon;
use \Fondo\FondoMkt;
use \Session;
use \User;
use \Auth;

class DevolutionController extends BaseController
{  

    public function setDevolucion( $solicitud_id , $numero_operacion_devolucion , $monto_devolucion , $id_estado_devolucion , $id_tipo_devolucion )
	{
		$devolution = new Devolution;
		$devolution->id = $devolution->nextId();
		$devolution->id_solicitud = $solicitud_id;
		$devolution->monto = $monto_devolucion;
		$devolution->id_estado_devolucion = $id_estado_devolucion;
		$devolution->id_tipo_devolucion = $id_tipo_devolucion;
		if ( $id_tipo_devolucion == DEVOLUCION_PLANILLA )
			$devolution->periodo = $numero_operacion_devolucion;
		elseif( $id_estado_devolucion != DEVOLUCION_POR_REALIZAR )
			$devolution->numero_operacion = $numero_operacion_devolucion;
		$devolution->save();

        if ( $id_tipo_devolucion == DEVOLUCION_INMEDIATA )
        {
            $devolutionUserTo = $this->getDevolutionUserTo( $devolution->id_estado_devolucion , $solicitud_id );
            $this->setDevolutionHistory( $devolution , 0 , $devolutionUserTo );
	    }
    }

    private function getDevolutionUserTo( $id_estado_devolucion , $solicitud_id )
    {
        $solicitud = Solicitud::find( $solicitud_id );
        if( $id_estado_devolucion == 1 )
        {
            return $solicitud->assignedTo->type;
        }
        elseif( $id_estado_devolucion == 2 )
        {
            return TESORERIA;
        }
        elseif( $id_estado_devolucion == 3 )
        {
            return CONT;
        }
    }

    private function setDevolutionHistory( $devolution , $oldState , $userTo )
    {
        $devolutionHistory                = new DevolutionHistory;
        $devolutionHistory->id            = $devolutionHistory->nextId();
        $devolutionHistory->id_devolucion = $devolution->id;
        $devolutionHistory->status_from   = $oldState;
        $devolutionHistory->status_to     = $devolution->id_estado_devolucion;
        $devolutionHistory->user_from     = Auth::user()->type;
        $devolutionHistory->user_to       = $userTo;
        $devolutionHistory->save();
    }

    public function getPayrollInfo()
    {
        $inputs = Input::all();
        $solicitud = Solicitud::where( 'token' , $inputs[ 'token' ] )->first();
        $data = array(
            'solicitud' => $solicitud );
        return $this->setRpta( array( 'View' => View::make( 'Dmkt.Devolution.payroll-discount' , $data )->render() ) );
    }

    public function confirmPayrollDiscount()
	{
		try
		{
			DB::beginTransaction();
			$inputs     = Input::all();
			$middleRpta = $this->validateDiscount( $inputs );
			if ( $middleRpta[ status] == ok )
			{
				$solicitud  = Solicitud::where( 'token' , $inputs[ 'token' ] )->first();
				
				if ( $solicitud->id_estado != ENTREGADO )
				{
					return $this->warningException( 'La solicitud no esta habilitado para que realize el descuento. Se requiere que se culmine con el Descargo' , __FUNCTION__ , __LINE__ , __FILE__ );
				}

				//REGISTRO DE LA DEVOLUCION
				$periodo = Carbon::createFromFormat( 'm-Y' , $inputs[ 'periodo' ] )->format( 'Ym' );
				$this->setDevolucion( $solicitud->id , $periodo , $inputs[ 'monto_descuento_planilla' ] , DEVOLUCION_CONFIRMADA , DEVOLUCION_PLANILLA );
				
				//DEVOLUCION DE SALDO A LOS FONDOS
				$fondoMktController = new FondoMkt;
				$fondoMktController->refund( $solicitud , $inputs[ 'monto_descuento_planilla' ] , FONDO_DEVOLUCION_PLANILLA );
				
				DB::commit();
				return $this->setRpta();
			}
			return $middleRpta;
		}
		catch( Exception $e )
		{
			DB::rollback();
			return $this->internalException( $e , __FUNCTION__ );
		}
	}

	private function validateDiscount( $inputs )
    {
        $rules = array( 
			'token'                    => 'required|string|size:40|exists:solicitud,token' ,
			'periodo'                  => 'required|string|size:7|date_format:"m-Y"|after:'  . date( '01-m-Y' ) ,
			'monto_descuento_planilla' => 'required|numeric|min:1' );  
        $validator = Validator::make( $inputs, $rules );
        if ( $validator->fails() ) 
            return $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );
	    return $this->setRpta(); 
    }

    private function getInmediateDevolutionRegisterInfo( $inputs )
    {
        $solicitud = Solicitud::where( 'token' , $inputs[ 'token' ] )->first();
        $data = array(
            'solicitud' => $solicitud );
        return $this->setRpta( array( 
                'View'  => View::make( 'Dmkt.Devolution.register-inmediate-devolution' , $data )->render() ,
                'Title' => 'Registro de la solicitud de devolucion' ,
                'Type'  => 'input' 
                ) , '<h4 class="text-warning">Confirme la devolucion de ' 
        );
    }

    private function getInmediateDevolutionDoInfo( $inputs )
    {
		$solicitud  = Solicitud::where( 'token' , $inputs[ 'token' ] )->first();
        $devolution = $solicitud->devolutions()->where( 'id_estado_devolucion' , DEVOLUCION_POR_REALIZAR )->first();
        $data = array(
            'solicitud'  => $solicitud ,
            'devolucion' => $devolution->monto );
        return $this->setRpta( array( 
    			'View'  => View::make( 'Dmkt.Devolution.do-inmediate-devolution' , $data )->render() , 
    			'Title' => 'Registro de la operacion de devolucion' ,
                'Type'  => 'input'
			) , '<h4 class="text-warning">Confirme el N° de operacion '
        );
    }

    public function getInmediateDevolutionConfirmationInfo( $inputs )
    {
        $solicitud = Solicitud::where( 'token' , $inputs[ 'token' ] )->first();
        $devolution = $solicitud->devolutions()->where( 'id_estado_devolucion' , DEVOLUCION_POR_VALIDAR )->first();
        $data = array(
            'solicitud' => $solicitud ,
            'numero_operacion' => $devolution->numero_operacion , 
            'devolucion'       => $devolution->monto );
        return $this->setRpta( array( 
                'Type'  => 'confirmation'
            ) , View::make( 'Dmkt.Devolution.confirmate-inmediate-devolution' , $data )->render()
        );
    }


    public function registerDevolutionData()
    {
        try
        {
            $inputs = Input::all();
            switch( $inputs[ 'tipo' ] )
            {
                case 'register-inmediate-devolution':
                    return $this->registerInmediateDevolution( $inputs );
                case 'do-inmediate-devolution':
                    return $this->doInmediateDevolution( $inputs );
                case 'confirm-inmediate-devolution':
                    return $this->confirmInmediateDevolution( $inputs );
            }         
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    public function registerInmediateDevolution( $inputs )
    {
        $solicitud = Solicitud::where( 'token' , $inputs[ 'token' ] )->first();
        
        $middleRpta = $this->validateDevolution( $solicitud );
        if ( $middleRpta[ status ] != ok )
        {
            return $middleRpta;
        }

        DB::beginTransaction();
        
		//REGISTRO DE LA DEVOLUCION
		$this->setDevolucion( $solicitud->id , null , $inputs[ 'monto_devolucion' ] , DEVOLUCION_POR_REALIZAR , DEVOLUCION_INMEDIATA );

        $middleRpta = $this->postman( $solicitud->id , $solicitud->id_estado , $solicitud->id_estado , array( $solicitud->assignedTo ) );
        if ( $middleRpta[ status ] == ok )
        {
            Session::put( 'state' , R_GASTO );
            DB::commit();
            return $this->setRpta( '' , 'Se registro la solicitud de devolucion correctamente' );
        }
        else
        {
            DB::rollback();
            return $middleRpta;
        }
    }

    public function getDevolutionInfo()
    {
    	try
    	{
    		DB::beginTransaction();
    		$inputs = Input::all();
            switch( $inputs[ 'tipo' ] )
    		{
                case 'register-inmediate-devolution':
                    return $this->getInmediateDevolutionRegisterInfo( $inputs );
    			case 'do-inmediate-devolution':
    				return $this->getInmediateDevolutionDoInfo( $inputs );
                case 'confirm-inmediate-devolution':
                    return $this->getInmediateDevolutionConfirmationInfo( $inputs );
    		}
    	} 
    	catch( Exception $e )
    	{
    		DB::rollback();
    		return $this->internalException( $e , __FUNCTION__ );
    	}
    }

    public function doInmediateDevolution()
    {
        DB::beginTransaction();
        $inputs = Input::all();
        $solicitud = Solicitud::where( 'token' , $inputs[ 'token' ] )->first();
        
        if ( is_null( $solicitud ) )
            return $this->warningException( 'Cancelado - No se encontro la solicitud' , __FUNCTION__ , __LINE__ , __FILE__ );

        if( $solicitud->idtiposolicitud == REEMBOLSO )
            return $this->warningException( 'Cancelado - Los Reembolos no estan habilitados para la confirmacion de la devolucion' , __FUNCTION__ , __LINE__ , __FILE__ );
        
        $devolucion = $solicitud->devolutions()->where( 'id_estado_devolucion' , DEVOLUCION_POR_REALIZAR )->get();
        if( $solicitud->id_estado != ENTREGADO && $devolucion->count() === 0 )
            return $this->warningException( 'Cancelado - La Devolucion no se encuentra en la etapa de registro del N° de Operacion' , __FUNCTION__ , __LINE__ , __FILE__ );
        
        $devolucion                       = $devolucion[ 0 ];
        $oldDevolutionState               = $devolucion->id_estado_devolucion;
        $devolucion->numero_operacion     = $inputs[ 'numero_operacion_devolucion' ];
        $devolucion->id_estado_devolucion = DEVOLUCION_POR_VALIDAR;
        $devolucion->save();

        //HISTORIAL DE LA DEVOLUCION
        $devolutionUserTo = $this->getDevolutionUserTo( $devolucion->id_estado_devolucion , $solicitud->id );
        $this->setDevolutionHistory( $devolucion , $oldDevolutionState , $devolutionUserTo );
        

        $toUser = User::getTesorerias();
        $middleRpta = $this->postman( $solicitud->id , $solicitud->id_estado , $solicitud->id_estado , $toUser );
        if ( $middleRpta[ status ] == ok )
        {
            Session::put( 'state' , R_GASTO );
            DB::commit();
            return $this->setRpta( '' , 'Se registro el numero de operacion de la devolucion correctamente' );
        }
        else
        {
            DB::rollback();
            return $middleRpta;
        }
    }

    private function confirmInmediateDevolution()
    {
        DB::beginTransaction();
        $inputs = Input::all();
        $solicitud = Solicitud::where( 'token' , $inputs[ 'token' ] )->first();
        
        $middleRpta = $this->validateDevolution( $solicitud );

        if ( $middleRpta[ status] !== ok )
        {
            DB::rollback();
            return $middleRpta;
        }

        $devolucion = $solicitud->devolutions()->where( 'id_estado_devolucion' , DEVOLUCION_POR_VALIDAR )->get();

        if( $solicitud->id_estado != ENTREGADO && $solicitud->devolutions()->where( 'id_estado_devolucion' , DEVOLUCION_POR_VALIDAR )->get()->count() === 0 )
            return $this->warningException( 'Cancelado - La solicitud no se encuentra en la etapa de validación de la devolucion' , __FUNCTION__ , __LINE__ , __FILE__ );
            
        $devolucion = $devolucion[ 0 ];

        $oldDevolutionState = $devolucion->id_estado_devolucion;
        $devolucion->id_estado_devolucion = DEVOLUCION_CONFIRMADA;
        $devolucion->save();

        //HISTORIAL DE LA DEVOLUCION
        $devolutionUserTo = $this->getDevolutionUserTo( $devolucion->id_estado_devolucion , $solicitud->id );
        $this->setDevolutionHistory( $devolucion , $oldDevolutionState , $devolutionUserTo );
        
        $fondoMktController = new FondoMkt;
        $fondoMktController->refund( $solicitud , $devolucion->monto , FONDO_DEVOLUCION_TESORERIA );

        $toUser = User::getCont();
        $middleRpta = $this->postman( $solicitud->id , $solicitud->id_estado , $solicitud->id_estado , $toUser );
        if ( $middleRpta[ status ] == ok )
        {
            Session::put( 'state' , R_GASTO );
            DB::commit();
            return $this->setRpta( '' , 'Se registro la confirmacion de la operacion de devolucion' );
        }
        else
        {
            DB::rollback();
            return $middleRpta;
        }
    }

    private function validateDevolution( $solicitud )
    {
        if ( is_null( $solicitud ) )
        {
            return $this->warningException( 'Cancelado - No se encontro la solicitud' , __FUNCTION__ , __LINE__ , __FILE__ );
        }

        if( $solicitud->idtiposolicitud == REEMBOLSO )
        {
            return $this->warningException( 'Cancelado - Los Reembolos no estan habilitados para la confirmacion de la devolucion' , __FUNCTION__ , __LINE__ , __FILE__ );
        }

        return $this->setRpta();
    }
    
}