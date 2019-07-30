<?php

namespace Seat;

use \Dmkt\Solicitud;
use \Expense\ProofType;
use \BaseController;
use \Expense\Entry;
use \Dmkt\Account;
use \Expense\MarkProofAccounts;
use \Carbon\Carbon;
use \Session;
use \View;
use \Input;
use \Validator;
use \DB;
use \Auth;
use \stdClass;
use \Expense\ChangeRate;
use \Expense\PlanCta;
use \Dmkt\SpecialAccount;

class Generate extends BaseController
{
    public function viewGenerateEntryExpense( $token )
    {
        try
        {
            $solicitud = Solicitud::findByToken( $token );
            
            $typeProof = ProofType::all();
            
            $resultSeats = $this->generateRegularizationSeatData( $solicitud ); 
            
            $data = array(
                'solicitud'   => $solicitud,
                'expenseItem' => $solicitud->expenses,
                'typeProof'   => $typeProof,
                'seats'       => $resultSeats
            );

            if( isset( $resultSeats[ 'error' ] ) )
            {
                $tempArray = array( 'error' => $resultSeats[ 'error' ] );
                $data = array_merge( $data , $tempArray );
            }
            Session::put( 'state' , R_GASTO );
            return View::make( 'Dmkt.Cont.expense_seat' , $data );
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    private function generateRegularizationSeatData( $solicitud )
    {
        $expenses = $solicitud->expenses;
        foreach( $expenses as $expense ) 
        {
            $expense->itemList = $expense->items;
            $expense->count    = count($expense->itemList);
        }
        $solicitud->documentList = $expenses;
        return $this->generateSeatExpenseData( $solicitud );
    }

    private function generateSeatExpenseData( $solicitud )
    {
        $now = Carbon::now();
        $result = array();
        $seatList = array();
        $detalle = $solicitud->detalle;
        $middleRpta = $this->searchFundAccount($solicitud);
        if( $middleRpta[ status ] == ok ) 
        {
            $fondo = $middleRpta[data];
            $cuentaExpense = '';
            $marcaNumber = '';
            $cuentaMkt = '';
            if ( ! is_null( $fondo ) ) 
            {
                $cuentaMkt = $fondo->num_cuenta;

                $cuentaExpense = Account::getExpenseAccount( $cuentaMkt );

                if ( ! is_null( $cuentaExpense[0]->num_cuenta ) ) 
                {
                    $cuentaExpense = $cuentaExpense[0]->num_cuenta;
                    $marcaNumber = MarkProofAccounts::getMarks( $cuentaMkt , $cuentaExpense );
                    $marcaNumber = $marcaNumber[0]->marca_codigo;
                } 
                else
                {
                    $result['error'][] = $accountResult['error'];
                }
            }

            $total_percepciones = 0;

            $userElement = $solicitud->assignedTo;
            $tipo_responsable = $userElement->tipo_responsable;
            $username = $userElement->personal->seat_name;


            if( $solicitud->documentList->count() === 0 )
            {
                /*GASTOS IGUAL A 0 SI NO SE HA REGISTRADO DOCUMENTOS */
                if( $solicitud->documentList->count() == 0 ) 
                {
                    $oldExpense = $solicitud->toAdvanceSeatHistory;
                }
            } 
            else
            {
                $CUENTA_HABER_REEMBOLSO = SpecialAccount::getRefund();
                
                $firstSolicitudClient = $solicitud->client;
                $clientName           = $firstSolicitudClient->{ $firstSolicitudClient->clientType->relacion }->entry_name;                          
              
                $year = $now->year;

                $nro_origen_model_entries = Entry::select( 'substr( nro_origen , 3 , 6 ) correl' )
                                            ->whereNotNull( 'nro_origen' )
                                            ->where( 'extract( year from created_at )' , $year )
                                            ->where( 'nro_origen' , 'like' , '70%' )
                                            ->where( 'length( nro_origen )' , 8 )
                                            ->orderBy( 'nro_origen' , 'DESC' );
                if( $year == 2016 )
                {
                    $change_date = Carbon::createFromFormat( 'Y-m-d H:i' , '2016-08-04 16:30' );
                    $nro_origen_model_entries->where( 'created_at', '>', $change_date );
                }

                $nro_origen_model_entries = $nro_origen_model_entries->first();

                if( is_null( $nro_origen_model_entries ) )
                {
                    $nro_origen_pre = '70000000';
                }
                else
                {
                    $nro_origen_pre = '70' . str_pad( $nro_origen_model_entries->correl , 6 , 0 , STR_PAD_LEFT );
                }

                $i = 1;
                
                foreach( $solicitud->documentList as $expense ) 
                {
                    if( isset( $oldExpense ) )
                    {
                        if( ! $oldExpense->updated_at->gt( $expense->updated_at ) )
                        {
                            $oldExpense = $expense;
                        }
                    }
                    else
                    {
                        $oldExpense = $expense;
                    }

                    $comprobante = $expense->proof;
                    if( $comprobante->igv == 1 && $expense->igv > 0 )
                    {
                        $cc         = $comprobante->cta_sunat;
                        //$nro_origen = $nro_origen_pre . str_pad( substr( $i , -2 ) , 2 , 0 , STR_PAD_LEFT );
                        $nro_origen = $nro_origen_pre + $i;
                        ++$i;
                        $ruc        = $expense->ruc;
                        $razon      = $expense->razon;
                        $base       = ASIENTO_GASTO_IVA_BASE;
                        $prov       = ASIENTO_GASTO_COD_PROV_IGV;
                        $doc        = ASIENTO_GASTO_COD_IGV;
                        $num        = $expense->num_prefijo;
                        $serie      = $expense->num_serie;

                    }
                    else
                    {
                        $cc         = '';
                        $nro_origen = '';
                        $ruc        = '';
                        $razon      = '';
                        $base       = '';
                        $prov       = '';
                        $doc        = '';
                        $num        = '';
                        $serie      = '';
                    }

                    $desc = substr( $comprobante->descripcion , 0 , 1 ) . '/' . $expense->num_prefijo . '-' . $expense->num_serie . ' ' . $expense->razon; 
                    $tasaCompra = $this->getExpenseChangeRate( $solicitud , $expense->updated_at );
                    $import = round( $expense->monto  * $tasaCompra , 2 , PHP_ROUND_HALF_DOWN );
                    
                    $comprobante->marcaArray = explode( ',' , $comprobante->marca );
                    $marca = '';
                 
                    if( $marcaNumber == '' ) 
                    {
                        $errorTemp = array( 'error' => ERROR_NOT_FOUND_MARCA ,
                                            'msg' => MESSAGE_NOT_FOUND_MARCA );
                        if ( ! isset( $result[ 'error' ] ) || ! in_array( $errorTemp , $result[ 'error' ] ) )
                            $result[ 'error' ][] = $errorTemp;
                    } 
                    else
                    {
                        if( count( $comprobante->marcaArray ) == 2 && (boolean) $comprobante->igv == true )
                        {
                            if ( $expense->igv > 0 )
                            {
                                $marca = $marcaNumber == '' ? '' : $marcaNumber . $comprobante->marcaArray[1];
                            }
                            else
                            {
                                $marca = $marcaNumber == '' ? '' : $marcaNumber . $comprobante->marcaArray[0];
                            }
                        }
                        else
                        {
                            $marca = $marcaNumber == '' ? '' : $marcaNumber . $comprobante->marcaArray[0];
                        }
                    }

                    $fecha_origen = date( 'd/m/Y' , strtotime( $expense->fecha_movimiento ) );
                    // COMPROBANTES CON IGV
                    if( ( boolean ) $comprobante->igv === true ) 
                    {
                        //$itemLength = count( $expense->itemList ) - 1;
                        $total_neto = 0;
                        foreach ( $expense->itemList as $itemElement )
                        {
                            $sufix_description_seat_item = $username . ' ' . $itemElement->descripcion . ' '; 
                            $avalaibleLength = 50 - strlen( $sufix_description_seat_item );
                            $clientName = $this->trim_text( $clientName , $avalaibleLength );

                            $description_seat_item = strtoupper( $sufix_description_seat_item . $clientName );
                            
                            $import_item = round( $itemElement->importe * $tasaCompra , 2 , PHP_ROUND_HALF_DOWN );

                            // ASIENTO ITEM
                            $seatList[] = $this->createSeatElement( $cuentaMkt , $cuentaExpense , $cc , $fecha_origen,
                                $base , $prov , $razon , $doc , $ruc, $num , $serie, ASIENTO_GASTO_BASE, $import_item , 
                                $marca, $description_seat_item, $tipo_responsable, $nro_origen ,'' );

                            $total_neto += $itemElement->importe;
                        }

                        //ASIENTO DE IGV
                        if( $expense->igv != 0 )
                        {
                            $description_seat_igv = strtoupper($expense->razon);    
                            $seatList[] = $this->createSeatElement($cuentaMkt,  CUENTA_REPARO_GOBIERNO, $comprobante->cta_sunat, $fecha_origen, 
                                ASIENTO_GASTO_IVA_IGV, ASIENTO_GASTO_COD_PROV_IGV, $expense->razon, ASIENTO_GASTO_COD_IGV, $expense->ruc, $expense->num_prefijo, 
                                $expense->num_serie, ASIENTO_GASTO_BASE, round( $expense->igv * $tasaCompra , 2 , PHP_ROUND_HALF_DOWN ) , '' , 
                                $description_seat_igv, $tipo_responsable, $nro_origen , 'IGV' );
                        }

                        //ASIENTO IMPUESTO SERVICIO
                        if ( ! ( $expense->imp_serv == null || $expense->imp_serv == 0 || $expense->imp_serv == '') )
                        {
                            $porcentaje = $total_neto / $expense->imp_serv;


                            $description_seat_tax_service = strtoupper('SERVICIO ' . $porcentaje . '% ' . $expense->descripcion);
                            $seatList[] = $this->createSeatElement($cuentaMkt,  $cuentaExpense, '', $fecha_origen , 
                                '', '', '', '', '', '', '', ASIENTO_GASTO_BASE, round( $expense->imp_serv * $tasaCompra , 2 , PHP_ROUND_HALF_DOWN ) , 
                                $marca, $description_seat_tax_service, '', '' , 'SER' );
                        }

                        //ASIENTO REPARO
                        if ( $expense->reparo == 1 ) 
                        {
                            $description_seat_repair_base = strtoupper( $username . ' ' . $expense->descripcion . '-REP ' . $desc );
                            $seatList[] = $this->createSeatElement($cuentaMkt,  CUENTA_REPARO_COMPRAS, '', $fecha_origen , '' , '' , '' , '' , '' , '' , '' , 
                                ASIENTO_GASTO_BASE, round( $expense->igv  * $tasaCompra , 2 , PHP_ROUND_HALF_DOWN ) , $marca, $description_seat_repair_base, '', '' , 'REP');
                            
                            $description_seat_repair_deposit = strtoupper('REPARO IGV MKT ' . $desc );
                            $seatList[] = $this->createSeatElement($cuentaMkt,  CUENTA_REPARO_GOBIERNO, '', $fecha_origen, '', '', '', '', '', '', '', 
                                ASIENTO_GASTO_DEPOSITO, round( $expense->igv  * $tasaCompra , 2 , PHP_ROUND_HALF_DOWN ) , '' , $description_seat_repair_deposit, '', '' , 'REP'); 
                        }

                        //ASIENTO RETENCION
                        if ($expense->idtipotributo == REGIMEN_RETENCION )
                        {
                            $description_seat_retencion_base = strtoupper('ENTREGAS A RENDIR CTA A TERCER ' . $desc );
                            $description_seat_retencion_deposit = strtoupper('RETENCION ' . $desc);
                    
                            $seatList[] = $this->createSeatElement($cuentaMkt,  CUENTA_RETENCION_DEBE, '', $fecha_origen, '', '', '', '', '', '', '', ASIENTO_GASTO_BASE, 
                                $expense->monto_tributo  * $tasaCompra , '' , $description_seat_retencion_base, '', '', 'RET');
                            $seatList[] = $this->createSeatElement($cuentaMkt,  CUENTA_RETENCION_HABER, '', $fecha_origen, '', '', '', '', '', '', '', 
                                ASIENTO_GASTO_DEPOSITO, round( $expense->monto_tributo  * $tasaCompra , 2 , PHP_ROUND_HALF_DOWN ) , '' , 
                                $description_seat_retencion_deposit, '', '' , 'RET');
                        }

                        //ASIENTO DETRACCION
                        if ($expense->idtipotributo == REGIMEN_DETRACCION )
                        {
                            $total_percepciones += $expense->monto_tributo;
                            $description_seat_detraccion_deposit = strtoupper('DETRACCION ' . $desc);

                            $seatList[] = $this->createSeatElement( $cuentaMkt,  CUENTA_DETRACCION_HABER, '', $fecha_origen, '', '', '', '', '', '', '', 
                                ASIENTO_GASTO_DEPOSITO, round( $expense->monto_tributo * $tasaCompra , 2 , PHP_ROUND_HALF_DOWN ) , '' , 
                                $description_seat_detraccion_deposit, '', '', 'DET');
                        }
                    }
                    else //TODOS LOS OTROS DOCUMENTOS
                    {
                        if ( $expense->idcomprobante == DOC_RECIBO_HONORARIO  )
                        {
                            $rh_desc = mb_strtoupper( 'RH/'.$expense->num_prefijo . '-' . $expense->num_serie );
                            $description_seat_other_doc = strtoupper( $username .' '. $expense->razon );
                            $descripcion_rh = $description_seat_other_doc . ' ' . $rh_desc;
                            $descripcion_rh_nacional = $expense->razon . ' ' . $rh_desc;
                            if ( $solicitud->id_inversion == 17 ) //Inversion Micromarketing y tipo de documento recibo x honorario
                            {
                                $cuentaExpenseDinamic = 6329200;
                            }
                            else
                            {
                                $cuentaExpenseDinamic = $cuentaExpense;
                            }

                            $seatList[] = $this->createSeatElement( $cuentaMkt , $cuentaExpenseDinamic , '' , $fecha_origen , '' , '' , '' , '' , '' , '' , '' , 
                                ASIENTO_GASTO_BASE , $import , $marca , $descripcion_rh , $tipo_responsable , '' , '' ); 
                            $seatList[] = $this->createSeatElement( $cuentaMkt , CUENTA_RECIBO_HONORARIO , '' , $fecha_origen , '' , '' , '' , '' , '' , '' , '' , 
                                ASIENTO_GASTO_BASE , $import , '' , $descripcion_rh_nacional , $tipo_responsable, '' , '' ); 
                            $seatList[] = $this->createSeatElement( $cuentaMkt , CUENTA_RECIBO_HONORARIO , '' , $fecha_origen , '' , '' , '' , '' , '' , '' , '' , 
                                ASIENTO_GASTO_DEPOSITO , $import , '' , $descripcion_rh_nacional , $tipo_responsable, '' , '' ); 
                        }
                        else
                        {
                            $sufix_description_seat_expense = $username . ' ' . $expense->descripcion . ' '; 
                            $avalaibleLength                = 50 - strlen( $sufix_description_seat_expense );
                            $clientName                     = $this->trim_text( $clientName , $avalaibleLength );
                            $description_seat_expense       = strtoupper( $sufix_description_seat_expense . $clientName );
                            
                            $seatList[] = $this->createSeatElement( $cuentaMkt , $cuentaExpense , '' , $fecha_origen, '' , '' , '' , '' , '' , '' , '' , 
                                ASIENTO_GASTO_BASE , $import , $marca, $description_seat_expense , $tipo_responsable, '' , '' ); 
                        }

                        //ASIENTO IMPUESTO A LA RENTA
                        if ( $expense->idtipotributo == REGIMEN_RETENCION && $expense->idcomprobante == DOC_RECIBO_HONORARIO ) 
                        {
                            $import_retencion = round( $expense->monto_tributo  * $tasaCompra , 2 , PHP_ROUND_HALF_DOWN );
                            $description_seat_renta4ta_deposit = strtoupper( 'RENTA 4TA CATEGORIA ' . $desc );
                            $total_percepciones += $expense->monto_tributo;
                            $seatList[] = $this->createSeatElement( $cuentaMkt , CUENTA_RENTA_4TA_HABER , '', $fecha_origen, '', '', '', '', '', '', '', 
                            ASIENTO_GASTO_DEPOSITO, $import_retencion , '' , $description_seat_renta4ta_deposit, '', '' , 'RENTA' );
                        }
                    }
                }
            }

            foreach( $solicitud->devolutions()->where( 'id_tipo_devolucion' , DEVOLUCION_INMEDIATA )->get() as $devolution )
            {
                $tasaCompra      = $this->getExpenseChangeRate( $solicitud , $devolution->updated_at );
                $devolution_date = date( 'd/m/Y' , strtotime( $devolution->updated_at ) ); 
                $import_devolucion = round( $devolution->monto  * $tasaCompra , 2 , PHP_ROUND_HALF_DOWN );
                $description_seat_devolucion = 'DEVOLUCION ' . $devolution->type->descripcion . ' - ' . $devolution->numero_operacion . ' - ' . strtoupper( $solicitud->assignedTo->personal->full_name );
                $seatList[] = $this->createSeatElement( $cuentaMkt , CUENTA_SOLES , '' , $devolution_date , '' , '' , '' , '' , '' , '' , '' , 
                    ASIENTO_GASTO_BASE , $import_devolucion , '' , $description_seat_devolucion , '' , '' , 'DEVOLUCION' );
            }

            // CONTRAPARTE ASIENTO DE ANTICIPO
            $nowChangeRate = $this->getExpenseChangeRate( $solicitud , $now );

            $description_seat_back = strtoupper($username . ' ' . $solicitud->titulo);
            $import_transf = round( ( $solicitud->detalle->monto_aprobado - $total_percepciones ) * $nowChangeRate , 2 , PHP_ROUND_HALF_DOWN );
            if( $solicitud->idtiposolicitud == REEMBOLSO )
            {
                $seatList[] = $this->createSeatElement( $cuentaMkt , $CUENTA_HABER_REEMBOLSO , '' , $now->format( 'd/m/Y' ) , '', '', '', '', '', '', '', 
                    ASIENTO_GASTO_DEPOSITO , $import_transf ,  '' , $description_seat_back, '', '' , 'CAN' );
            }
            else
            {
                if( in_array( $solicitud->id_inversion , [ 36 , 38 ] ) )
                {
                    if( $solicitud->detalle->deposit->account->idtipomoneda == SOLES )
                    {
                        $cuentaMkt = 1893000;
                    }
                    elseif( $solicitud->detalle->deposit->account->idtipomoneda == DOLARES )
                    {
                        $cuentaMkt = 1894000;
                    }
                }
            
                $seatList[] = $this->createSeatElement( $cuentaMkt , $cuentaMkt, '', $oldExpense->seat_date , '', '', '', '', '', '', '', 
                    ASIENTO_GASTO_DEPOSITO , $import_transf , '' , $solicitud->solicitud_caption , '' , '' , 'CAN' );
            }
            return $seatList;
        }
        return $middleRpta;
    }

    private function searchFundAccount($solicitud)
    {
        $fondo = $solicitud->investment->accountFund;
        if ( is_null( $fondo ) )
        {
            return $this->warningException('No se encontro el Fondo asignado a la solicitud', __FUNCTION__, __LINE__, __FILE__);
        }
        else
        {
            return $this->setRpta( $fondo );
        }
    }

    private function createSeatElement( $cuentaMkt , $account_number , $cod_snt , $fecha_origen , $iva , $cod_prov , $nom_prov, $cod, $ruc, $prefijo, $numero, $dc, $monto, $marca, $descripcion, $tipo_responsable, $origin , $type)
    {
        $seat                   = new StdClass;
        $seat->cuentaMkt        = $cuentaMkt;
        $seat->numero_cuenta    = $account_number;
        $seat->codigo_sunat     = $cod_snt;
        $seat->nro_origen       = $origin;
        $seat->fec_origen       = $fecha_origen;
        $seat->iva              = $iva;
        $seat->cod_prov         = $cod_prov;
        $seat->nombre_proveedor = $nom_prov;
        $seat->cod              = $cod;
        $seat->ruc              = $ruc;
        $seat->prefijo          = $prefijo;
        $seat->cbte_proveedor   = $numero;
        $seat->dc               = $dc;
        $seat->importe          = $monto;
        $seat->leyenda          = $marca;
        $seat->leyenda_variable = $descripcion;
        $seat->tipo_responsable = $tipo_responsable;
        $seat->type             = $type;
        return $seat;
    }


    // IDKC: CHANGE STATUS => GENERADO
    public function saveEntryExpense()
    {
        $inputs = Input::all();
        return $this->regularizationEntryOperation( $inputs[ 'solicitud_token' ] );
    }

    public function regularizationEntryOperation( $token )
    {
        try
        {
            $solicitud = Solicitud::findByToken( $token );
            
            if( $solicitud->id_estado != REGISTRADO )
            {
                return $this->warningException( 'La solicitud no se encuentra en la etapa del asiento de la regularizacion' , __FUNCTION__ , __LINE__ , __FILE__ );
            }
            
            $entries = $this->generateRegularizationSeatData( $solicitud );

            if( empty( $entries ) )
            {
                return $this->warningException( 'No existe informacion para poder registrar el asiento' , __FUNCTION__ , __LINE__ , __FILE__ );
            }

            unset( $solicitud->documentList );

            return $this->regularizationEntryTransaction( $solicitud , $entries );
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    private function regularizationEntryTransaction( $solicitud , $entries )
    {
        DB::beginTransaction();
        
        $oldIdEstado = $solicitud->id_estado;
        if ($solicitud->idtiposolicitud == REEMBOLSO )
        {
            $solicitud->id_estado = DEPOSITO_HABILITADO;
        }
        else
        {
            $solicitud->id_estado = GENERADO;
        }
        $solicitud->save();

        $entryAgrupation = [];
        foreach( $entries as $entry )
        {
            $tbEntry = new Entry;
            $tbEntry->insertRegularizationEntry( $entry , $solicitud->id );
            $entryAgrupation[ $solicitud->id ][ TIPO_ASIENTO_GASTO ][] = $tbEntry;
        }

        $user = Auth::user();

        if ($solicitud->idtiposolicitud == REEMBOLSO )
        {
            $middleRpta = $this->setStatus($oldIdEstado, $solicitud->id_estado , $user->id, USER_TESORERIA, $solicitud->id);
        }
        else
        {
            $middleRpta = $this->setStatus($oldIdEstado, $solicitud->id_estado , $user->id, $user->id, $solicitud->id);
        }

        if( $middleRpta[ status ] == ok ) 
        {
            $this->generateBagoSeat( $entryAgrupation );
            DB::commit();
            Session::put( 'state' , R_GASTO );
            $middleRpta[ 'asiento' ] = substr( $tbEntry->penclave , 0 , 5 );
            return $middleRpta;
        }
        DB::rollback();
        return $middleRpta;
        
    }

    private function validateInputAdvanceEntry($inputs)
    {
        $rules = array( 'solicitud_token' => 'required|min:1|exists:solicitud,token,id_estado,' . DEPOSITADO );
        $messages = array( 'solicitud_token.exists' => 'La solicitud no se encuentra en la etapa de generacion del asiento de la transferencia' );
        $validator = Validator::make( $inputs , $rules , $messages );
        
        if( $validator->fails() )
        {
            return $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );
        }
        else
        {
            return $this->setRpta();
        }
    }

    private function getExpenseChangeRate( $solicitud , $date )
    {        
        if( $solicitud->detalle->id_moneda == SOLES )
        {
            $tasaCompra = 1;
        }
        elseif( $solicitud->detalle->id_moneda == DOLARES )
        {
            $tc = ChangeRate::getDayTc( $date );
            if ( is_null( $tc ) )
            {
                $tasaCompra = ChangeRate::getTc()->venta;
            }
            else
            {
                $tasaCompra = $tc->venta;
            }
        }
        return $tasaCompra;
    }	

    public function generateAdvanceEntry()
    {
        try 
        {
            $middleRpta = array();
            $inputs = Input::all();
            $middleRpta = $this->validateInputAdvanceEntry( $inputs );
            if( $middleRpta[ status ] == ok ) 
            {
                $middleRpta = $this->advanceEntryOperation( $inputs[ 'solicitud_token' ] );    
            }
            return $middleRpta;
        }
        catch ( Exception $e )
        {
            DB::rollback();
            return $this->internalException($e, __FUNCTION__);
        }
    }

    public function advanceEntryOperation( $solicitud_token )
    {
        try
        {
            $solicitud = Solicitud::findByToken( $solicitud_token );
            if( ! in_array( $solicitud->idtiposolicitud , [ SOL_REP , REEMBOLSO , SOL_INST ] ) )
            {
                return $this->warningException( 'El tipo de la solicitud no es valido' , __FUNCTION__ , __LINE__ , __FILE__ );
            }
            $entries = $this->generateDepositEntryData( $solicitud );
            return $this->advanceEntryTransaction( $solicitud , $entries ); 
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    private function advanceEntryTransaction( $solicitud , array $entries )
    {
        DB::beginTransaction();

        $oldIdEstado = $solicitud->id_estado;

        if( $solicitud->idtiposolicitud == REEMBOLSO )
        {
            $solicitud->id_estado = GENERADO;
        }
        else
        {
            $solicitud->id_estado = GASTO_HABILITADO;    
        }
        
        $solicitud->save();

        $entryAgrupation = [];
        foreach( $entries as $entry ) 
        {
            $tbEntry = new Entry;
            $tbEntry->insertAdvanceEntry( $entry , $solicitud->id );
            $entryAgrupation[ $tbEntry->id_solicitud ][ $tbEntry->tipo_asiento ][] = $tbEntry;
        }

        $toUser = $solicitud->id_user_assign;
        $middleRpta = $this->setStatus( $oldIdEstado , $solicitud->id_estado , Auth::user()->id , $toUser , $solicitud->id );
        
        if( $middleRpta[ status ] === ok ) 
        {
            $this->generateBagoSeat( $entryAgrupation );
            DB::commit();
            Session::put( 'state' , R_REVISADO );
            $middleRpta[ 'asiento' ] = substr( $tbEntry->penclave , 0 , 5 ); 
            return $middleRpta;
        }
        DB::rollback();
        return $middleRpta;
        
        DB::rollback();
    }

    public function generateDepositEntryData( $solicitud )
    {
        $entries   = [];
        $entries[] = $this->generateDepositEntryDebitData( $solicitud );
        $entries[] = $this->generateDepositEntryCreditData( $solicitud );
        return $entries;
    }

    private function generateDepositEntryDebitData( $solicitud )
    {
        $detail               = $solicitud->detalle;
        $investment           = $solicitud->investment;
        $account              = $investment->accountFund;
        
        $entry                 = new stdClass;
        $entry->origin         = $detail->deposit->updated_at;
        $entry->d_c            = ASIENTO_GASTO_BASE;
        $entry->import         = $detail->soles_import;
        $entry->caption        = $solicitud->solicitud_caption;

        $CUENTA_HABER_REEMBOLSO = SpecialAccount::getRefund();

        if( $solicitud->idtiposolicitud == REEMBOLSO )
        {
            $num_cuenta = $CUENTA_HABER_REEMBOLSO;
            $entry->account_name   = PlanCta::find( $num_cuenta )->ctanombrecta;
            $entry->account_number = $num_cuenta; 
        }
        else
        {
            if( in_array( $solicitud->id_inversion , [ 36 , 38 ] ) )
            {
                if( $solicitud->detalle->deposit->account->idtipomoneda == SOLES )
                {
                    $num_cuenta = 1893000;
                }
                elseif( $solicitud->detalle->deposit->account->idtipomoneda == DOLARES )
                {
                    $num_cuenta = 1894000;
                }
                $entry->account_name   = PlanCta::find( $num_cuenta )->ctanombrecta;
                $entry->account_number = $num_cuenta;   
            }
            else
            {
                $entry->account_name   = $account->nombre;
                $entry->account_number = $account->num_cuenta;
            }
        }

        return $entry;
    }

    private function generateDepositEntryCreditData( $solicitud )
    {
        $detail                = $solicitud->detalle;
        $deposit               = $detail->deposit;
        $investment            = $solicitud->investment;
        $account               = $investment->accountFund;

        $entry                 = new stdClass;
        $entry->account_name   = $deposit->bagoAccount->ctanombrecta;
        $entry->account_number = $deposit->num_cuenta;
        $entry->origin         = $deposit->updated_at;
        $entry->d_c            = ASIENTO_GASTO_DEPOSITO;
        $entry->import         = $detail->soles_deposit_import;
        $entry->caption        = $solicitud->deposit_credit_caption;
        return $entry;
    }

    private function generateBagoSeat( array $entries )
    {
        $migrateSeatController = new MigrateSeatController;
        $migrateSeatController->transactionGenerateSeat( $entries );
    }

}