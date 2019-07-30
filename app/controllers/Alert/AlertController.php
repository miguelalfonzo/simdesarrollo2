<?php

namespace Alert;

use \BaseController;
use \Exception;
use \Carbon\Carbon;
use \Dmkt\Solicitud;
use \Dmkt\SolicitudClient;
use \Auth;
use \Illuminate\Database\Eloquent\Collection;
use \Parameter\Parameter;
use \View;
use \DB;
use \System\SolicitudHistory;

class AlertController extends BaseController
{
	public function show()
	{
		$data = array('alerts' => $this->alertConsole2());
		return View::make('template.User.alerts', $data);
	}

	public function showAlerts()
	{
		return array( status => ok, 'alerts' => $this->alertConsole2());
	}

    public function alertConsole2()
    {
    	$result = array();
    	$result[ 'alert' ] = array();
    	
    	if( in_array( Auth::user()->type , [ REP_MED , SUP , GER_PROD , GER_PROM , GER_COM ] , 1 ) )
    	{
	    	$clientAlert = $this->newClientAlert();
	    	if( ! empty( $clientAlert[ 'data' ] ) )
	    	{
	    		$result[ 'alert' ][] = $clientAlert;
    		}
    	}
    	$expenseAlert = $this->expenseAlert2();
    	if( ! empty( $expenseAlert[ 'data' ] ) )
    	{
    		$result['alert'][] = $expenseAlert;
    	}
    	$timeAlert = $this->compareTime2();
    	if( ! empty( $timeAlert[ 'data' ] ) )
    	{
    		$result['alert'][] = $timeAlert;
    	}

    	return $result['alert'];
    }

    public function expenseAlert2()
	{
		$result = array();
		$solicituds = Solicitud::where( 'id_user_assign' , Auth::user()->id )->where( 'id_estado' , GASTO_HABILITADO )->get();
		$tiempo = Parameter::find( ALERTA_TIEMPO_ESPERA_POR_DOCUMENTO );
		foreach ( $solicituds as $solicitud )
		{
			$expenseHistory = $solicitud->expenseHistory;
			$lastExpense = $solicitud->lastExpense;
			if ( is_null( $lastExpense ) && ! is_null( $expenseHistory ) && $this->timeAlert( $expenseHistory , 'diffInDays' , 'updated_at' ) >= $tiempo->valor ){
				$result[] = array(
					"solicitude" => $solicitud->id,
					"msg" => $tiempo->mensaje,
					);
			}
			else if ( ( ! is_null( $lastExpense ) ) && $this->timeAlert( $lastExpense , 'diffInDays' , 'updated_at' ) >= $tiempo->valor ){
				$result[] = array(
					"solicitude" => $solicitud->id,
					"msg" => $tiempo->mensaje,
					);
			}
		}
		return array( 'type' => 'warning' , 'typeData' => 'expenseAlert', 'data' => $result );
	}

	public function compareTime2()
	{
		$solicituds =   Solicitud::where( 'id_user_assign' , Auth::user()->id )
						->where( 'idtiposolicitud' , '<>' , SOL_INST )
						->where( 'id_user_assign' , Auth::user()->id )->whereNotIn( 'id' , function( $query )
						{
							$query->select( 'id_solicitud' )
							->from( with( new SolicitudHistory )->getTable() )
							->whereIn( 'status_to' , [ CANCELADO , RECHAZADO , ENTREGADO , 29 , 30 ] );
						})->get();
		$result = array();
		$tiempo = Parameter::find( ALERTA_TIEMPO_REGISTRO_GASTO );
		foreach ( $solicituds as $solicitud )
		{
			if ( $this->timeAlert( $solicitud , 'diffInDays' , 'created_at' ) >= $tiempo->valor )
			{
				$result[] = array(
					"solicitude" => $solicitud->id,
					"msg" => $tiempo->mensaje,
					);
			}
		}
		return array( 'type' => 'warning' , 'typeData' => 'expenseAlert' , 'data' => $result );
	}

	private function timeAlert( $record , $method , $date )
	{
		$now = Carbon::now();
		$updated = new Carbon( $record->$date );
		return $updated->$method( $now );
	}


