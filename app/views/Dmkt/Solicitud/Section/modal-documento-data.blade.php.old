<div class="modal fade" id="expense-register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Registro de Documentos</h4>
      </div>
      <div class="modal-body">
       <section class="row reg-expense">
			<input type="hidden" name="idgasto">
			<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
				<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Tipo de Comprobante</label>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<select id="proof-type" class="form-control">
						@foreach($typeProof as $val)
							<option value="{{$val->id}}" igv="{{$val->igv}}" marca="{{$val->marca}}">{{$val->descripcion}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
				<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">RUC</label>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="input-group">
						<input id="ruc" type="text" class="form-control" maxlength="11">
						<div class="input-group-addon search-ruc" data-sol="1">
							<span class="glyphicon glyphicon-search" style="font-size:1.0em"></span>
						</div>
						<input id="ruc-hide" type="hidden">
					</div>
				</div>
			</div>

			<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
				<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Razón Social</label>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="razon-val">
					<button id="razon" type="button" class="form-control ladda-button" data-style="expand-left" data-spinner-color="#5c5c5c" value=0 data-edit=0 readonly>
				</div>
				<div class="input-group" id="manual-razon" style="display: none;">
			      <input type="text" class="form-control" id="manual-razon-val" placeholder="Ingrese Razon Social">
			      <span class="input-group-btn">
			        <button class="btn btn-default add-manual-razon" type="button">Aceptar</button>
			      </span>
			    </div>
			</div>

			<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
				<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Número de Comprobante</label>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="input-group">
						<input id="number-prefix" type="text" class="form-control" maxlength="4">
						<div class="input-group-addon">-</div>
				      	<input id="number-serie" class="form-control" type="text" maxlength="12">
					</div>
				</div>
			</div>

			<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
				<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Descripción del Gasto</label>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<input id="desc-expense" type="text" class="form-control">
				</div>
			</div>

			<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
				<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Fecha del Documento</label>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="input-group date">
						<span class="input-group-addon">
							<i class="glyphicon glyphicon-calendar"></i>
						</span>
						<input id="date" type="text" class="form-control" maxlength="10" readonly>
					</div>
				</div>
			</div>

			<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
				<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Balance</label>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">	
					<div class="input-group">
				    	<div class="input-group-addon">{{$solicitud->detalle->typemoney->simbolo}}</div>
				    	@if ( isset( $balance) )
				    		<input id="balance" class="form-control" type="text" value="{{ $balance }}" disabled>
				    	@else
				      		<input id="balance" class="form-control" type="text" value="{{ $detalle->monto_aprobado}}" disabled>
				    	@endif
				    </div>
				</div>
			</div>

		</section>

		<section class="row reg-expense detail-expense" style="margin:0">
			<div style="padding:0 15px">
				<div class="panel panel-info">
					<div class="panel-heading">
						<span class="text-left">Detalle del Comprobante</span>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table id="table-items" class="table table-bordered">
								<thead>
									<tr>
										<th class="w-quantity">Cantidad</th>
										<th class="w-desc-item">Descripción</th>
										<th class="w-type-expense">Tipo de Gasto</th>
										<th class="w-total-item">Valor de Venta {{$solicitud->detalle->typemoney->simbolo}}</th>
										<th>Eliminar</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th class="quantity"><input type="text" class="form-control" maxlength="4"></th>
										<th class="description"><input type="text" class="form-control"></th>
										<th>
											<select class="form-control type-expense">
												@foreach($typeExpense as $val)
													<option value="{{$val['ID']}}">{{$val['DESCRIPCION']}}</option>
												@endforeach
											</select>
										</th>
										<th class="total-item">
											<div class="input-group">
										      	<input class="form-control" type="text" maxlength="8">
										    </div>
										</th>
										<th>
											<a class="delete-item" href="#">
												<span class="glyphicon glyphicon-remove"></span>
											</a>
										</th>
									</tr>
								</tbody>
							</table>
							@if ( Auth::user()->type == CONT )
								<aside class="col-xs-12 col-sm-6 col-md-4" style="padding:0;">
									<button id="add-item" type="button" class="btn btn-default">Agregar Item</button>
								</aside>
							@endif
						</div>
					</div>
				</div>
			</div>
		</section>
		<section class="row reg-expense detail-expense" style="margin:0">
			<div class="col-xs-12 col-sm-6 col-md-4 tot-document">
				<div class="form-expense">
					<label>Sub Total</label>
					<div class="input-group">
				    	<div class="input-group-addon">{{$solicitud->detalle->typemoney->simbolo}}</div>
				      	<input id="sub-tot" class="form-control" type="text" value=0>
				    </div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 tot-document">
				<div class="form-expense">
					<label>Impuesto por Servicio</label>
					<div class="input-group">
				    	<div class="input-group-addon">{{$solicitud->detalle->typemoney->simbolo}}</div>
				      	<input id="imp-ser" class="form-control" type="text" value=0>
				    </div>
				</div>
			</div>
			
			<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 tot-document">
				<div class="form-expense">
					<label>IGV</label>
					<div class="input-group">
				    	<div class="input-group-addon">{{$solicitud->detalle->typemoney->simbolo}}</div>
				      	<input id="igv" class="form-control" type="text" igv="{{$igv->numero}}">
				    </div>
				</div>
			</div>

			<div class="col-xs-12 col-sm-6 col-md-4">
				<div class="form-expense">
					<label>Monto Total</label>
					<div class="input-group">
				    	<div class="input-group-addon">{{$solicitud->detalle->typemoney->simbolo}}</div>
				      	<input id="total-expense" class="form-control" type="text">
				    </div>
				</div>
			</div>

			@if ( Auth::user()->type == CONT )
				
				<!-- Retencion o Detraccion -->
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
					<div class="form-expense">
						<label>Retención o Detracción</label>
						<select id="regimen" class="form-control">
	                    	<option value=0 selected>NO APLICA</option>	
	                    	@foreach( $regimenes as $regimen )
		                        <option value="{{$regimen['ID']}}">{{$regimen['DESCRIPCION']}}</option>                          
	                    	@endforeach
	                	</select>
					</div>
				</div>

				<!-- Monto de la Retencion -->
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 form-group" style="display:none">
					<label>Monto de la Retención o Detracción</label>
					<input id="monto-regimen" type="text" class="form-control">
				</div>

				<!-- REPARO -->
				<div id="dreparo" class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
					<div class="form-expense">
						<label>Reparo</label>
						<div class="input-group">
							<div class="btn-group" role="group">
								<label class="btn btn-default">
							 		<input value="1" type="radio" name="reparo" style="margin-top:.5em;">Si
								</label>
								<label class="btn btn-default">
									<input value="0" type="radio" name="reparo" style="margin-top:.5em;" checked>No
								</label> 
							</div>
					    </div>
					</div>
				</div>
			@endif
		</section>

		<section class="row reg-expense detail-expense" style="margin:0">
			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="form-expense">
					
					<div class="inline"><p class="inline message-expense"></p></div>
				</div>
			</div>
		</section>

		
      </div>
      <div class="modal-footer">
        <button id="save-expense" type="button" class="btn btn-primary">Registrar</button>
		<button id="cancel-expense" type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
@include( 'Script.Cont.retencion_detraccion' )