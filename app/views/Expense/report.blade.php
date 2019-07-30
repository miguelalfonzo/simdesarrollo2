<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>>REPORTE DE GASTOS PUBLICIDAD Y PROMOCIÓN SEGÚN PRESUPUESTO</title>
    {{ HTML::style('css/report.css') }}
</head>
<body style="background: url( {{ asset( 'img/logo-marcadeagua.png' ) }} ) no-repeat center fixed">
    <div class="container-fluid">
        <header style="/*margin-top:-2em;*/">
            <h3 style="text-align:center"><strong>REPORTE DE GASTOS PUBLICIDAD Y PROMOCIÓN SEGÚN PRESUPUESTO</strong></h3>
            <img src="{{ URL::to( 'img/logo-report.png' ) }}" style="position: absolute; top: 0; width:170px; padding:0px; margin: 0; margin-left: -30px">
        </header>        
            <section style="text-align:center;margin-top:1em;">
                <table style="width: 100%">
                    <tbody>
                        <tr>
                            <th class="sin-border text-right">Colaborador Bagó:</th>
                            <td class="sin-border text-left">{{ $solicitud->assignedTo->personal->full_name }}</td>  
                            <th class="sin-border text-right">Cargo:</th>
                            <td class="sin-border text-left">{{ ucwords( strtolower( $solicitud->assignedTo->userType->descripcion ) ) }}</td>
                            
                            <th class="sin-border text-right">Ciudad:</th>
                            <td class="sin-border text-left">{{ $zona }}</td>
                            <th class="sin-border text-right">Fecha:</th>
                            <td class="sin-border text-left">{{ \Carbon\Carbon::now()->format( 'Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <th class="sin-border text-right">Autorizado por:</th>
                            <td class="sin-border text-left">{{ $solicitud->approvedHistory->updatedBy->personal->full_name }}</td>
                            <th class="sin-border text-right">Cargo:</th>
                            <td class="sin-border text-left">{{ ucwords( strtolower( $solicitud->approvedHistory->updatedBy->userType->descripcion ) ) }}</td>
                            <th class="sin-border text-right">Fondo:</th>
                            <td class="sin-border text-left">{{mb_convert_case($solicitud->products[0]->thisSubFondo->subCategoria->descripcion ,MB_CASE_TITLE,'UTF-8')}}</td>   
                            <th class="sin-border text-right">Código Comercial:</th>
                            <td class="sin-border text-left">{{$solicitud->id}}</td>   
                        </tr>
                    </tbody>
                </table>
            </section>
            <section style="margin-top:1.5em;">
                <table class="table" style="width:100%;">
                    <tbody>
                        <tr>
                            <th>Fecha</th>
                            <th>Comprobante</th>
                            <th>N° de Comprobante</th>
                            <th>Descripción</th>
                            <th>C.M.P.</th>
                            <th>Especialidad</th>
                            <th>Nombre del Médico o Institución</th>
                            <th>Total</th>
                        </tr>
                        @foreach($expenses as $key => $value)
                            <tr>
                                
                                <td>{{date('d/m/Y',strtotime($value->fecha_movimiento))}}</td>
                                <td>{{mb_convert_case($value->proof->descripcion,MB_CASE_TITLE,'UTF-8')}}</td>
                                <td>{{$value->num_prefijo.'-'.$value->num_serie}}</td>
                                <td>{{mb_convert_case($value->descripcion,MB_CASE_TITLE,'UTF-8')}}</td>
                                @if($key == 0 )
                                <td rowspan="{{ $size }}">{{ $cmps }}</td>
                                <td rowspan="{{ $size }}">{{ $getSpecialty  }}</td>
                                <td rowspan="{{ $size }}">
                                    {{$clientes}}
                                </td>
                                @endif
                                <td>S/.{{ $value->monto }}</td>
                            </tr>    
                        @endforeach
                        <tr class="sin-border">
                            <td class="sin-border" colspan="6" rowspan="5">
                                <table class="tb-firmas" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td class="sin-border" style="height: 100px">&nbsp;</td>
                                            <td class="sin-border" style="height: 100px">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="sin-border" style="width: 50%;">
                                                <table style="margin: 0 auto; min-width: 200px">
                                                    <tbody>
                                                        <tr>
                                                            <td style="border-top: 1px solid;">{{ $solicitud->assignedTo->personal->full_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">DNI: {{$dni}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                            @foreach( $solicitud->toAcceptedApprovedHistories()->orderBy( 'updated_at' , 'ASC' )->get() as $acceptedApprovedHistory )
                                                @if( ! is_null( $solicitud->investment->approvalInstance->approvalPolicyType( $acceptedApprovedHistory->user_from ) ) && 
                                                     ! ( is_null( $solicitud->investment->approvalInstance->approvalPolicyType( $acceptedApprovedHistory->user_from )->desde ) ) )
                                                    <td class="sin-border"  style="width: 50%;">
                                                        <table style="margin: 0 auto; min-width: 200px">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="border-top: 1px solid;">
                                                                        V°B° {{ ucwords( strtolower( $acceptedApprovedHistory->fromUserType->descripcion ) ) }}
                                                                    </td>
                                                                </tr>
                                                                <tr><td>&nbsp;</td></tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </td>

                            <td class="border align-left">
                                <strong>Total Reportado</strong>
                            </td>
                            <td class="border">
                                <strong>
                                    <span class="symbol">S/.</span>
                                    <span class="total-expense">{{$total}}</span>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="border align-left">
                                <strong>Total Depositado</strong>
                            </td>
                            <td class="border">
                                <strong>
                                    <span class="symbol">S/.</span>
                                    <span class="total-expense">
                                        @if ( is_null( $solicitud->detalle->deposit ) )
                                            -
                                        @else
                                            @if ( $solicitud->detalle->deposit->account->idtipomoneda == DOLARES )
                                                {{ round ( $solicitud->detalle->deposit->total * $detalle->tcv , 2 , PHP_ROUND_HALF_DOWN ) }}
                                            @else
                                                {{ $solicitud->detalle->deposit->total }}    
                                            @endif
                                        @endif
                                    </span>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="border align-left">
                                <strong>Aplicación Parcial del depósito</strong>
                            </td>
                            <td class="border"><strong>-</strong></td>
                        </tr>
                        <tr>
                            <td class="border align-left">
                                <strong>Saldo a favor Compañía</strong>
                            </td>
                            <td class="border">
                                <strong>
                                    <span class="symbol">S/.</span>
                                    <span class="total-expense">{{ $balance[ 'bussiness' ] }}</span>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="border align-left">
                                <strong>Saldo a favor del Empleado</strong>
                            </td>
                            <td class="border">
                                <strong>
                                    <span class="symbol">S/.</span>
                                    <span class="total-expense">{{ $balance[ 'employed' ] }}</span>
                                </strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
        <div class="clearfix"></div>        
            <div class="clearfix"></div>
        </section>
    </div>
</body>
</html>
