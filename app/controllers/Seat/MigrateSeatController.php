<?php

namespace Seat;

use \BaseController;
use \Input;
use \View;
use \Exception;
use \Expense\Entry;
use \Carbon\Carbon;
use \DB;
use \Excel;
use \Dmkt\Solicitud;

class MigrateSeatController extends BaseController
{
	public function migrateSeats()
	{
		try
		{
			$data = array( 'ok' => array() , 'error' => array() );
			$systemSeats = Entry::whereNull( 'estado' )->orderBy( 'id_solicitud' , 'asc' )->orderBy( 'tipo_asiento' , 'asc' )
						   ->orderBy( 'updated_at' , 'asc' )->orderBy( 'id' , 'asc' )->get();
			$seats = array();
			foreach( $systemSeats as $key => $systemSeat )
			{
				if( isset( $seats[ $systemSeat->id_solicitud ][ $systemSeat->tipo_asiento ] ) )
				{
					$seats[ $systemSeat->id_solicitud ][ $systemSeat->tipo_asiento ][] = $systemSeat;				
				}
				else
				{
					$seats[ $systemSeat->id_solicitud ][ $systemSeat->tipo_asiento ]   = array();
					$seats[ $systemSeat->id_solicitud ][ $systemSeat->tipo_asiento ][] = $systemSeat;			
				}
			}
			DB::beginTransaction();	
			$data = $this->transactionGenerateSeat( $seats );
			DB::commit();
			return $this->generateSeatExcel( $data );
		}
		catch( Exception $e )
		{
			$this->internalException( $e , __FUNCTION__ );
			return $this->generateSeatExcel( $data );
		}
	}

	public function transactionGenerateSeat( $seats )
	{
		$penclave = array();
		$errors   = array();
		foreach( $seats as $solicitud_id => $entryTypes )
		{
			foreach( $entryTypes as $entryType => $entries )
			{
				$this->migrateSeat( $entryType , $entries , $solicitud_id , $penclave , $errors );
			}
		}
		return array( 'ok' => $penclave , 'error' => $errors );
	}

	public function migrateSeat( $seatType , $seats , $idSolicitud , &$penclave , &$errors )
	{
		$solicitud = Solicitud::find( $idSolicitud );
		if( in_array( $solicitud->id_inversion , [ 36 , 38 ] ) && $seatType == 'G' )
		{
			$origen = 4; // CODIGO PARA EL ASIENTO DE DOCUMENTOS SOLO PARA LA INVERSION VMJ y HOLISTIC SOLUTIONS
		}
		else
		{	
			$origen = $seatType == 'A' ? 7 : ( $seatType == 'G' ? 1 : NULL );// ASIENTO DE DEPOSITO => 7 ( EXCEPCION CHEQUES ) | ASIENTO DE DOCUMENTO => 1 ( EXCEPCION PROVEEDORES ) 
		}

		$year   = Carbon::now()->year;
		
		if ( is_null( $origen ) )
		{
			$errors[ $idSolicitud ][ $seatType ] = 'No se pudo determinar el origen del asiento ' . $seatType . ' de la solicitud ' . $idSolicitud ;
			DB::rollback();
			return;
		}
		else if( $origen === 7 || $origen === 4 )
		{
			$penclave[ $idSolicitud ][ $seatType ] = SeatCod::generateSeatCod( $year , $origen );
			if ( $penclave[ $idSolicitud ][ $seatType ] === 0 )
			{
				unset( $penclave[ $idSolicitud ][ $seatType ] );
				$errors[ $idSolicitud ][ $seatType ] = 'No se pudo determinar el origen del asiento ' . $seatType . ' de la solicitud ' . $idSolicitud ;
				DB::rollback();
				return;		
			}
		}
		else if( $origen === 1 )
		{
			$penclave[ $idSolicitud ][ $seatType ] = Seat::generateManualSeatCod( $year , $origen );

			if ( $penclave[ $idSolicitud ][ $seatType ] === 0 )
			{
				unset( $penclave[ $idSolicitud ][ $seatType ] );
				$errors[ $idSolicitud ][ $seatType ] = 'No se pudo determinar el origen del asiento ' . $seatType . ' de la solicitud ' . $idSolicitud ;
				DB::rollback();
				return;		
			}
		}
		foreach( $seats as $key => $seat )
		{
			$middleRpta = $this->registerSeatLines( $key , $seat , $penclave[ $idSolicitud ][ $seatType ] );
			if ( $middleRpta[ status ] !== ok )
			{
				unset( $penclave[ $idSolicitud ][ $seatType ] );
				$errors[ $idSolicitud ][ $seatType ] = 'No se pudo registar la fila ' . ( $key + 1 ) . ' del asiento ' . $seatType . ' de la solicitud ' . $idSolicitud ;
				DB::rollback();
				return;
			}
		}
	}

	private function registerSeatLines( $key , $seat , $seatPrefix )
	{
		if ( $key == 0 )
		{
			$state = 'C';
		}
		else
		{
			$state = ' ';
		}
		return Seat::registerSeat( $seat , $seatPrefix , $key , $state );
	}

	private function generateSeatExcel( $data )
	{
		$date = Carbon::now()->format( 'Y-m-d H-i-s' );
		
		Excel::create( 'Reporte de Asiento '. $date , function( $excel ) use ( $data  )
        {  
            $excel->sheet( 'Asientos' , function( $sheet ) use ( $data )
            {
                $sheet->loadView( 'Dmkt.Cont.seatMigration' , $data ); 
            });  
        })->store( 'xlsx' , public_path( 'files/asientos' ) )->export('xls');    
    }
}