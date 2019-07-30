<div class="container" id="leyenda" style="display: none">
    <table style=  "border-collapse: separate;border-spacing: 5px" >
        <tbody>
            @foreach($states as $state)
                <tr>
                    <td>
                        <div class="" style='background-color: {{$state->color}} ; border-radius: 5px; text-align: center ;width: 120px'>
                            <span style="color: #ffffff">{{$state->nombre}}</span>
                        </div>
                    </td>
                    <td>
                        <span style="text-indent:50px;">{{$state->descripcion}}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>