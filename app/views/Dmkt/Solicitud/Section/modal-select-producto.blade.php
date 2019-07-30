<div class="modal fade" id="approval-product-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Productos xxx</h4>
            </div>
            <div class="modal-body">
                <select id="selectfamilyadd"  class="form-control products">
                    @if ( isset( $families ) )
                        @foreach( $families as $family )
                            <option value="{{$family['ID']}}">{{$family['DESCRIPCION']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btn-add-family-fondo" class="btn btn-primary ladda-button" data-style="zoom-in">Confirmar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->