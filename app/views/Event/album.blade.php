		@if(count($events) == 0)
			<blockquote>
				<p>No se encontraron eventos registrados.</p>
			</blockquote>
		@endif
		@foreach($events as $event)
		  	<div class="col-sm-6 col-md-4">
			    <div class="thumbnail">
			    	@if($event->photos())
			    		@foreach($event->photos() as $key => $photo)
							@if($key == 0)
								<div class="solicitude_img">
									<img src="{{asset($photo->directory.$photo->id.'.'.$photo->extension)}}">
								</div>
								<div class="caption">
						        	<h3>{{$event->name}}</h3>
						        	<p>Fecha: {{$event->event_date}}</p>
						        	<p>{{$event->description}}</p>
						        	<p><a href="#" class="btn btn-primary photosEvent" role="button" data-event="{{$photo->event_id}}">Ver Fotos</a>
						      	</div>
							@endif
			    		@endforeach
			      	@else
				      	<div class="solicitude_img">
				      		<img src="{{asset('img/semfoto.png')}}">
				      	</div>
				      	<div class="caption">
				        	<h3>{{$event->name}}</h3>
				        	<p>Fecha: {{$event->event_date}}</p>
				        	<p>{{$event->description}}</p>
				      	</div>
			      	@endif
			    </div>
		  	</div>
		  	@endforeach
	
	<script>
		// var size = $(".col-sm-6.col-md-4").css('height');
		// $(".col-sm-6.col-md-4").css('height', size);
		heights = $(".col-sm-6.col-md-4").map(function ()
	    {
	        return $(this).height();
	    }).get(),

	    maxHeight = Math.max.apply(null, heights);
	    $(".col-sm-6.col-md-4").css('height', maxHeight);
	</script>