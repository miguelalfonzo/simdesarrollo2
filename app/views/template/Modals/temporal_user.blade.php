<!-- if( Auth::user()->type == SUP ) -->
    @if ( is_null( Auth::user()->assignTempUser ) )
        <div class="modal fade" id="modal-temporal-user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title">Asignación de Usuario Temporal</h4>
                    </div>
                    <div class="modal-body">
                        <label>Ingresar usuario</label>
                        <input id="user-seeker" class="form-control input-md" type="text">
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button id="button-confirm-temporal-user" class="btn btn-success" type="button">Confirmar</button>
                        <button id="edit-user" type="button" class="btn btn-info">Editar</button>    
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="modal fade" id="modal-temporal-user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title">Asignación de Usuario Temporal</h4>
                    </div>
                    <div class="modal-body">
                        <label class="green">Usuario Asignado</label>
                        <h3 class="green">
                            {{ Auth::user()->assignTempUser->user->personal->full_name }}    
                        </h3>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button id="button-remove-temporal-user" type="button" class="btn btn-danger">Eliminar</button>    
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
<!-- endif -->