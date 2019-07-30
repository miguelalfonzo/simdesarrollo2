<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
    <label class="control-label">Productos</label>
    <ul id="listfamily" style="padding:0">
        @if ( ! isset( $solicitud ) )
            <li>
                <div style="margin-top:5px;position:relative">
                    <select data-placeholder="Seleccione una familia de Productos ..." multiple id="selectfamily" name="productos[]" class="form-control products chosen-select">
                        @foreach( $families as $family )
                            <option value="{{$family['ID']}}">{{$family['DESCRIPCION']}}</option>
                        @endforeach
                    </select>
                
                </div>
            </li>
        @else
            <select data-placeholder="Seleccione una familia de Productos ..." multiple id="selectfamily" name="productos[]" class="form-control products chosen-select"> 
                @foreach( $families as $family )
                    {{--*/$seleccion = ''/*--}}
                    @foreach ($productos as $idPro )
                        @if( $idPro == $family['ID'] )
                            {{--*/$seleccion = 'selected'/*--}}
                            @break
                        @endif
                    @endforeach
                    <option value="{{$family['ID']}}" {{$seleccion}}>{{$family['DESCRIPCION']}}</option>
                @endforeach                      
            </select>    
        @endif
    </ul>
    <span class="col-sm-10 col-md-10 families_repeat" style="margin-bottom: 10px ; margin-top: -10px"></span>
    
</div>
<script>
    $( '#btn-add-family' ).on( 'click' , function() 
    {
        $( '.btn-delete-family' ).show();
        $( '#listfamily>li:first-child' ).clone(true, true).appendTo( '#listfamily' );
    });
    $( document ).off( 'click' , '.btn-delete-family' );
    $( document ).on( 'click', '.btn-delete-family' , function() 
    {
        $( '#listfamily>li .porcentaje_error' ).css( { border: 0 } );
        $( '.option-des-1' ).removeClass( 'error' );
        $( '.families_repeat' ).text( '' );
        var k = $( '#listfamily li' ).size();
        if ( k > 1 )
        {
            var other = $( '.btn-delete-family' ).index( this );
            $( '#listfamily li' ).eq( other ).remove();
            var p = $("#listfamily li").size();
            if( p === 1 )
            {
                $( '.btn-delete-family' ).hide();
            }
        }
    });
    $(document).off( 'click' , '.btn-delete-client' );
    $(document).on( 'click' , '.btn-delete-client' , function () 
    {
        var li = $(this).closest('li');
        var ul = li.parent();
        if ( li.index() === 0 && ul.children().length > 1 )
        {
            var clientType = li.find( 'input[ name="tipos_cliente[]"]' ).val();
            li.remove();
            var old_clientType = ul.children().first().find( 'input[ name="tipos_cliente[]"]' ).val();
            if( clientType !== old_clientType )
            {
                clientFilter( old_clientType , 'eliminacion' );    
            }
        }
        else
        {
            li.remove();
        }
        if ( ul.children().length === 0 )
        {
            fillInvestmentsActivities();
        }
});
</script>