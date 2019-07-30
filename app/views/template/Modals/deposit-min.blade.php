@if( Auth::user()->type == TESORERIA && $solicitud->id_estado == DEPOSITO_HABILITADO )
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Registro del Depósito</h4>
                </div>
                <div class="modal-body">
                    <label>Bancos</label>
                    <select id="bank_account" name="bank_account" class="form-control">
                        @foreach ( $banks as $bank )
                            <option value="{{$bank->num_cuenta}}">
                                {{$bank->typeMoney->simbolo.'-'.$bank->bagoAccount->ctanombrecta}}
                            </option>
                        @endforeach
                    </select>
                    <br>
                    <label for="op-number">Número de Operación, Transacción, Cheque:</label>
                    <input id="op-number" type="text" class="form-control" maxlength="200">
                    <p id="message-op-number" style="margin-top:1em;color:#a94442;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success register-deposit" style="margin-right: 1em;">Confirmar Operación</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
@endif