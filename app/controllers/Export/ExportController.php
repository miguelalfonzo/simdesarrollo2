<?php

namespace Export;

use \BaseController;
use \Input;
use \View;
use \Exception;
use \PDF;
use \Dmkt\Solicitud;
use \Excel;
use \Carbon\Carbon;
use \Session;
use \File;
use \Response;

class ExportController extends BaseController
{
	public function exportSolicitudToDepositPDF()
	{
		try
		{
			$data = array( 'solicituds' => Solicitud::getDepositSolicituds( Carbon::now()->year ) );
			$view = View::make( 'Dmkt.Cont.SolicitudsToDeposit.pdf' , $data )->render();
			return PDF::load( $view , 'A4' , 'landscape' )->show();
		}
		catch( Exception $e )
		{
			return $this->internalException( $e , __FUNCTION__ );
		}
	}

	public function exportSolicitudToDepositExcel()
	{
		try
		{
			$solicituds = Solicitud::getDepositSolicituds( Carbon::now()->year );
			$data = array( 'solicituds' => $solicituds );
			Excel::create( 'Solicitudes a Depositar' , function( $excel ) use( $data )
			{
				$excel->sheet( 'solicitudes' , function( $sheet ) use( $data )
				{
					$sheet->loadView( 'Dmkt.Cont.SolicitudsToDeposit.excel' , $data );
				});
			})->download( 'xls' );
		}
		catch( Exception $e )
		{
			return $this->internalException( $e , __FUNCTION__ );
		}
	}

    public function revisionExport()
    {
        try
        {
            $now           = Carbon::now();
            $date          = $now->toDateString();
            $title         = 'Detalle del Procesamiento de Solicitudes-';
            $fullTitle     = $title . $date;
            $directoryPath = 'files/revisiones';
            $filePath      = $directoryPath . '/' . $fullTitle . '.xls';
            $sessionName   = 'revisiones';
            $view          = 'Dmkt.Cont.Excel.revision_detail';
        
            $middleRpta = $this->validateAccountingExport( $directoryPath , $sessionName , $filePath );
            if( $middleRpta[ status ] == ok )
            {
                return $this->accountingExcel( $view , $middleRpta[ data ] , $fullTitle , $directoryPath , $filePath );
            }
            return $middleRpta;
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    public function advanceEntryExport()
    {
        try
        {
            $now           = Carbon::now();
            $date          = $now->toDateString();
            $title         = 'Detalle del Procesamiento de Solicitudes-';
            $fullTitle     = $title . $date;
            $directoryPath = 'files/asientos/anticipo';
            $filePath      = $directoryPath . '/' . $title . $date . '.xls';
            $sessionName   = 'asientos_anticipo';
            $view          = 'Dmkt.Cont.Excel.entry_detail';
        
            $middleRpta = $this->validateAccountingExport( $directoryPath , $sessionName , $filePath );
            if( $middleRpta[ status ] == ok )
            {
                return $this->accountingExcel( $view , $middleRpta[ data ] , $fullTitle , $directoryPath , $filePath );
            }
            return $middleRpta;
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    public function regularizationEntryExport()
    {
        try
        {
            $now           = Carbon::now();
            $date          = $now->toDateString();
            $title         = 'Detalle del Procesamiento de Solicitudes-';
            $fullTitle     = $title . $date;
            $directoryPath = 'files/asientos/regularizacion';
            $filePath      = $directoryPath . '/' . $title . $date . '.xls';
            $sessionName   = 'asientos_regularizacion';
            $view          = 'Dmkt.Cont.Excel.entry_detail';
        
            $middleRpta = $this->validateAccountingExport( $directoryPath , $sessionName , $filePath );
            if( $middleRpta[ status ] == ok )
            {
                return $this->accountingExcel( $view , $middleRpta[ data ] , $fullTitle , $directoryPath , $filePath );
            }
            return $middleRpta;
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

	private function validateAccountingExport( $directoryPath , $sessionName , $filePath )
    {      
        $data = [];
        if( File::exists( public_path( $filePath ) ) )
        {
            $oldResponses = Excel::load( public_path( $filePath ) )->get();
            $data[ 'oldResponses' ] = $oldResponses;
        }

        if( Session::has( $sessionName ) )
        {
            $responses = Session::pull( $sessionName );
            $data[ 'responses' ] = $responses;
        }

        if( ! isset( $oldResponses ) && ! isset( $responses ) )
        {
            return $this->warningException( 'No se pudo exportar el excel con las observaciones de las solicitudes procesadas' , __FUNCTION__ , __LINE__ , __FILE__ );
        }

        return $this->setRpta( $data );
    }

    private function accountingExcel( $view , $data , $fullTitle , $directoryPath , $filePath )
    {     
        Excel::create( $fullTitle , function( $excel ) use( $view , $data )
        {
            $excel->sheet( 'solicitudes' , function( $sheet ) use( $view , $data )
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
                $sheet->loadView( $view , $data );
            });
        })->store( 'xls' , public_path( $directoryPath ) );
        return Response::download( $filePath );
    }

}