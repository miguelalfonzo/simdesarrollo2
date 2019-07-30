<dl class="dl-horizontal">
    @foreach($states as $state)
        <dt><span class="label" style="background-color:{{$state['COLOR']}};">{{$state['NOMBRE']}}</span></dt>
        <dd>{{$state['DESCRIPCION']}}</dd>
    @endforeach
</dl>