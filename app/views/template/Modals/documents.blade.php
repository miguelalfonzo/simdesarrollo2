<div class="modal fade" id="documents_Modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">Edición del Documento</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="idDocumento">
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 form-group">
                        <label>Sub-Total</label>
                        <input id="subtotal" class="form-control" type="text" disabled>    
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 form-group">
                        <label>IGV</label>
                        <input id="igv" class="form-control" type="text" disabled>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 form-group">
                        <label>Impuesto de Servicio</label>
                        <input id="imp-serv" class="form-control" type="text" disabled>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 form-group">
                        <label>Total</label>
                        <input id="total" class="form-control" type="text" disabled>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 form-group">
                        <label>Reparo</label>
                        <input id="reparo" class="form-control" type="text" disabled>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 form-group">
                        <label>Retención o Detracción</label>
                        <select id="regimen" class="form-control">
                            <option value=0 selected>NO APLICA</option> 
                            @foreach( $regimenes as $regimen )
                                <option value="{{$regimen['ID']}}">{{$regimen['DESCRIPCION']}}</option>                          
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 form-group" style="display:none">
                        <label>Monto de la Retención o Detracción</label>
                        <input id="monto-regimen" type="text" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="update-document" class="btn btn-success" style="margin-right: 1em;">Actualizar</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
@include( 'Script.Cont.retencion_detraccion' )
