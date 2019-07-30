<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>Solicitud</th>
                    <th>N. Operacion</th>
                    <th>Estado</th>
                    <th>Observacion</th>
                </tr>
            </thead>
            <tbody>
                @if( isset( $oldResponses ) )
                    @foreach( $oldResponses as $oldResponse )
                        <tr>
                            @if( $oldResponse->estado === ok )
                                <td style="background-color:#00dd0d"></td>
                            @elseif( $oldResponse->estado === warning )
                                <td  style="background-color:#f5f904"></td>
                            @else
                                <td style="background-color:#f90404"></td>
                            @endif
                            <td>{{ $oldResponse->solicitud }}</td>
                            <td>{{ $oldResponse->n_operacion }}</td>
                            <td>{{ $oldResponse->estado }}</td>
                            <td>{{ $oldResponse->observacion }}</td>
                        </tr>    
                    @endforeach
                @endif
                @if( isset( $responses ) )
                    @foreach( $responses as $idSolicitud => $response )
                        <tr>
                            @if( $response[ status ] === ok )
                                <td style="background-color:#00dd0d"></td>
                            @elseif( $response[ status ] === warning )
                                <td  style="background-color:#f5f904"></td>
                            @else
                                <td style="background-color:#f90404"></td>
                            @endif
                            <td>{{ $idSolicitud }}</td>
                            <td>{{ $response[ 'operacion' ] }}</td>
                            <td>{{ $response[ status ] }}</td>
                            <td>{{{ $response[ description ] or '' }}}</td>
                        </tr>       
                    @endforeach
                @endif
            </tbody>
        </table>
    </body>
</html>