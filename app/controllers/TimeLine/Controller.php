<?php

namespace TimeLine;

use \BaseController;
use \Dmkt\Solicitud;
use \System\SolicitudHistory;
use \System\TiempoEstimadoFlujo;
use \Common\TypeUser;
use \View;


class Controller extends BaseController
{
    public function getTimeLine($id)
    {
        $solicitud = Solicitud::find($id);
        //$solicitud_history = $solicitud->histories;
        $solicitud_history = SolicitudHistory::where('id_solicitud', '=', $id)
            ->orderby('ID', 'ASC')
            ->get();
        $time_flow_event = TiempoEstimadoFlujo::all();
        $previus_date = null;
        $orden_history = 0;
        $duration_limit = 5;
        $duration_limit_max = 10;
        foreach ( $solicitud_history as $history ) 
        {
            foreach( $time_flow_event as $time_flow )
            {
                if( $time_flow->status_id == $history->status_from && $time_flow->to_user_type == $history->user_from)
                {
                    $history->estimed_time = $time_flow->hours;
                    break;
                }
            }
        }

        foreach( $solicitud_history as $history ) 
        {
            if( $previus_date ) 
            {
                $date_a   = $history->created_at;
                $date_b   = $previus_date;
                $interval = date_diff( $date_a, $date_b );
                $days     = $interval->days;
                $history->duration = $interval->h < 1? $interval->format('%i M') :$interval->format('%h H');
                if ($interval->h <= $history->estimed_time){
                    $history->duration_color = 'success';
                    $history->hand = 'glyphicon-thumbs-up';
                }
//              elseif( $interval->h <= $duration_limit_max )
//              {
//                    $history->duration_color = 'warning';
//              }
                else
                {
                    $history->duration_color = 'danger';
                    $history->hand = 'glyphicon-thumbs-down';
                }
            }
            $previus_date = $history->created_at;
            $history->orden = $orden_history;
            $orden_history++;

        }
        $tasa = $this->getExchangeRate( $solicitud );

        $flujo1 = $solicitud->investment->approvalInstance->approvalPolicies()
            ->orderBy( 'orden' , 'ASC' )->get();
        $flujo = array();

        if( is_null( $solicitud->approvedHistory ) )
        {
            foreach( $flujo1 as $fl )
            {
                if( $fl->desde == null )
                {
                    $flujo[] = $fl;
                }
                elseif( $fl->desde < ( $solicitud->detalle->monto_actual * $tasa ) || ( $solicitud->id_estado == DERIVADO && $fl->tipo_usuario == GER_PROD ) )
                {    
                    $flujo[] = $fl;
                }
            }
        }
        else
        {
            foreach( $solicitud->histories()->whereIn( 'status_to' , array( DERIVADO , ACEPTADO , APROBADO ) )->orderBy( 'created_at' , 'id' )->get() as $approvalFlow )
            {
                $approvalFlow->tipo_usuario = $approvalFlow->user_to;
                $flujo[] = $approvalFlow;
            }
        }


        $type_user = TypeUser::all();
        foreach( $flujo as $fl ) 
        {
            foreach( $type_user as $type ) 
            {
                if( $fl->tipo_usuario == $type->codigo ) 
                {
                    $fl->nombre_usuario = $type->descripcion;
                    break;
                }
            }
        }

        $status_flow = null;
        foreach( $flujo as $fl ) 
        {
            if(isset($status_flow))
            {
                $fl->status = 2;
            }
            else
            {
                $status_flow = 1;
                $fl->status = 1;
            }

            foreach( $time_flow_event as $time_flow )
            {
                if ($time_flow->status_id == $fl->status && $time_flow->to_user_type == $fl->tipo_usuario)
                {
                    $fl->estimed_time = $time_flow->hours;
                    break;
                }
            }
        }
        $linehard = unserialize(TIMELINEHARD);
        $linecese = unserialize(TIMELINECESE);
        //$motivo = $solicitud->detalle->id_motivo;
        $motivo = $solicitud->idtiposolicitud;

        $line_static = array();
        foreach ( $linehard as $line ) 
        {
            $cond = false;
            $condFin = false;
            foreach ($line as $key => $value) 
            {
                if( $solicitud->state->id_estado == R_NO_AUTORIZADO )
                {
                    break;
                }

                if( $key == 'status_id' && $value == GASTO_HABILITADO )
                {
                    $line[ 'info' ] = is_null( $solicitud->id_user_assign ) ? $line[ 'info' ] : strtoupper( $solicitud->assignedTo->personal->full_name );
                }

                if ( $key == 'cond' ) 
                {
                    $cond = true;
                }

                if ( $key == 'cond_add_motivo' ) 
                {
                    if ( $motivo == $value )
                    {
                        $cond = true;
                    }
                    else
                    {
                        $cond = false;
                    }
                }
                
                if ( $key == 'cond_sub_motivo' )
                {
                    if ( $motivo == $value )
                    {
                        $cond = false;
                    }
                    else
                    {
                        $cond = true;
                    }
                }
                
                if( $key == 'cond_cese' )
                {
                    if( $value && $solicitud->id_estado == 30 )
                    {
                        array_push( $line_static , $linecese[ 1 ] );
                        $condFin = true;
                    }
                }
            }
            if( $condFin )
            {
                break;
            }
            elseif ($cond)
            {
                array_push($line_static, $line);
            }
        }

        $devolutionHistory = $this->getDevolutionTimeLine( $solicitud );

        return  View::make('template.Modals.timeLine2')
                ->with( array(
                    'solicitud'         => $solicitud, 
                    'solicitud_history' => $solicitud_history, 
                    'flujo'             => $flujo, 
                    'line_static'       => $line_static, 
                    'time_flow_event'   => $time_flow_event,
                    'devolutions'       => $devolutionHistory )
                )->render();
    }

    private function getDevolutionTimeLine( $solicitud )
    {
        return $solicitud->devolutions()->where( 'id_tipo_devolucion' , DEVOLUCION_INMEDIATA )
               ->orderBy( 'created_at' , 'ASC' )->orderBy( 'id' , 'ASC' )->get();
    }
}