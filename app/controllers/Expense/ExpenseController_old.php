<?php

namespace Expense;

use \Auth;
use \BaseController;
use \View;
use \Dmkt\Activity;
use \Dmkt\Solicitud;
use \User;
use \Common\State;
use \Input;
use \DB;
use \Redirect;
use \PDF;
use \Client;
use \Log;
use \Common\Deposit;
use \BagoUser;
use \Exception;
use \Session;
use \Dmkt\Account;
use \Validator;
use \yajra\Pdo\Oci8\Exceptions\Oci8Exception;
use \PDF2;
use \Dmkt\InvestmentType;
use \Users\Supervisor;
use \Users\Visitador;
use \Fondo\FondoMkt;
use \Devolution\DevolutionController;
use \Parameter\Parameter;

class ExpenseController extends BaseController
{

	private function objectToArray($object)
    {
        $array = array();
        foreach ($object as $member => $data) {
            $array[$member] = $data;
        }
        return $array;
    }

    private function validateInputExpenseDetail( $inputs )
    {
    	$rules = array( 'tipo_gasto'	   => 'required|numeric|min:1|exists:'.TB_TIPO_GASTO.',id',
                        'quantity'		   => 'required|numeric',
                        'description'	   => 'required|string|min:1|max:100',
                        'total_item'	   => 'required|numeric' );
    	$messages = array( 'description.max'  => 'La descripcion del detalle de comprobante soporta un maximo de 100 caracteres.' );
    	$validator = Validator::make( $inputs , $rules , $messages );
        if ( $validator->fails() )
       	{ 
            return $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );
    	}
    	return $this->setRpta();
    }

    private function validateInputExpense( $inputs )
    {
        $rules = array( 'token'    		   => 'required|string|size:40|exists:'.TB_SOLICITUD.',token' ,
                        'proof_type'       => 'required|integer|min:1|exists:'.TB_TIPO_COMPROBANTE.',id' ,
                        'desc_expense'     => 'required|string|min:1|max:100',
                        //'tipo_gasto'	   => 'required|array|min:1|each:integer|each:min,1|each:exists,'.TB_TIPO_GASTO.',id',
                        //'quantity'		   => 'required|array|min:1|each:integer|each:min,1',
                        //'description'	   => 'required|array|min:1|each:string|each:min,1|each:max,100',
                        //'total_item'	   => 'required|array|min:1|each:numeric|each:min,1',
                        'total_expense'    => 'required|numeric|min:1' ); 

        $messages = array( 'desc_expense.max'  => 'La descripcion del gasto soporta un maximo de 100 caracteres.' );

        $validator = Validator::make( $inputs , $rules , $messages );
        if ( $validator->fails() ) 
            return $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );
        else
        {
        	$rules 		= array();
        	$proofType = ProofType::find( $inputs['proof_type'] );
        	$solicitud = Solicitud::where( 'token' , $inputs[ 'token' ] )->first();
        	$date 	   = $this->getExpenseDate( $solicitud , 1 );
        	$rules[ 'fecha_movimiento' ] = 'required|string|date_format:"d/m/Y"|after:' . $date[ 'startDate' ] . '|before:' . $date[ 'endDate'] ;
        	$validator = Validator::make( $inputs , $rules );
        	$validator->sometimes( 'number_prefix' , 'required|string|min:1|max:4' , function( $input ) use( $proofType )
            {
                return $proofType->marca != 'N';
            });
            $validator->sometimes( 'number_serie' , 'required|numeric|min:1|digits_between:1,12' , function( $input ) use( $proofType )
            {
                return $proofType->marca != 'N';
            });
            $validator->sometimes( 'ruc' , 'required|numeric|digits:11' , function( $input ) use( $proofType )
            {
                return $proofType->marca != 'N';
            });
            $validator->sometimes( 'razon' , 'required|string|min:1' , function( $input ) use( $proofType )
            {
                return $proofType->marca != 'N';
            });
            $validator->sometimes( 'imp_service' , 'numeric|min:0' , function( $input ) use( $proofType )
            {
                return $proofType->igv == 1 ;
            });
            $validator->sometimes( 'igv' , 'numeric|min:0' , function( $input ) use( $proofType )
            {
                return $proofType->igv == 1 ;
            });
            if ( $validator->fails() ) 
        	    return $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );
        	else
	    	    return $this->setRpta();
        } 
    }

	public function registerExpense()
	{
		try
		{
			DB::beginTransaction();
			$inputs = Input::all();

			$solicitud = Solicitud::findByToken( $inputs[ 'token' ] );

			if( $solicitud->pendingRefund->count() != 0 )
			{
				return $this->warningException( 'No puede modificar documentos debido a que hay una devolucion pendiente' , __FUNCTION__ , __LINE__ , __FILE__ );
			}

			$middleRpta = $this->validateInputExpense( $inputs );
			if ( $middleRpta[ status ] === ok )
			{
				$aInputs = array();
				$messages = 'Se encontro las siguientes observaciones en el detalle del comprobante.<ul class="list-group">';
				$aStatus = false;
				foreach( $inputs[ 'total_item' ] as $key => $amount )
				{
					$aInputs[ 'tipo_gasto' ]  = $inputs[ 'tipo_gasto' ][ $key ];
					$aInputs[ 'quantity' ]    = $inputs[ 'quantity' ][ $key ];
					$aInputs[ 'description' ] = $inputs[ 'description' ][ $key ];
					$aInputs[ 'total_item' ]  = $amount;
					$middleRpta = $this->validateInputExpenseDetail( $aInputs );
					if( $middleRpta[ status ] !== ok )
					{
						$aStatus   = true;
						$messages .= '<li class="list-group-item list-group-item-danger">En la fila #' . ( $key + 1 ) . ': ' . $middleRpta[ description ] . '</li>';
					}
				}

				$messages .= '</ul>';

				if( ! $aStatus )
				{
					if ( isset( $inputs[ 'idgasto' ] ) )
						$expense = Expense::find( $inputs[ 'idgasto' ] );
					else
					{
						$expense 	 = new Expense;
						$expense->id = $expense->lastId() + 1;
			        }
			        
			        
					$proof = ProofType::find( $inputs[ 'proof_type' ] );
		    		
				    if( $proof->code != 'N' && ! isset( $inputs[ 'idgasto' ] ) && ! is_null( $inputs[ 'ruc' ] ) && ! is_null( $inputs[ 'number_prefix' ] ) && ! is_null( $inputs[ 'number_serie' ] ) )
			    	{
			    		$row_expense = Expense::where( 'ruc' , $inputs[ 'ruc' ] )->where( 'num_prefijo' ,$inputs[ 'number_prefix' ] )->where( 'num_serie' , $inputs[ 'number_serie' ] )->first();	
						if ( ! is_null( $row_expense ) )
						{
							if ( $solicitud->idtiposolicitud === $row_expense->solicitud->idtiposolicitud )
							{
								return $this->warningException( 'Ya existe un gasto registrado con Ruc: ' . $inputs[ 'ruc' ] . ' numero: '.$inputs[ 'number_prefix' ] . '-' . $inputs[ 'number_serie' ] , __FUNCTION__ , __LINE__ , __FILE__ );
							}
						}
					}

					if( $proof->igv == 1 )
					{
						$expense->igv      = $inputs['igv'];
						$expense->imp_serv = $inputs['imp_service'];
						$expense->sub_tot  = $inputs['sub_total_expense'];
					}	
		            else
		            {
		                $expense->igv      = null;
		                $expense->imp_serv = null;
		                $expense->sub_tot  = null;
		            }

			        $date 			  = $inputs['fecha_movimiento'];
			        list($d, $m, $y)  = explode('/', $date);
			        $d 				  = mktime(11, 14, 54, $m, $d, $y);
					$inputs[ 'date' ] = date( "Y/m/d" , $d );
					$inputs[ 'id_solicitud' ] = $solicitud->id;
					$this->setExpense( $expense , $inputs );

					//Detail Expense
					ExpenseItem::where( 'id_gasto' , $expense->id )->delete();
					if( $proof->igv == 1 && $expense->igv != 0 && array_sum( $inputs[ 'total_item' ] ) == ( $inputs[ 'total_expense' ] - $inputs[ 'imp_service' ] ) )
					{
						$pIGV = 1 + ( Table::getIgv()->numero / 100 );
					}
					else
					{
						$pIGV = 1;
					}
					for( $i = 0 ; $i < count( $inputs[ 'quantity' ] ) ; $i++ )
					{
						$expense_detail              = new ExpenseItem;
						$expense_detail->id          = $expense_detail->lastId() + 1 ;
						$expense_detail->id_gasto    = $expense->id;
						$expense_detail->cantidad    = $inputs['quantity'][$i];
						$expense_detail->descripcion = $inputs['description'][$i];
						$expense_detail->tipo_gasto  = $inputs['tipo_gasto'][$i];
						$expense_detail->importe     = $inputs['total_item'][$i] / $pIGV ;
						$expense_detail->save();				
					}
					if ( Auth::user()->type === CONT )
					{
						$this->postman( $solicitud->id , ENTREGADO , ENTREGADO , array( $solicitud->assignedTo ) );
					}
					DB::commit();
					return $this->setRpta();
				}
				else
				{
					$middleRpta = $this->warningException( $messages , __FUNCTION__ , __LINE__ , __FILE__ );
				}
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

	private function setExpense( $expense , $inputs )
	{
		$expense->idcomprobante    = $inputs[ 'proof_type' ];
		$expense->num_prefijo      = $inputs[ 'number_prefix' ];
		$expense->num_serie 	   = $inputs[ 'number_serie' ];
		$expense->ruc 			   = $inputs[ 'ruc' ];
		$expense->razon 		   = $inputs[ 'razon' ];
		$expense->monto 		   = $inputs[ 'total_expense' ];
		$expense->descripcion 	   = $inputs[ 'desc_expense' ];
        $expense->fecha_movimiento = $inputs[  'date' ];
        $expense->idcomprobante    = $inputs[ 'proof_type' ];
 		$expense->id_solicitud 	   = $inputs[ 'id_solicitud' ];

 		if ( isset( $inputs[ 'rep' ] ) )
			$expense->reparo = $inputs[ 'rep' ];

 		if ( isset( $inputs[ 'idregimen' ] ) )
            if ( $inputs[ 'idregimen' ] == 0 )
            {
            	$expense->idtipotributo = 0;
            	$expense->monto_tributo = 0;
            }
            else
            {
        		$expense->idtipotributo = $inputs['idregimen'];
            	$expense->monto_tributo = $inputs['monto_regimen'];    	
            }
		$expense->save();
	}
	

	public function deleteExpense()
	{
		try
		{
			DB::beginTransaction();
			$inputs = Input::all();
			$expense = Expense::find( $inputs['gastoId'] );
			if ( is_null( $expense ) )
				return $this->warningException( 'No se encontro el registro del gasto con Id: '.$inputs['gastoId'] , __FUNCTION__ , __LINE__ , __FILE__ );
			ExpenseItem::where( 'id_gasto' , $inputs[ 'gastoId' ] )->delete();
			$expense->delete();
			DB::commit();
			return $this->setRpta();
		}
		catch( Exception $e )
		{
			DB::rollback();
			return $this->internalException( $e , __FUNCTION__ );
		}
	}

	public function editExpense()
	{
		try
		{
			$inputs 	  = Input::all();
			$expense      = Expense::find( $inputs[ 'idgasto' ] );
			if ( is_null( $expense ) )
			{
				return $this->warningException( 'No existe registro del gasto con Id: ' . $inputs[ 'idgasto' ] , __FUNCTION__ , __LINE__ , __FILE__ );
			}

			return $this->setRpta( array( 'expenseItems' => $expense->items , 'expense' => $expense ) );
		}
		catch ( Exception $e )
		{
			return $this->internalException( $e , __FUNCTION__ );
		}
	}

	private function setDataInstitucional( $solicitud , $inputs )
	{
		$solicitud->id_actividad = $inputs[ 'actividad' ];
	}

	// IDKC: CHANGE STATUS => REGISTRADO
	public function finishExpense()
	{
		try
		{
			$inputs 	= Input::all();
			$solicitud  = Solicitud::where( 'token' , $inputs[ 'token' ] )->first();
			
			if( is_null( $solicitud ) )
			{
				return $this->warningException( 'No se encontro la solicitud con token: '.$inputs['token'] , __FUNCTION__ , __LINE__ , __FILE__ );
			}

			if( $solicitud->id_estado != GASTO_HABILITADO )
			{
				return $this->warningException( 'Ya finalizo el registro de documentos para esta solicitud' , __FUNCTION__ , __LINE__ , __FILE__ );
			}
			
			if( $solicitud->idtiposolicitud == REEMBOLSO )
			{
				$gastos = $solicitud->expenses->sum( 'monto' );
				if( $gastos <= 0 )
				{
					return $this->warningException( 'No puedo terminar un reembolso sin registrar al menos un documento' , __FUNCTION__ , __LINE__ , __FILE__ );
				}
			}

			DB::beginTransaction();
			$oldIdEstado = $solicitud->id_estado;
		
			$middleRpta = $this->validateExpense( $solicitud , $inputs );
			if ( $middleRpta[ status ] != ok )
				return $middleRpta;

			$toUser = $middleRpta[ data ];
			$solicitud->id_estado = ENTREGADO;
			$solicitud->save();

			$middleRpta = $this->setStatus( $oldIdEstado , $solicitud->id_estado , Auth::user()->id, $toUser , $solicitud->id );
			if ( $middleRpta[status] == ok )
			{
				Session::put( 'state' , R_GASTO );
				DB::commit();
			}
			else
				DB::rollback();
			return $middleRpta;
		}
		catch (Exception $e)
		{
			DB::rollback();
			return $this->internalException( $e , __FUNCTION__ );
		}
	}

	private function validateExpense( $solicitud , $inputs )
    {

		$devolutionController = new DevolutionController;
		$detalle              = $solicitud->detalle;
		$monto_aprobado       = $detalle->monto_aprobado;
		$totalGasto           = $solicitud->expenses->sum( 'monto' );
		$totalGasto 		  = $totalGasto;				
		$balance              = bcsub( $monto_aprobado , $totalGasto , 2 );
		$payrollAmount        = Parameter::find( 4 )->valor;

		$tasa = $this->getExchangeRate( $solicitud );
		$balanceSoles  = $balance * $tasa;
		$jDetalle = json_decode( $detalle->detalle );

    	if ( $solicitud->idtiposolicitud == SOL_REP )
		{
			if ( $balanceSoles > $payrollAmount )
				if ( isset( $inputs[ 'numero_operacion_devolucion' ] ) && ! empty( trim( $inputs[ 'numero_operacion_devolucion'] ) ) )
				{
					$devolutionController->setDevolucion( $solicitud->id , $inputs[ 'numero_operacion_devolucion' ] , $balance , DEVOLUCION_POR_VALIDAR , DEVOLUCION_INMEDIATA );
					$toUser = USER_TESORERIA;
				}
				else
					return array( 
						status  =>  'Info' , 
			        	'View'  =>  View::make( 'Dmkt.Register.expense-missing-data' , array( 
			        					'devolucion' => true , 
			        					'balance'    => $balanceSoles ) 
			        				)->render() ,
				        'Type'  =>  'D' ,
				        'Title' =>  'Registro de la Operacion de Devolución' );
			elseif ( $balanceSoles <= $payrollAmount && $balanceSoles >= 0  )
				$toUser = USER_CONTABILIDAD;
			else
				return $this->warningException( 'No se puede registrar los gastos si exceden al monto depositado' , __FUNCTION__ , __LINE__ , __FILE__ );
		}
		elseif( $solicitud->idtiposolicitud == SOL_INST )
		{
			if( $balanceSoles > $payrollAmount )
			{
				if ( isset( $inputs[ 'inversion'] ) && isset( $inputs[ 'actividad' ] ) && isset( $inputs[ 'numero_operacion_devolucion' ] ) )
				{
					$this->setDataInstitucional( $solicitud , $inputs );	
					$devolutionController->setDevolucion( $solicitud->id , $inputs[ 'numero_operacion_devolucion' ] , $balance , DEVOLUCION_POR_VALIDAR , DEVOLUCION_INMEDIATA );
					$toUser = USER_TESORERIA;		
				}
				else
				{
					$investments = InvestmentType::orderInst();
					return array( 
						status  =>  'Info' , 
						'View'  =>  View::make( 'Dmkt.Register.expense-missing-data' , array( 
										'investments' => $investments , 
										'activities'  => Activity::order() , 
										'devolucion'  => true ,
										'balance'     => $balanceSoles ) 
									)->render() ,
						'Type'  =>  'ID' ,
						'Title' =>  'Registro de la Operacion de Devolución , Inversion y Actividad' );
				}
			}
			elseif ( $balanceSoles <= $payrollAmount && $balanceSoles >= 0 )
			{
				if ( isset( $inputs[ 'inversion'] ) && isset( $inputs[ 'actividad' ] ) )
				{
					$this->setDataInstitucional( $solicitud , $inputs );
					$toUser = USER_CONTABILIDAD;
				}
				else
				{
					$investments = InvestmentType::orderInst();
					return array( 
						status => 'Info' , 
						'View' => View::make( 'Dmkt.Register.expense-missing-data' , array( 'investments' => $investments , 'activities' => Activity::order() ) )->render() ,
						'Type' => 'I' ,
						'Title' => 'Registro de la Inversion y Actividad');
				}
			}			
			else
			{
				return $this->warningException( 'No se puede registrar los gastos si exceden al monto depositado' , __FUNCTION__ , __LINE__ , __FILE__ );
			}
		}
		elseif( $solicitud->idtiposolicitud == REEMBOLSO )
		{
			$toUser = USER_CONTABILIDAD;
		}
		else
		{
			return $this->warningException( 'No se pudo identificar el tipo de solicitud' , __FUNCTION__ , __LINE__ , __FILE__ );
		}
		return $this->setRpta( $toUser );
    }

	public function viewExpense($token)
	{
		$solicitud = Solicitud::where('token',$token)->firstOrFail();
		if(count($solicitud)>0)
		{
			$expenses = Expense::where('idsolicitud',$solicitud->idsolicitud)->get();
		}
		$gasto_total = 0;
		foreach ($expenses as $expense)
		{
			$gasto_total += $expense->monto;
		}

		$data = [
			'solicitude' => $solicitud,
			'expenses'    => $expenses,
			'total'      => $gasto_total
		];
		return View::make('Expense.view',$data);
	}

	public function manageDocument()
	{
		try
		{
			DB::beginTransaction();
			$data = array();
			$now = getdate();
			$dateNow = $now['year'].'-'.$now['mon'].'-'.$now['mday'].' '.$now['hours'].':'.$now['minutes'].':'.$now['seconds'];
			$input = Input::all();
			
			if ($input['type'] == 'Update')
			{
				$document = ProofType::where('id',$input['pk'])->first();
				$document->descripcion = strtoupper($input['desc']);
				$document->marca = strtoupper($input['marca']);
				$document->igv = $input['igv'];
			}
			else
			{
				$document = new ProofType;
				$document->id = $document->lastId() + 1;
				$document->descripcion = strtoupper($input['desc']);
				$document->cta_sunat = $input['sunat'];
				$document->marca = strtoupper($input['marca']);
				$document->igv = $input['igv'];
			}
			if ( !$document->save() )
				return $this->warningException( __FUNCTION__ , 'No se pudo procesar el documento');
			else
			{
				DB::commit();
				return $this->setRpta();
			}
		}
		catch (Exception $e)
		{
			DB::rollback();
			return $this->internalException( $e , __FUNCTION__ );
		}
	}

	public function getSpecialty($cmp = null){
		$result = null;
		if(!is_null($cmp)){
			$query = "SELECT " .
							"P.PEFCODPERS as CMP, " .
							"P.PEFPATERNO as APELLIDO, " .
							"E1.NOMESP AS ESPECIALIDAD1, " .
							"E2.NOMESP AS ESPECIALIDAD2 " .
						"FROM " .
								TB_DOCTOR." P " .
								"LEFT JOIN " .
								  "FICPE.ESPECIAL E1 " .
								  "ON TO_NUMBER(NVL(TRIM(P.PEFESPECIAL1),'0')) = E1.CODESP " .
								"LEFT JOIN " .
								  "FICPE.ESPECIAL E2 " .
								  "ON TO_NUMBER(NVL(TRIM(P.PEFESPECIAL2),'0')) = E2.CODESP " .
						"WHERE " .
						  "P.PEFNRODOC1 = ".$cmp;
			$result = DB::select($query)[0];
		}
		return $result;
	}

	public function getZonaRep($id){
		$result = null;
		$rep = Visitador::where('VISVISITADOR', '=', $id)->first();
		if(isset($rep->visnivel4geog))
			$result = DB::select("SELECT * FROM FICPE.NIVEL4GEOG where N4GNIVEL4GEOG=".$rep->visnivel4geog)[0]->n4gdescripcion;
		return $result;
	}
	public function getZonaSup($id){
		$result = null;
		$sup = Supervisor::where('SUPSUPERVISOR', '=', $id)->first();
		if(isset($rep->supnivel4geog))
			$result = DB::select("SELECT * FROM FICPE.NIVEL4GEOG where N4GNIVEL4GEOG=".$sup->supnivel4geog)[0]->n4gdescripcion;
		return $result;
	}

	public function reportExpense($token)
	{
		$solicitud = Solicitud::where('token',$token)->firstOrFail();
		$detalle   = $solicitud->detalle;
		$jDetalle  = json_decode( $solicitud->detalle->detalle );
		$expenses  = $solicitud->expenses;
		$dni       = new BagoUser;
		$dni       = $dni->dni( $solicitud->assignedTo->username );

		if ( $dni[ status ] == ok )
		{
			$dni = $dni[data];
		}
		else
		{
			$dni = '';
		}

		$zona  = null;

		if ( $detalle->id_moneda == DOLARES )
		{
			foreach( $expenses as $expense )
			{
				$expense->monto = $expense->monto * $this->getDateExchangeRate( $expense->fecha_movimiento );
			}
		}

		$total = $expenses->sum('monto');
		$size  = $expenses->count();

		$clientes  = array();
		$cmps      = array();
		$getSpecialty = array();

		$this->setReportData( $solicitud , $clientes , $cmps , $getSpecialty );

		$getSpecialty = implode("<br><br>", $getSpecialty);
		$clientes     = implode('<br><br>',$clientes);
		$cmps         = implode('<br><br> ',$cmps);

		if ( in_array( $solicitud->idtiposolicitud , array( SOL_REP , REEMBOLSO ) ) )
		{	
			$aproved_user = User::where( 'id' , $solicitud->approvedHistory->updated_by )->firstOrFail();
			$name_aproved = $aproved_user->personal->full_name;
			$charge = $aproved_user->userType->descripcion;
			
			$data  = array( 
				'solicitud'    => $solicitud,
				'detalle'      => $jDetalle,
				'clientes'     => $clientes,
				'cmps'         => $cmps,
				'getSpecialty' => $getSpecialty,
				'date'         => array( 'toDay' => $solicitud->created_at_parse , 'lastDay' => $jDetalle->fecha_entrega ),
				'name'         => $name_aproved,
				'dni'          => $dni,
				'charge'       => $charge,
				'expenses'     => $expenses,
				'zona'		   => $zona,
				'total'        => $total, 
				'size'		   => $size );
			$data['balance'] = $this->reportBalance( $solicitud , $detalle , $jDetalle , $total );
			$html = View::make( 'Expense.report' , $data )->render();
		}
		elseif( $solicitud->idtiposolicitud == SOL_INST )
		{
			$data = array(  
				'fondo'        => $solicitud,
				'detalle'      => $jDetalle,
				'clientes'     => $clientes,
				'cmps'         => $cmps,
				'getSpecialty' => $getSpecialty,
				'dni'          => $dni,
				'date'         => $this->getDay(),
				'expenses'     => $expenses ,
				'zona'         => $zona,
				'total'        => $total, 
				'size'         => $size );
        	$data['balance'] = $this->reportBalance( $solicitud , $detalle , $jDetalle , $total );
			$html = View::make('Expense.report-fondo',$data)->render();
		}
		return PDF2::loadHTML( $html )->setPaper( 'a4' , 'landscape' )->stream();
	}

	private function setReportData( $solicitud , &$clientes , &$cmps , &$getSpecialty )
	{
		foreach( $solicitud->clients as $client )
        {
			$clientes[]     = $client->clientType->descripcion . ': ' . $client->{$client->clientType->relacion}->full_name;
			if(!is_null($client->{$client->clientType->relacion}->pefnrodoc1))
				$cmps[]         = $client->{$client->clientType->relacion}->pefnrodoc1;
			$getSpecialtyResult = $this->getSpecialty($client->{$client->clientType->relacion}->pefnrodoc1);
			if(!is_null($getSpecialtyResult)){
				$getSpecialty[] = $getSpecialtyResult->especialidad1;
			}
        }
	}

    private function reportBalance( $solicitud , $detalle , $jDetalle , $mGasto )
	{
		if ( is_null( $detalle->deposit ) )
		{
			return array( 'bussiness' => '-' , 'employed' => $mGasto );
		}
		else
		{
			$mDeposit = $detalle->deposit->total;
		    if ( $detalle->deposit->account->idtipomoneda == DOLARES )
		    {
	    		$mDeposit = $mDeposit * $jDetalle->tcv;
		    }

		    $mBalance = $mDeposit - $mGasto;
			if ( $mBalance > 0)
		    	return array( 'bussiness' => round( $mBalance , 2 , PHP_ROUND_HALF_DOWN ) , 'employed' => 0 );
		    elseif ( $mBalance < 0 )
		    	return array( 'bussiness' => 0 , 'employed' => round( $mBalance*-1 , 2 , PHP_ROUND_HALF_DOWN ) );
		    elseif ( $mBalance == 0 )
				return array( 'bussiness' => 0 , 'employed' => 0 );
			else
				return $this->warningException( __FUNCTION__ , 'No se pudo procesar el Balance( '.$mBalance.' )' );
		}
	}

	public function getExpenses()
	{
		try
		{
			$inputs = Input::all();
			$solicitud = Solicitud::find( $inputs['idsolicitud']);
			if ( is_null( $solicitud ) )
				return $this->warningException( 'No se encontro la solicitud con id: '.$inputs['idsolicitud'] , __FUNCTION__ , __LINE__ , __FILE__ ); 
			$data = array( 'solicitud' => $solicitud , 'expenses'   => $solicitud->expenses );
			return $this->setRpta( View::make('Dmkt.Solicitud.Section.gasto-table')->with( $data )->render() );
		}
		catch ( Exception $e )
		{
			return $this->internalException( $e , __FUNCTION__ );
		}
	}

	public function getDocument()
	{
		try
		{
			$inputs = Input::all();
			$document = Expense::find( $inputs['id'] );
			if ( is_null( $document ) )
				return $this->warningException( __FUNCTION__ , 'No se encontro el documento con Id: '.$inputs['id'] );
			else
			{
				$document->moneda = $document->solicitud->detalle->typeMoney->simbolo ;
				return $this->setRpta( $document );
			}
		}
		catch ( Exception $e )
		{
			return $this->internalException( $e , __FUNCTION__ );
		}
	}

	public function updateDocument()
	{
		try 
		{
			$inputs = Input::all();
			$document = Expense::find( $inputs['id'] );
			if ( is_null( $document ) )
				return $this->warningException( __FUNCTION__ , 'No se encontro el documento con Id: '.$inputs['id'] );
			else
			{
				$regimenes = Regimen::lists( 'id' );
				if ( in_array( $inputs['idregimen'] , $regimenes ) )
				{
					$document->idtipotributo = $inputs['idregimen'];
					$document->monto_tributo = $inputs['monto'];
				}
				elseif ( $inputs['idregimen'] == 0 )
				{
					$document->idtipotributo = null;
					$document->monto_tributo = null;		
				}
				else
					return $this->warningException( __FUNCTION__ , 'No esta registrado la retencion o detracción con Id: '.$inputs['idregimen'] );
				if ( !$document->save() )
					return $this->warningException( __FUNCTION__ , 'No se pudo actualizar el documento' );
				else
					return $this->setRpta();
			}	
		} 
		catch ( Exception $e ) 
		{
			return $this->internalException( $e , __FUNCTION__ );	
		}
	}

    public function endExpenseRecord()
    {
    	try
    	{
			$inputs               = Input::all();
			DB::beginTransaction();
			$solicitud            = Solicitud::where( 'token' , $inputs[ 'token' ] )->first();
			$oldIdEstado          = $solicitud->id_estado;
			$solicitud->id_estado = REGISTRADO;
			$solicitud->save();

			$middleRpta = $this->setStatus( $oldIdEstado , $solicitud->id_estado , Auth::user()->id , USER_CONTABILIDAD , $solicitud->id );
			if ( $middleRpta[status] == ok )
			{
				Session::put( 'state' , R_GASTO );
				DB::commit();
			}
			else
				DB::rollback();
			return $middleRpta;
		}
		catch ( Exception $e )
		{
			DB::rollback();
			return $this->internalException( $e , __FUNCTION__ );
		}
    }
}