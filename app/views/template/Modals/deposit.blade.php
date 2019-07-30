<div class="modal fade" id="enable_deposit_Modal" tabindex="-1" role="dialog" aria-labelledby="enable_deposit_ModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="enable_deposit_ModalLabel">Registro del Depósito</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6 form-group">
                        <label>Solicitud</label>
                        <div class="input-group">
                            <div id="id-solicitude" class="input-group-addon" value=""></div>
                            <input id="sol-titulo" class="form-control" type="text" disabled>
                            <input name="token" type="hidden">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 form-group">
                        <label>Beneficiario</label>
                        <input id="beneficiario" class="form-control" type="text" disabled>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 form-group">
                        <label>Monto a Depositar</label>
                        <input id="total-deposit" class="form-control" type="text" disabled>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 form-group">
                        <label>Bancos</label>
                        <select id="bank_account" name="bank_account" class="form-control">
                            @foreach ( $banks as $bank )
                                <option value="{{$bank['NUM_CUENTA']}}">
                                    {{ $bank['SIMBOLO'] . '-' . $bank['CTANOMBRECTA']}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 form-group">
                        <label for="op-number">Número de Operación, Transacción, Cheque</label>
                        <input id="op-number" type="text" class="form-control" maxlength="200">
                        <p id="message-op-number" style="margin-top:1em;color:#a94442;"></p> 
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success register-deposit" style="margin-right: 1em;">Confirmar Operación</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>