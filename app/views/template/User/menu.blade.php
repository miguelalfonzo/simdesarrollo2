<div>   
    {{Form::token()}}
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <select id="idState" name="idstate" class="form-control selectestatesolicitude">
            @foreach( $states as $estado )
                @if ( Auth::user()->type == TESORERIA )
                    @if( in_array( $estado['ID'] , array( R_REVISADO , R_GASTO ) ) )
                        @if( isset($state) && $state == $estado['ID'] )
                            <option value="{{$estado['ID']}}" selected>{{$estado['NOMBRE']}}</option>
                        @else
                            <option value="{{$estado['ID']}}">{{$estado['NOMBRE']}}</option>
                        @endif
                    @endif
                @elseif ( Auth::user()->type == CONT )
                    @if( in_array( $estado['ID'] , array( R_APROBADO , R_REVISADO , R_GASTO , R_FINALIZADO , R_NO_AUTORIZADO ) ) )
                        @if( isset( $state ) && $state == $estado['ID'] )
                            <option value="{{$estado['ID']}}" selected>{{$estado['NOMBRE']}}</option>
                        @else
                            <option value="{{$estado['ID']}}">{{$estado['NOMBRE']}}</option>
                        @endif
                    @endif
                @else
                    @if( isset( $state ) && $state == $estado['ID'] )
                        <option value="{{$estado['ID']}}" selected>{{$estado['NOMBRE']}}</option>
                    @else
                        <option value="{{$estado['ID']}}">{{$estado['NOMBRE']}}</option>
                    @endif
                @endif
            @endforeach
        </select>
    </div>
    @if ( in_array( Auth::user()->type , array( GER_COM , CONT , TESORERIA ) ) )
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            @if( Auth::user()->type == TESORERIA )
                <a class="btn btn-primary" data-toggle="modal" data-target="#massive-deposit-modal">
                    Deposito Masivo
                </a>
            @elseif( Auth::user()->type == GER_COM )
                <a id="btn-mass-approve" class="btn btn-primary">
                    Aprobación
                </a>
            @else
                <a class="btn btn-primary" data-toggle="modal" data-target="#massive-revision-modal">
                    Revision Masiva
                </a>
            @endif
        </div>
    @endif
    @if( Auth::user()->type == TESORERIA )
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <h4 style="margin:0px">
                Tasa de Cambio
                @foreach($tc as $tcs)
                <span class="label label-info">
                    {{ $tcs['FECHA'] }}
                </span>
                Compra
                <span class="label label-info">
                    {{ $tcs['COMPRA'] }}
                </span>
                Venta
                <span class="label label-info">
                    {{$tcs['VENTA']}}
                </span>
                @endforeach
            </h4>
        </div>
    @endif
    <div class="container-fluid">
        <table id="table_solicituds" class="table table-striped table-hover table-bordered" cellspacing="0" cellpading="0" border="0" style="width:100%"></table>
    </div>
</div>