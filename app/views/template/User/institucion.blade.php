@extends('template.main')
@section('solicitude')
    <div class="panel panel-default " style="margin-top: 10px">
        <div class="panel-heading">
            <h3 class="panel-title" style="height: 15px">Registro</h3>
            <small style="float: right; margin-top: -15px">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" class="icon-collapse">
                    <span class="glyphicon glyphicon-chevron-down"></span>
                </a>
            </small>
        </div>
        <div id="collapseOne" class="panel-collapse collapse in">
   			<div class="panel-body">
                <div>
                    <input id="idsolicitud" name="idsolicitud" type="hidden" autocomplete="off">
                    <!-- PERIODO -->
                    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
                        <label class="control-label">Mes a Registrar</label>
                        <div>
                            <div class="input-group">
                                <span class="input-group-addon" >
                                    <i class="glyphicon glyphicon-calendar"></i>
                                </span>
                                <input type="text" class="form-control date_month" data-type="fondos" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Fondos -->
                    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
                        <label class="control-label">Fondo</label>
                        <select name="fondo_producto[]" class="selectpicker form-control">
                            <option selected disabled value="0">Seleccione el Fondo</option>
                            @foreach( $subFondos as $subFondo )
                                <option value="{{ $subFondo->id }}">{{ $subFondo->detail_name . ' S/.' . $subFondo->saldo_disponible  }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- INVERSION -->
                    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
                        <label class="control-label">Inversion</label>
                        <select id="fondo-inversion" class="selectpicker form-control">
                            <option selected disabled value="0">Seleccione la Inversion</option>
                            @foreach( $investments as $investment )
                                <option value="{{ $investment->id }}">{{ $investment->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    

                    <div class="clearfix"></div>

                    <!-- SISOL -->
                    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
                        <label class="control-label">SISOL - Hospital</label>
                        <div class="scrollable-dropdown-menu">  
                            <input id="fondo_institucion" class="form-control input-md institucion-seeker" name="institucion" type="text" style="display:inline">
                            <a id="edit-institucion" class="edit-repr  glyphicon glyphicon-pencil" href="#" style="display:none;padding-top:5px">
                            </a>
                        </div>
                    </div>

                    <!-- REPRESENTANTE -->
                    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
                        <label class="control-label">Depositar a</label>
                        <div>
                            <input id="fondo_repmed" name="repmed" type="text" style="display:inline"
                            data-select="false" class="form-control input-md rep-seeker">
                            <a id="edit-rep" class="edit-repr glyphicon glyphicon-pencil" href="#" style="display:none;padding-top:5px">
                            </a>
                        </div>
                    </div>

                    <!-- MONTO -->
                    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
                        <label class="control-label">Total a depositar</label>
                        <div>    
                            <input id="fondo_total" name="total" type="text" class="form-control input-md">
                        </div>
                    </div>
                  
                   <!-- Button (Double) -->
                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: 20px">
                        <div>
                            <button class="btn btn-primary register_fondo ladda-button" data-style="zoom-in" data-size="l">
                                Registrar
                            </button>
                            <button class="btn btn-primary btn_edit_fondo ladda-button" data-style="zoom-in" data-size="l" style="display:none">
                                Actualizar
                            </button>
                            <button class="btn btn-primary btn_cancel_fondo ladda-button" data-style="zoom-in" data-size="l" style="display:none">
                                Cancelar
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Fondos Institucionales</h3>
        </div>
        <div class="panel-body table-solicituds-fondos" style="position: relative"></div>
    </div>
@stop