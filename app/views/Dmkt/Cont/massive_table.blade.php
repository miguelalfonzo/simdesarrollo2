<table class="massive-revision-table table table-striped table-hover table-bordered table-condensed" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>N°</th>
            <th># Solicitud</th>
            <th><input type="checkbox" class="thead-checkbox"></th>
        </tr>
    </thead>
    <tbody>
        @foreach( $solicituds as $key => $solicitud )
            <tr>
                <td><b>{{ $key + 1 }}</b></td>
                <td><b>{{ $solicitud['ID'] }}</b></td>
                <td class="text-center"><input type="checkbox"></td>
                <input type="hidden" class="revision-solicitud-token" value="{{ $solicitud['TOKEN']}}"> 
            </tr>
        @endforeach
    </tbody>
</table>