<?php

namespace Report;

use \BaseController;
use \View;
use \Custom\DataList;
use \Input;
use \Carbon\Carbon;
use \Excel;
use \Response;

class Accounting extends BaseController
{

  public function show( $type )
  {
    $data = $this->getReportType( $type );
    return View::make( $data[ 'View' ] , $data );
  }

  private function getReportType( $type )
  {
    if( $type == 'cuenta' )
    {
      $data =
      [
        'View' => 'Report.account.view',
        'type' => $type,
                'title' => 'Reporte de Estado de Cuenta'
      ];
    }
    elseif( $type == 'completo' )
    {
        $data =
        [
            'View'  => 'Report.account.view',
            'type'  => $type,
            'title' => 'Reporte Completo'
        ];
    }
    return $data;
  }

  public function source()
  {
    try
    {
        $inputs = Input::all();

        $data = $this->getData( $inputs );

        if( isset( $data[ status ] ) && $data[ status ] == error )
        {
            return $data;
        }

        $format = $this->getFormat( $inputs[ 'type' ] );

        $rpta = $this->setRpta( $data );

        $rpta = array_merge( $rpta , $format );

        return $rpta;
    }
    catch( Exception $e )
    {
        return $this->internalException( $e , __FUNCTION__ );
    }
  }

    private function getData( $inputs )
    {
        $dates  = [ 'start' => $inputs[ 'fecha_inicio' ] , 'end' => $inputs[ 'fecha_final' ] ];

        switch( $inputs[ 'type' ] )
        {
            case 'cuenta':
                $data = DataList::getAmountReport( $inputs[ 'colaborador' ] , $dates , $inputs[ 'num_cuenta' ] , $inputs[ 'solicitud_id' ] , $inputs[ 'depurado' ] );
                break;
            case 'completo':
                $data = DataList::getCompleteAccountReport( $inputs[ 'colaborador' ] , $dates , $inputs[ 'num_cuenta' ] , $inputs[ 'solicitud_id' ] , $inputs[ 'depurado' ] );
                break;
        }
        return $data;
    }

