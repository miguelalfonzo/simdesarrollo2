<?php

use \Dmkt\Solicitud;
use \Common\State;
use \Common\StateRange;
use \System\SolicitudHistory;
use \Carbon\Carbon;
use \Expense\ChangeRate;

class BaseController extends Controller 
{
    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'put', 'patch', 'delete')));
    }

    protected function getDateExchangeRate( $date )
    {
        $formatDate = Carbon::createFromFormat( 'Y-m-d' , $date )->format( 'Y/m/d' );
        $tc = ChangeRate::getDayTc( $formatDate );
        if( is_null( $tc ) )
        {
            $tc = ChangeRate::getTc();
        }
        $tasa = $tc->venta;
        return $tasa;
    }

    protected function getExchangeRate( $solicitud )
    {
        if ( $solicitud->detalle->id_moneda == SOLES )
            $tasa = 1;
        elseif( $solicitud->detalle->id_moneda == DOLARES )
        {
            $tc = ChangeRate::getTc();
            $tasa = $tc->venta;
        }
        return $tasa;
    }

    protected function getApprovalExchangeRate( $solicitud )
    {
        if( $solicitud->detalle->id_moneda == SOLES )
        {
            $tasa = 1;
        }
        elseif( $solicitud->detalle->id_moneda == DOLARES )
        {
            //BUSQUEDA DEL TIPO DE CAMBIO DEL DIA PASADO , DEBIDO A QUE NO EXISTE TIPO DE CAMBIO DEL DIA ACTUAL
            $pastDay = Carbon::createFromFormat( 'Y-m-d H:i' , $solicitud->approvedHistory->updated_at )->subDay()->format( 'Y/m/d' );
            $tc = ChangeRate::getDayTc( $pastDay );
            if( is_null( $tc ) )
            {
                $tc = ChangeRate::getTc();
            }
            $tasa = $tc->venta;
        }
        return $tasa;
    }

    protected function getExpenseDate( $solicitud , $range = 0 )
    {
        if( $solicitud->idtiposolicitud == 3 )
        {
            $expenseDate = new Carbon( 'first day of August 2015' );
        }
        else
        {
            $expenseDate = Carbon::now()->subDays( 30 )->max( $solicitud->created_at );
        }
        return [ 'startDate' => $expenseDate->format( 'd/m/Y' ) , 'endDate' => Carbon::now()->format( 'd/m/Y' ) ];
    }

    protected function getDay( $type=1 )
    {
        $currentDate = getdate();
        $toDay = $currentDate['mday']."/".str_pad( $currentDate['mon'] , 2 , '0' , STR_PAD_LEFT )."/".$currentDate['year'];
        $lastDay = '06/'.str_pad( ( $currentDate['mon'] + 1 ) , 2 , '0' , STR_PAD_LEFT ).'/'.$currentDate['year'];
        
        if ( $type == 2 )
        {
            $toDay = $currentDate['year']."/".str_pad( $currentDate['mon'] , 2 , '0' , STR_PAD_LEFT )."/".$currentDate['mday'].' '.$currentDate['hours'].':'.$currentDate['minutes'].':'.$currentDate['seconds'];
            $lastDay = $currentDate['year']."/".str_pad( $currentDate['mon'] , 2 , '0' , STR_PAD_LEFT )."/".'06'.' '.$currentDate['hours'].':'.$currentDate['minutes'].':'.$currentDate['seconds'];   
        }

        $date = array( 'toDay' => $toDay , 'lastDay' => $lastDay );
        return $date;
    }

    protected function msgValidator( $validator )
    {
        $rpta = '';
        foreach( $validator->messages()->all() as $msg )
        {
            $rpta .= $msg.'<br> ';
        }
        return substr( $rpta , 0 , -1 );
    }

    protected function msg2Validator( $validator )
    {
        $rpta = '';
        foreach( $validator->messages()->all() as $msg )
        {
            $rpta .= $msg . ' - ' ;
        }
        return substr( $rpta , 0 , -1 );
    }

	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

    protected function warningException( $description , $function , $line , $file , $sendMail = 0 )
    {
        Log::error( $description );
        if( $sendMail === 1 )
        {
            Mail::send( 'soporte' , array( 'description' => $description , 'function' => $function , 'line' => $line , 'file' => $file ) , 
            function( $message ) use( $function )
            {
                $message->to( [ SOPORTE_EMAIL_1 => POSTMAN_USER_NAME_1 /*, SOPORTE_EMAIL_2 => POSTMAN_USER_NAME_2 */ ] )
                ->subject( warning.' - Function: '. $function );      
            });
            return array( status => error , description => $description );
        }
        else
        {
            return array( status => warning , description => $description );
        
        }
    }

    public function internalException( $exception , $function , $type = 'System' )
    {
        Log::error( $exception );
        if ( $exception->getCode() != 1 )
        {
            Mail::send('soporte', array( 'exception' => $exception ), function( $message ) use( $function )
            {
                $message->to( array( SOPORTE_EMAIL_1 => POSTMAN_USER_NAME_1 , SOPORTE_EMAIL_2 => POSTMAN_USER_NAME_2 ) )
                ->subject( error.' - Function: '.$function );
            });
        }
        else
        {
            if( strpos( $exception->getMessage() , 'ORA-00001' ) )
            {
                return array( status => error , description => $type . ':  La informacion ingresada ya existe y no puede repetir la informacion de la tabla' );
            }
        }
        return array( status => error , description => $type . ': ' . $exception->getMessage() );
    }

    private function validateJson()
    {
        $rpta = array();
        if (json_last_error() === 0)
        {
            $rpta[status] = ok;
        }
        else
        {
            $rpta[status] = warning;
            $rpta[description]='Error parseando Json: '.json_last_error_msg();
        }
        return $rpta;
    }

    public function postman( $idSolicitud, $fromEstado, $toEstado, $toUser )
    {
        try 
        {
            $solicitud = Solicitud::find( $idSolicitud );
            $subject    = 'Solicitud N° '.$idSolicitud;
            
            $user_name  = array();
           
            foreach ( $toUser as $user )
                $user_name[ $user->email ] = $user->getName();
        
            if( count( $user_name ) != 0 )
            {
                $data = array(
                    'solicitud_id'          => $idSolicitud ,
                    'solicitud_estado'      => $toEstado ,
                    'solicitud_titulo'      => $solicitud->titulo ,
                    'solicitud_descripcion' => $solicitud->descripcion
                );
                Mail::send( 'emails.notification' , $data , function( $message ) use ( $subject , $user_name )
                {
                    $message->to( $user_name )->subject( $subject );
                });
                return $this->setRpta();
            }
            else
            {
                Mail::send('soporte', array( 'subject' => 'No se pudo enviar email, debido a que el usuario o password son Incorrectos' ) , function($message) use ($subject)
                {
                    $message->to( array( SOPORTE_EMAIL_1 => POSTMAN_USER_NAME_1 , SOPORTE_EMAIL_2 => POSTMAN_USER_NAME_2 ) )
                    ->subject('PostMan [BaseController:89]:');
                });
                Log::error("IDKC [BaseController:109]: No se pudo enviar email, debido a que el usuario o password son Incorrectos");
                return $this->warningException( 'No se encontro los usuarios para la solicitud n°: '. $idSolicitud , __FUNCTION__ , __LINE__ , __FILE__ );
            }
        }
        catch ( Swift_TransportException $e )
        {
            return $this->warningException( "Swift_TransportException: No se pudo enviar el correo al destinatario" , __FUNCTION__ , __LINE__ , __FILE__ );
        }
        catch (Exception $e)
        {
            return $this->internalException($e,__FUNCTION__);
        }
    }

    public function setStatus( $status_from , $status_to , $user_from_id , $user_to_id , $idSolicitud )
    {
        $fromStatus = State::where( 'id' , $status_from )->first();
        $toStatus   = State::where( 'id' , $status_to )->first();
        
        $statusNameFrom = $fromStatus == null ? '' : $fromStatus->nombre;
        $statusNameTo   = $toStatus == null ? '' : $toStatus->nombre;
        
        #$fromUser   = User::where('id' , $user_from_id)->first();
        $fromUser   = User::where('id' , $user_from_id)->first();

        if ( is_array( $user_to_id ) )
            $toUser     = User::whereIn( 'id' , $user_to_id )->get();
             
        else
            $toUser     = User::where( 'id' , $user_to_id )->get();
           
        // POSTMAN: send email
        $rpta = $this->postman( $idSolicitud , $statusNameFrom , $statusNameTo , $toUser );

        $toUser = array_unique( $toUser->lists('type') );

        if ( count( $toUser ) > 1 )
            return $this->warningException( 'No se puede enviar la solicitud a mas de un tipo de usuario: '.explode(',' , $toUser ) , __FUNCTION__ , __LINE__ , __FILE__ );

        if ( $rpta[status] == ok  )
            return $this->updateStatusSolicitude( $status_from , $status_to , $fromUser , $toUser[0] , $idSolicitud , 1 );
        else if ( $rpta[status] == warning ){
            #$toUser = User::where( 'id' , $user_to_id )->get();
            #$toUser = array_unique( $toUser->lists('type') );
            return $this->updateStatusSolicitude( $status_from , $status_to , $fromUser , $toUser[0] , $idSolicitud , 0 );
        }else
            return $rpta;
    }

    protected function updateStatusSolicitude( $status_from , $status_to , $user_from , $user_to , $idSolicitud , $notified )
    {   
        $fData = array( 'status_to'    => $status_to ,
                        'id_solicitud' => $idSolicitud ,
                        'user_from'    => $user_from->type );

        $statusSolicitude = SolicitudHistory::firstOrNew($fData);
        if ( ! isset( $statusSolicitude->rn ) )
            $statusSolicitude->id = $statusSolicitude->lastId() + 1;
        else
            $statusSolicitude = SolicitudHistory::find( $statusSolicitude->id );
        
        $statusSolicitude->status_from  = $status_from;
        $statusSolicitude->user_to      = $user_to;
        $statusSolicitude->notified     = $notified;
        $statusSolicitude->updated_at   = Carbon::now();
        if ( is_null( $statusSolicitude->user_to ) )
            return $this->warningException( 'No se pudo determinar el tipo de usuario para enviar la solicitud' , __FUNCTION__ , __LINE__ , __FILE__ );    
        $statusSolicitude->save();
        return $this->setRpta();
    }

    protected function setRpta( $data='' , $description = '' )
    {
        return array( status => ok , data => $data , description => $description );
    }

    public function getLeyenda()
    {
        #$data = array('states' => StateRange::order());
        $data = array('states' => StateRange::orderSP());
        return View::make('template.leyenda', $data);
    }

    protected function trim_text( $input, $length ) 
    {
        if( $length > 0 )
        {
            $inputLength = strlen( $input );
            if( $inputLength > $length )
            {
                return substr( $input , $length * -1 );
            }
            else
            {
                return $input;
            }
        }
        else
        {
            return '';
        }
    }
}