	private function newClientAlert()
	{
		$user = Auth::user();
		$tiempo = Parameter::find( ALERTA_INSTITUCION_CLIENTE );
	    	
		$add         = false;
		$result      = [];
		$clientArray = [];
		$data        = \DB::table( 'VALIDACION_CLIENTE' )->get();
		foreach( $data as $key1 => $register1 )
		{
			$ids_solicitud1 = explode( ',' , $register1->ids_solicitud );
			foreach( $data as $key2 => $register2 )
			{
				if( $key1 != $key2 && $register1->id_tipo_cliente != $register2->id_tipo_cliente )
				{
					$ids_solicitud2 = explode( ',' , $register2->ids_solicitud );
					$intersect = array_intersect( $ids_solicitud1 , $ids_solicitud2 );
					if( count( $intersect ) >= 3 )
					{
						foreach( $intersect as $id_solicitud )
						{
							$sol = Solicitud::find( $id_solicitud );
							if( $user->type === REP_MED && $sol->id_user_assign == $user->id )
							{

								$add = true;
								break;
							}
							else
							{
								$ids = $sol->gerente->lists( 'id_gerprod' );
								if( in_array( $user->id , $ids ) )
								{
									$add = true;
									break;
								}	
							}
						}
						if( $add )
						{
							$index = $register1->id_cliente . ',' . $register1->id_tipo_cliente . '|' . $register2->id_cliente . ',' . $register2->id_tipo_cliente;
							$sol_cli1 = SolicitudClient::where( 'id_cliente' , $register1->id_cliente )->where( 'id_tipo_cliente' , $register1->id_tipo_cliente )->first();
							$sol_cli_fn_1 = $sol_cli1->{$sol_cli1->clientType->relacion}->full_name;

							$sol_cli2 = SolicitudClient::where( 'id_cliente' , $register2->id_cliente )->where( 'id_tipo_cliente' , $register2->id_tipo_cliente )->first();
							$sol_cli_fn_2 = $sol_cli2->{$sol_cli2->clientType->relacion}->full_name;

							$clientArray = [ $sol_cli_fn_1 , $sol_cli_fn_2 ];
							$result[] = 
			    			[
								'cliente'    => $clientArray,
								'solicitude' => $intersect,
								'msg'        => $tiempo->mensaje
							];
							$add = false;
						}
					}
				}
			}
			unset( $data[ $key1 ] );
		}
		return array( 'type' => 'warning' , 'data' => $result, 'typeData' => 'clientAlert');
	}


	/* public function clientalert2()
    {
    	$user = Auth::user();
    	if( in_array( $user->type , [ REP_MED , SUP , GER_PROD , GER_PROM , GER_COM , CONT ] ) )
    	{
	    	$add;
	    	$result = [];				
	    	$clienteArray;
	    	$solicitudArray;
	    	$data = DB::table( 'VALIDACION_CLIENTE' )->get();
	    	$tiempo = Parameter::find( ALERTA_INSTITUCION_CLIENTE );
	    	foreach( $data as $register )
	    	{
	    		$add = false;
	    		$ids_solicitud = explode( ',' , $register->ids_solicitud );
	    		foreach( $ids_solicitud as $id_solicitud )
	    		{
	    			$solicitud = Solicitud::find( $id_solicitud );
	    			if( $user->type === REP_MED )
	    			{
	    				if( $solicitud->id_user_assign == $user->id )
	    				{
	    					$add = true;
	    				}
	    			}
	    			elseif( in_array( $user->type , [ SUP , GER_PROD , GER_PROM ] , 1 ) )
	    			{
	    				$ids = $solicitud->gerente->lists( 'id_gerprod' );
	    				if( in_array( $user->id , $ids ) )
	    				{
	    					$add = true;
	    				}
	    			}
	    			elseif( in_array( $user->type , [ GER_COM , CONT ] , 1 ) )
	    			{
	    				$add = true;
	    			}
	    		}
	    		if( $add )
	    		{
	    			$clients = explode( ',' , $register->cliente );
	    			foreach( $clients as $client )
	    			{
	    				$client_data      = explode( '|' , $client );
	    				$solicitudCliente = SolicitudClient::where( 'id_cliente' , $client_data[ 0 ] )->where( 'id_tipo_cliente' , $client_data[ 1 ] )->first();
	    				$clientName     = $solicitudCliente->{$solicitudCliente->clientType->relacion}->full_name;
	    				$clienteArray[] = $clientName;
	    				$cliente = '( ' . $clientName . ' , ';
					} 
					$cliente = rtrim( $cliente , ', ' );
					$cliente .= ' ).';
					$solicitudArray = $ids_solicitud;
	    			$result[]= 
	    			[
						'cliente'    => $clienteArray,
						'solicitude' => $ids_solicitud,
						'msg'        => $tiempo->mensaje
					];
					$clienteArray = [];
				}
	    	}
	    }
	    return array( 'type' => 'warning' , 'data' => $result, 'typeData' => 'clientAlert');
	}*/
}