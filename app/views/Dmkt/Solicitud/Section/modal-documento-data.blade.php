<div class="modal fade" id="expense-register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
  <div id="modalDialogGastos" class="modal-dialog" role="document" style="width: 98% !important;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="">Registro de Documentos</h4>
      </div>
      <div class="modal-body">
       <section class="row reg-expense">
			<input type="hidden" name="idgasto" id="idgasto">

				<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
					<div class="row">
						<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
							<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Tipo de Comprobante</label>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<select id="proof-type" class="form-control">
									@foreach($typeProof as $val)
										<option value="{{$val->id}}" igv={{$val->igv}} marca="{{$val->marca}}">{{$val->descripcion}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
							<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">RUC</label>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<div class="input-group" id="DivRuc">
									<input id="ruc" type="text" class="form-control" maxlength="11">
									<div class="input-group-addon search-ruc" data-sol="1">
										<span class="glyphicon glyphicon-search" style="font-size:1.0em"></span>
									</div>
									<input id="ruc-hide" type="hidden">
								</div>
							</div>
						</div>

						<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4" data-html="true">
							<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Razón Social</label>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="razon-val">
								<button id="razon" type="button" class="form-control ladda-button" data-style="expand-left" data-spinner-color="#5c5c5c" value=0 data-edit=0 readonly></button>
							</div>
							<div class="input-group" id="manual-razon" style="display: none;">
						      <input type="text" class="form-control" id="manual-razon-val" placeholder="Ingrese Razon Social">
						      <span class="input-group-btn">
						        <button class="btn btn-default add-manual-razon" type="button">Aceptar</button>
						      </span>
						    </div>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4" style="margin-top: -1em;">
							<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Número de Comprobante</label>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<div class="input-group">
									<input id="number-prefix" type="text" class="form-control" maxlength="4">
									<div class="input-group-addon">-</div>
							      	<input id="number-serie" class="form-control" type="text" maxlength="12">
								</div>
							</div>
						</div>

						<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4" style="margin-top: -1em;">
							<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Descripción del Gasto</label>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<input id="desc-expense" type="text" class="form-control">
							</div>
						</div>

						<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4" style="margin-top: -1em;">
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
					</div>
					<div class="row">
						<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4" style="margin-top: -1em;">
							<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">
								Balance
							</label>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">	
								<div class="input-group">
							    	<div class="input-group-addon">
							    		{{$solicitud->detalle->typemoney->simbolo}}
							    	</div>
							    	@if ( isset( $balance) )
							    		<input id="balance" class="form-control" type="text" value="{{ $balance }}" disabled>
							    	@else
							      		<input id="balance" class="form-control" type="text" value="{{ $detalle->monto_aprobado}}" disabled>
							    	@endif
							    </div>
							</div>
						</div>
						<div class="form-group col-xs-2 col-sm-2 col-md-2 col-lg-2" id="titleSincronizado" style="margin-top: -1em;">
							<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Registrado</label>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">	
								<div id="StatusSincronizado" class="form-group col-xs-1 col-sm-1 col-md-1 col-lg-1">
									<img id="imgSincronizado" alt="Imagen Sincronizado" src="" width="35" height="35" data-toggle="tooltip" title="">
								</div>
							</div>
						</div>
						
						<input id="tipoUser" class="form-control" type="text" value="{{Auth::user()->type}}" style="display: none;">
						
						@if ( Auth::user()->type == 'R' )
							<div class="form-group col-xs-2 col-sm-2 col-md-2 col-lg-2 center" id="titleVbSup" data-html="true" style="margin-top: -1em;">
								<label>V.B. SUP.</label>
								<div id="divVbSup">
									
								</div>
							</div>
							<div class="form-group col-xs-2 col-sm-2 col-md-2 col-lg-2 center" id="titleVbDoc" data-html="true" style="margin-top: -1em;">
								<label class="col-xs-12 col-sm-12 col-md-6 col-lg-12 control-label">V.B. DOC.</label>
								<div  id="divVbDoc">
									
								</div>
							</div>				
							<div class="form-group col-xs-2 col-sm-2 col-md-2 col-lg-2 center" id="titleDocRec" data-html="true" style="margin-top: -1em;">
								<label class="col-xs-12 col-sm-12 col-md-6 col-lg-12 control-label">DOC. REC.</label>
								<div  id="divVbDocRec">
									
								</div>
							</div>
						@else
						
							<div class="form-group col-xs-2 col-sm-2 col-md-2 col-lg-2 center" id="titleVbSup" data-html="true" style="margin-top: -1em;">
								<label class="col-xs-12 col-sm-12 col-md-6 col-lg-12 control-label">V.B. SUP.</label>
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label" style="margin-top: -1em;">
									<label class="switch">
										<input type="checkbox" id="idVbSup" name="idVbSup" data-toggle="tooltip">
										<span class="slider" id="checkbox1"></span>
									</label>
								</div>
							</div>

							<div class="form-group col-xs-2 col-sm-2 col-md-2 col-lg-2 center" id="titleVbDoc" data-html="true" style="margin-top: -1em;">
								<label class="col-xs-12 col-sm-12 col-md-6 col-lg-12 control-label">V.B. DOC.</label>
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label" style="margin-top: -1em;">
									<label class="switch">
										<input type="checkbox" id="idVbDoc" name="idVbDoc" data-toggle="tooltip">
										<span class="slider" id="checkbox2"></span>
									</label>
								</div>
							</div>
				
							<div class="form-group col-xs-2 col-sm-2 col-md-2 col-lg-2 center" id="titleDocRec" data-html="true" style="margin-top: -1em;">
								<label class="col-xs-12 col-sm-12 col-md-6 col-lg-12 control-label">DOC. REC.</label>
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label" style="margin-top: -1em;">
									<label class="switch">
										<input type="checkbox" id="idDocRec" name="idDocRec" data-toggle="tooltip">
										<span class="slider" id="checkbox3"></span>
									</label>
								</div>
							</div>
						@endif
					</div>



					<section class="row reg-expense detail-expense" style="margin:0">
						<div class="col-xs-12 col-sm-12 col-md-12" style="margin-top: -2em;margin-left: -1em;">
							<div style="padding:0 15px">
								<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Detalle del Gasto</label>
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
												<th class="quantity">
													<input type="text" class="form-control" maxlength="4">
												</th>
												<th class="description">
													<input type="text" class="form-control">
												</th>
												<th>
													<select class="chosen-select form-control type-expense">
														@foreach($typeExpense as $val)
														<option style="text-align: left;" value="{{$val['ID']}}">{{$val['DESCRIPCION']}}</option>
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
														<i style="font-size: 18px;" class="text-danger far fa-trash-alt"></i>
													</a>
												</th>
											</tr>
										</tbody>
									</table>
									@if ( Auth::user()->type == CONT )
									<aside class="col-xs-12 col-sm-6 col-md-4" style="padding:0;margin-top: -1em">
										<button id="add-item" type="button" class="btn btn-default">Agregar Item</button>
									</aside>
									@endif
								</div>
								
								
							</div>
						</div>
					</section>

					<section class="row reg-expense detail-expense" style="margin:0">
						<div class="col-xs-12 col-sm-12 col-md-12">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-3 tot-document" style="margin-top: -0.5em;">
									<div class="form-expense">
										<label>Sub Total</label>
										<div class="input-group">
									    	<div class="input-group-addon">{{$solicitud->detalle->typemoney->simbolo}}</div>
									      	<input id="sub-tot" class="form-control" type="text" value=0>
									    </div>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 tot-document" style="margin-top: -1.75em;">
									<div class="form-expense">
										<label>Impuesto por Servicio</label>
										<div class="input-group">
									    	<div class="input-group-addon">{{$solicitud->detalle->typemoney->simbolo}}</div>
									      	<input id="imp-ser" class="form-control" type="text" value=0>
									    </div>
									</div>
								</div>
								
								<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 tot-document" style="margin-top: -1.75em;">
									<div class="form-expense">
										<label>IGV</label>
										<div class="input-group">
									    	<div class="input-group-addon">{{$solicitud->detalle->typemoney->simbolo}}</div>
									      	<input id="igv" class="form-control" type="text" igv="{{$igv->numero}}">
									    </div>
									</div>
								</div>

								<div class="col-xs-12 col-sm-6 col-md-3" style="margin-top: -1.75em;">
									<div class="form-expense">
										<label>Monto Total</label>
										<div class="input-group">
									    	<div class="input-group-addon">{{$solicitud->detalle->typemoney->simbolo}}</div>
									      	<input id="total-expense" class="form-control" type="text">
									    </div>
									</div>
								</div>
							</div>

							@if ( Auth::user()->type == CONT )
								
								<!-- Retencion o Detraccion -->
								<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
									<div class="form-expense">
										<label>Retención o Detracción</label>
										<select id="regimen" class="chosen-select form-control">
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
								<div id="dreparo" class="col-xs-6 col-sm-3 col-md-2 col-lg-3">
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
						</div>
					</section>


				</div>
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
					<center>
						<!-- <form enctype="multipart/form-data" id="FormImagenes" >
							<div class="row center">
								<span class="btn btn-info btn-file">
									Subir Imagenes 
									<input name="file-0a[]" id="subirImagenes" multiple="true" type="file" accept="image/jpg, image/jpeg, image/png"> 
								</span>
							</div>
						</form>
						<br> -->


						<span id="esperaImagenes"> <i class="text-danger fa-3x fas fa-spinner fa-spin"></i> Buscando imagenes del gastos... </span>

						<div class="row">
							<div class="col-xs-12 col-md-12">
							    <a href="#" class="thumbnail" id="divImagenPrincipal">
							    	<img id="imagenPrincipal" alt="Imagen Principal" src="{{ asset('img/sin-imagen.jpg') }}" style="width: 100%;height: 350px;" data-zoom-image="large/image1.jpg" class="imageZoom">
							    </a>   
							</div>
						</div>
						<div class="row">
							<div class="col-xs-6 col-md-3">
							    <a href="#" class="thumbnail" id="divImagen1" style="display: none;">
							    	<div>
							    		<img id="imagen1" alt="Imagen 1" src="{{ asset('uploads/1.jpg') }}" width="35" height="50" data-zoom-image="large/image1.jpg" class="imageZoom">
							    	</div>
							    </a>   
							</div>
							<div class="col-xs-6 col-md-3">
								<a href="#" class="thumbnail" id="divImagen2" style="display: none;">
							      <div>
							    		<img id="imagen2" alt="Imagen 1" src="{{ asset('uploads/1.jpg') }}" width="35" height="50" data-zoom-image="{{ asset('uploads/1.jpg') }}" class="imageZoom">
							    	</div>
							    </a>
							</div>
							<div class="col-xs-6 col-md-3">
								<a href="#" class="thumbnail" id="divImagen3" style="display: none;">
							     <div>
							    		<img id="imagen3" alt="Imagen 1" src="{{ asset('uploads/1.jpg') }}" width="35" height="50" data-zoom-image="{{ asset('uploads/1.jpg') }}" class="imageZoom">
							    	</div>
							    </a>
							</div>
							<div class="col-xs-6 col-md-3">
								<a href="#" class="thumbnail" id="divImagen4" style="display: none;">
							      <div>
							    		<img id="imagen4" alt="Imagen 1" src="{{ asset('uploads/1.jpg') }}" width="35" height="50" data-zoom-image="{{ asset('uploads/1.jpg') }}" class="imageZoom" >
							    	</div>
							    </a>
							</div>
						</div>
					</div>
				</center>

				</section>
				<section class="row reg-expense detail-expense" style="margin:0">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-expense">
							
							<div class="inline"><p class="inline message-expense"></p></div>
						</div>
					</div>
				</section>
				<div style="float: right;">
					<button id="save-expense" type="button" class="btn btn-primary">Registrar</button>
					<button id="cancel-expense" type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				</div>
				<br>	
		
      </div>
      
    </div>
  </div>
</div>
@include( 'Script.Cont.retencion_detraccion' )