@if( ( $solicitud->id_user_assign == Auth::user()->id  && ! is_null( $solicitud->expenseHistory ) ) || ( Auth::user()->type == CONT && ! is_null( $solicitud->toDeliveredHistory ) ) )
	@if( ( $solicitud->id_user_assign == Auth::user()->id &&  in_array( $solicitud->id_estado , [ GASTO_HABILITADO , ENTREGADO ] ) ) || ( Auth::user()->type == CONT && $solicitud->id_estado == ENTREGADO ) )
		<div class="col-sm-12 col-md-12 col-lg-12" style="text-align: center">  
			<button type="button" class="btn btn-default" data-toggle="modal"  id="open-expense-register" data-target="#expense-register">
				Registar Documento
			</button>
			@if ( Auth::user()->id == $solicitud->id_user_assign )
				<button type="button" class="btn btn-default" data-toggle="modal"  id="open-event-section" data-target="#event-section">
					Evento
				</button>
			@endif
		</div>
		@include('Dmkt.Solicitud.Section.modal-documento-data')
		@if ( Auth::user()->id == $solicitud->id_user_assign )
			@include('Dmkt.Solicitud.Section.modal-evento-data')
		@endif
	@endif	
	<section class="row reg-expense" style="margin:0">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="form-expense">
				<div class="table-responsive" id="section-table-expense">
					@include('Dmkt.Solicitud.Section.gasto-table')
				</div>
				<input id="tot-edit-hidden" type="hidden">
			</div>
		</div>
	</section>
@endif