    private function getFormat( $type )
    {
        if( $type == 'cuenta' )
        {
            $columns =
                [
                    [ 'title' => '#' , 'data' => 'id' , 'className' => 'text-center' ],
                    [ 'title' => 'Colaborador' , 'data' => 'empl_nom' , 'className' => 'text-center' ],
                    [ 'title' => 'Inversion' , 'data' => 'inversion' , 'className' => 'text-center' ],
                    [ 'title' => 'Detalle' , 'data' => 'detalle' , 'className' => 'text-center' ],
                    [ 'title' => 'Cuenta' , 'data' => 'cuenta_num' , 'className' => 'text-center' ],
                    [ 'title' => 'Fecha Deposito' , 'data' => 'dep_fec' , 'className' => 'text-center' ],
                    [ 'title' => 'Monto Depositado' , 'data' => 'dep_mon' , 'className' => 'text-center' ],
                    [ 'title' => 'Monto Regularizado' , 'data' => 'reg_mon' , 'className' => 'text-center' ],
                    [ 'title' => 'Monto Devuelto' , 'data' => 'dev_mon' , 'className' => 'text-center' ],
                    [ 'title' => 'Estado de Cuenta' , 'data' => 'debe' , 'className' => 'text-center' ]
                ];
        }
        else
        {
            $columns =
                [
                    [ 'title' => '#' , 'data' => 'id' , 'className' => 'text-center' ],
                    [ 'title' => 'Colaborador' , 'data' => 'resp_nom' , 'className' => 'text-center' ],
                    [ 'title' => 'Inversion' , 'data' => 'inversion' , 'className' => 'text-center' ],
                    [ 'title' => 'Detalle' , 'data' => 'detalle' , 'className' => 'text-center' ],
                    [ 'title' => 'Cuenta' , 'data' => 'cta' , 'className' => 'text-center' ],
                    [ 'title' => 'Fecha Deposito' , 'data' => 'dep_fec' , 'className' => 'text-center' ],
                    [ 'title' => 'Depositado' , 'data' => 'dep_mon' , 'className' => 'text-center' ],
                    [ 'title' => 'N° Operacion' , 'data' => 'dep_num' , 'className' => 'text-center' ],
                    [ 'title' => 'N° A. Anticipo' , 'data' => 'asi_ant_num' , 'className' => 'text-center' ],
                    [ 'title' => 'N° A. Regularizacion' , 'data' => 'asi_reg_num' , 'className' => 'text-center' ],
                    [ 'title' => 'Comprobante' , 'data' => 'comp_nom' , 'className' => 'text-center' ],
                    [ 'title' => 'RUC' , 'data' => 'comp_ruc' , 'className' => 'text-center' ],
                    [ 'title' => 'Razon' , 'data' => 'comp_raz' , 'className' => 'text-center' ],
                    [ 'title' => 'N°' , 'data' => 'comp_num' , 'className' => 'text-center' ],
                    [ 'title' => 'Fecha Doc.' , 'data' => 'comp_fec' , 'className' => 'text-center' ],
                    [ 'title' => 'Descripcion' , 'data' => 'comp_des' , 'className' => 'text-center' ],
                    [ 'title' => 'Sub Total' , 'data' => 'comp_subt' , 'className' => 'text-center' ],
                    [ 'title' => 'Impuesto Servicio' , 'data' => 'comp_iser' , 'className' => 'text-center' ],
                    [ 'title' => 'IGV' , 'data' => 'comp_igv' , 'className' => 'text-center' ],
                    [ 'title' => 'Reparo' , 'data' => 'comp_rep' , 'className' => 'text-center' ],
                    [ 'title' => 'Retencion' , 'data' => 'comp_ret' , 'className' => 'text-center' ],
                    [ 'title' => 'Detraccion' , 'data' => 'comp_det' , 'className' => 'text-center' ],
                    [ 'title' => 'Total' , 'data' => 'comp_tot' , 'className' => 'text-center' ],
                    [ 'title' => 'Detalle Cantidad' , 'data' => 'comp_d_cant' , 'className' => 'text-center' ],
                    [ 'title' => 'Detalle Descripcion' , 'data' => 'comp_d_des' , 'className' => 'text-center' ],
                    [ 'title' => 'Detalle Tipo' , 'data' => 'comp_d_tip' , 'className' => 'text-center' ],
                    [ 'title' => 'Detalle Monto' , 'data' => 'comp_d_tot' , 'className' => 'text-center' ],
                    [ 'title' => 'Devolucion' , 'data' => 'dev_mon' , 'className' => 'text-center' ]
                ];
        }
        return [ 'columns' => $columns ];
    }

  public function export()
  {
        try
        {
        $inputs = Input::all();
            $dates  = [ 'start' => $inputs[ 'fecha_inicio' ] , 'end' => $inputs[ 'fecha_final' ] ];
            $data   = $this->getData( $inputs );

            if( isset( $data[ status ] ) && $data[ status ] == error )
            {
                return $data;
            }

            $format = $this->getFormat( $inputs[ 'type' ] );
            $columns = $format[ 'columns' ];

            $now = Carbon::now();
            $title = 'Rep-' . $now->format( 'YmdHi' );
            $directoryPath = 'files/reporte/contabilidad/' . $inputs[ 'type' ];

        Excel::create( $title , function( $excel ) use ( $data , $columns )
            {
                $excel->sheet( 'Data' , function( $sheet ) use ( $data , $columns )
                {
                    $sheet->freezeFirstRow();
                    $sheet->loadView( 'Report.account.table' , [ 'data' => $data , 'columns' => $columns ] );
                });
            })->store( 'xls' , public_path( $directoryPath ) );

            $rpta = $this->setRpta();
            $rpta[ 'title' ] = $title;
            return $rpta;
      }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }

    public function download( $type , $title )
    {
        try
        {
            $directoryPath = 'files/reporte/contabilidad/' . $type ;
            $filePath = $directoryPath . '/' . $title . '.xls';
            return Response::download( $filePath );
        }
        catch( Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ );
        }
    }
}