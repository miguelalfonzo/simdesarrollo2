@if( $devolutions->count() !== 0 )
	<div class="container-fluid hide">
		<h6 class="text-center">DEVOLUCION</h6>
		<div class="stage-container">
			@foreach( $devolutions as $devolution )
				@foreach( $devolution->histories as $devolutionHistory )
					<div class="stage col-md-3 col-sm-3 success">
						<div class="stage-header stage-success"></div>
						<div class="stage-content">
							<h3 class="stage-title">
								@if( $devolutionHistory->status_to == 1 )
									Inicio por Rev. de Documentos
								@elseif( $devolutionHistory->status_to == 2 )
									Pago
								@elseif( $devolutionHistory->status_to == 3 )
									Confirmacion
								@endif
							</h3>
							<span class="label label-info">
								{{ strtoupper( $devolutionHistory->updatedBy->personal->full_name ) }}
							</span>
							<span class="label label-info">{{ $devolutionHistory->updated_at }}</span>      	
						</div>
					</div>
				@endforeach
				@if( $devolution->id_estado_devolucion == 1 )
					<div class="stage col-md-3 col-sm-3 pending">
						<div class="stage-header stage-pending"></div>
						<div class="stage-content">
							<h3 class="stage-title">Pago</h3>
							<span class="label label-info">
								{{ strtoupper( $solicitud->updatedBy->personal->full_name ) }}
							</span>
						</div>
					</div>
				@endif
				@if( in_array( $devolution->id_estado_devolucion , [ 1 , 2 ] ) )
					<div class="stage col-md-3 col-sm-3">
						<div class="stage-header"></div>
						<div class="stage-content">
							<h3 class="stage-title">Confirmacion</h3>
							<span class="label label-info">
								TESORERIA
							</span>
						</div>
					</div>
				@endif
			@endforeach
		</div>
	</div>
@endif