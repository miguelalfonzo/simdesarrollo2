@if( $solicitud->devolutions->count() !== 0 )
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                Devoluciones
                <label class="pull-right">Tipo de Devolución</label>
            </div>
            <ul class="list-group">                 
                @foreach( $solicitud->devolutions()->orderBy( 'updated_at' , 'ASC' )->get() as $devolution )
                    <li class="list-group-item">
                        <label class="label label-primary">{{ $devolution->updated_at }}</label> 
                        | <label class="label label-primary">{{ $devolution->state->descripcion }}</label>
                        | <label class="label label-primary">{{ $solicitud->detalle->typeMoney->simbolo . $devolution->monto }}</label>    
                        <span class="badge">{{ $devolution->type->descripcion}}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif