<div class="timeLineModal">
    <div class="container-fluid hide">
        <h6 class="text-center">{{ $solicitud->typeSolicitude->nombre }}</h6>
        <div class="stage-container">
            @for ($i = 0; $i < count($solicitud_history); $i++)
                {{ ''; $history = $solicitud_history[$i] }}
                <div class="stage col-md-3 col-sm-3 @if( in_array( $history->status_to , array( 8 , 9 , 30 , 29 ) ) ) rejected @else success @endif">
                    <div class="stage-header @if( in_array( $history->status_to , array( 8 , 9 , 30 , 29 ) ) ) stage-rejected @else stage-success @endif"></div>
                    <div class="stage-content">
                        <h3 class="stage-title" style="white-space:nowrap">
                            @if($i==0)
                                @if($solicitud->idtiposolicitud == SOL_INST )
                                    Inicio de Fondo Institucional
                                @else
                                    Inicio de Solicitud
                                @endif
                            @elseif ( $history->status_to == ACEPTADO || $history->status_to == APROBADO )
                                @if( is_null( $solicitud->investment->approvalInstance->approvalPolicyType( $history->user_from )->desde ) && is_null( $solicitud->investment->approvalInstance->approvalPolicyType( $history->user_from )->hasta ) )
                                    Validacion {{ $history->user_from }}
                                @else
                                    Aprobacion {{ $history->user_from }}
                                @endif  
                            @elseif ( $history->status_to == 30 || $history->status_to == 29 )
                                {{ $history->toState->nombre }}
                            @elseif( $history->status_to == 8 )
                                Cancelado
                            @else 
                                {{ $history->statusFrom->descripcion_min }}
                            @endif
                        </h3>
                        <span class="label label-info">{{ strtoupper( $history->createdBy->personal->full_name ) }} </span>
                        
                        @if($history->estimed_time)
                            <div class="time-estimated">
                                <span class="label label-primary">{{ $history->estimed_time}}</span>
                            </div>
                        @endif
                        <span class="label label-info">{{ $history->created_at}}</span>
                        <div class="time-history-duration">
                            <span class="label label-{{$history->duration_color}}">{{ $history->duration}} <span class="glyphicon {{$history->hand}}">
                            </span></span>
                        </div>
                    </div>
                </div>
            @endfor

            <?php
            $historyArray = $solicitud_history->toArray();
            $adicional = 0;

            $num = $historyArray[count($historyArray) - 1]['orden'];
            $count_flujo = 0;
            $j = count($historyArray) - 1;
            if ($solicitud->idtiposolicitud != SOL_INST) {
                $count_flujo = count($flujo);
                $num = $num == $count_flujo ? $num + 1 : $num;
            }
            ?>

            @if( $solicitud->idtiposolicitud != SOL_INST && $solicitud->state->id_estado != R_NO_AUTORIZADO )
                @for ($i = $num; $i < $count_flujo; $i++)
                    {{ '' ; $fl = $flujo[$i] }}
                    <div class="stage col-md-3 col-sm-3 @if($i == $num) pending @endif">
                        <div class="stage-header @if($i == $num) stage-pending @endif"></div>
                        <div class="stage-content">
                            @if( is_null( $fl->desde ) && is_null( $fl->hasta ) )
                                <h3 class="stage-title">Validaci&oacute;n {{$fl->tipo_usuario}} .</h3>
                            @else
                                <h3 class="stage-title" style="white-space:nowrap">Aprobaci&oacute;n</h3>
                            @endif
                            <span class="label label-info">
                                {{$fl->nombre_usuario}}
                            </span>
                            <div class="time-estimated">
                                <span class="label label-primary ">{{$fl->estimed_time}}</span>
                            </div>
                        </div>
                    </div>
                @endfor
            @endif

            <?php
            $count_history = $j - $count_flujo;
            $num_static = ($count_history < 0) ? 0 : ($count_history);
            ?>
            
            @for($i = $num_static; $i < count($line_static); $i ++)
                {{ ''; $line = $line_static[$i] }}
                <div class="stage col-md-3 col-sm-3 @if($i == $count_history) pending @endif">
                    <div class="stage-header @if($i == $count_history) stage-pending @endif"></div>
                    <div class="stage-content">
                        <h3 class="stage-title">{{ $line[ 'title' ] }}.</h3>
                        <span class="label label-info">{{ $line[ 'info' ] }}</span>
                        @foreach ($time_flow_event as $time_flow)
                            @if ($time_flow->status_id == $line['status_id'] && $time_flow->to_user_type == $line['user_type_id'])
                                <div class="time-estimated">
                                    <span class="label label-primary">{{ $time_flow->hours  }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endfor
        </div>
    </div>
     @include( 'template.Modals.devolution-timeline' ) 
</div>
