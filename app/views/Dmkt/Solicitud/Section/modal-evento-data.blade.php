<div class="modal fade" id="event-section" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Detalle de Evento</h4>
			</div>
			<div class="modal-body">
					<div class="panel-body">
						<form class="form-horizontal" {{ isset($event) ? '' : 'action="'. URL::to('createEvent') .'" accept-charset="UTF-8" method="POST"' }}>
							{{ Form::token() }}
							<div class="form-group hide">
								<label for="name" class="col-sm-2 control-label">Id Solicitud</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="solicitud_id" placeholder="Id de Solicitud" maxlength="100" required="required" value="{{ $solicitud->id }}">
								</div>
							</div>
							<div class="form-group">
								<label for="name" class="col-sm-2 control-label">Nombre del Evento</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="name" placeholder="Nombre del Evento" maxlength="100" required="required" {{ isset($event) ? 'value="'. $event->name .'" disabled' : ''}}>
								</div>
							</div>
							<div class="form-group">
								<label for="event-date" class="col-sm-2 control-label">Fecha del Evento</label>
								<div class="col-sm-10">
									<div id="event-date" class="input-group">
										<span class="input-group-addon">
											<i class="glyphicon glyphicon-calendar"></i>
										</span>
										<input type="text" class="form-control" maxlength="10" maxlength="250" name="event_date" required="required"  {{ isset($event) ? 'value="'. $event->event_date .'" disabled' : ''}}>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="place" class="col-sm-2 control-label" >Lugar del Evento</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="place" placeholder="(Opcional)" maxlength="250" required="required" {{ isset($event) ? 'value="'. $event->place .'" disabled' : ''}}>
								</div>
							</div>
							<div class="form-group">
								<label for="description" class="col-sm-2 control-label">Descripcion del Evento</label>
								<div class="col-sm-10">
									<textarea name="description" type="text" class="form-control"  rows="4" maxlength="250" required="required" {{ isset($event) ? 'disabled' : '' }}>{{ isset($event) ? $event->description : ''}}</textarea>
								</div>
							</div>
							@if(!isset($event))
							<div class="form-group">
								<div class="col-sm-offset-2 col-sm-10">						
									<button type="button" class="btn btn-primary btn_event_submit">Crear</button>
								</div>
							</div>
							@endif
						</form>
						<form class="form-horizontal" id="solicitude-upload-image-event" enctype="multipart/form-data" method="post" action="{{ url('testUploadImgSave') }}" autocomplete="off" style="{{ isset($event) ? '' : 'display:none;' }}">
							{{ Form::token() }}
							<div class="form-group hide">
								<label for="name" class="col-sm-2 control-label">Id Evento</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="event_id" placeholder="Id del Evento" maxlength="100" required="required" value="{{ isset($event) ? $event->id : '' }}">
								</div>
							</div>
							<div class="form-group ">
								<label for="name" class="col-sm-2 control-label"></label>
								<div class="col-sm-10">
									<span class="btn btn-info btn-file">
										Subir Imagenes <input type="file" name="image[]" id="upload-image-event" multiple="true" /> 
									</span>
								</div>
							</div>
						</form>
						<div id="output">
							@if(isset($event))
							@if($event->photos())
							@foreach($event->photos() as $key => $photo)
							<div class="col-xs-6 col-md-3 solicitude_img thumbnail show_event_img" data-slide-num="{{ $key }}">
								<img data-img-id="{{$photo->id}}" src="{{asset($photo->directory.$photo->id.'.'.$photo->extension)}}" >
							</div>
							@endforeach
							@endif
							@endif
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
		</div>


	</div>
</div>
<script>
	$(document).ready(function() {
		$("#event-date input").datepicker({
			clearBtn: true,
			language: "es",
			multidate: true,
			todayHighlight: true,
			toggleActive: true
		});

		var options = {
			beforeSubmit:   showRequest,
			success:        showResponse,
			dataType:       'json' 
		}; 
		$('body').delegate('#upload-image-event','change', function(){
			$('#solicitude-upload-image-event').ajaxForm(options).submit();         
		}); 
		$('.btn_event_submit').click(function(){
			var eventForm = $(this).parent().parent().parent();
			eventForm.ajaxForm({
				success: function(result){
					if(result.hasOwnProperty("status")){
						bootbox.alert(result.message);
						if(result.status=="error")
							console.log(eventForm);
						else if(result.hasOwnProperty("id")){
							$("input[name=event_id]").val(result.id);
							eventForm.find('input, textarea').prop('disabled', true);
							eventForm.find('button').first().hide('slow');
							$("#solicitude-upload-image-event").show('slow');
						}
					}
				},
				error: function(result){
					bootbox.alert(result);
				}                
			}).submit();     
		});
	});     
	function showRequest(formData, jqForm, options) { 
		$("#validation-errors").hide().empty();
		$("#output").css('display','none');
		return true; 
	} 
	function showResponse(response, statusText, xhr, $form)  { 
		if(response.success == false)
		{
			var arr = response.errors;
			$.each(arr, function(index, value)
			{
				if (value.length != 0)
				{
					$("#validation-errors").append('<div class="alert alert-error"><strong>'+ value +'</strong><div>');
				}
			});
			$("#validation-errors").show();
		} else {
			for (var i = response.fileList.length - 1; i >= 0; i--) {
				response.fileList[i]
				$("#output").append('<div class="col-xs-6 col-md-3 solicitude_img thumbnail"> <img data-img-id='+ response.fileList[i]['id'] +' src="'+ response.fileList[i]['name'] +'" /></div>');
				$("#output").css('display','block');
			};
		}
	}
</script>
@if( isset( $event ) && $event )
	<script>
		$(document).ready(function()
		{
			{{ ''; $html = '<div id="carousel-example-captions" class="carousel slide" data-ride="carousel" data-interval="5000"><ol class="carousel-indicators">'; }}
			@foreach($event->photos() as $key => $photo)
			{{''; $html.='<li data-target="#carousel-example-captions" data-slide-to="'. $key .'" class="'. ($key == 0 ? "active" : "") .'"></li>'; }}
			@endforeach
			{{''; $html.='</ol>'; }}
			{{''; $html.='<div class="carousel-inner" role="listbox">'; }}
			@foreach($event->photos() as $key => $photo)
			{{''; $html .='<div class="item '. ($key == 0 ? "active" : "") .'">' .
			'<img class="img_idkc" src="'.asset($photo->directory.$photo->id.'.'.$photo->extension).'">' .
			'<div class="carousel-caption">' .
			'<h3 id="first-slide-label">'.$event->name.'</h3>' .
			'<p>'. $event->description .'</p>' .
			'</div>' .
			'</div>'; }}
			@endforeach
			{{ ''; $html.='</div>'; }}
			{{ ''; $html .='<a class="left carousel-control" href="#carousel-example-captions" role="button" data-slide="prev">'.
			'<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>'.
			'<span class="sr-only">Previous</span>'.
			'</a>'.
			'<a class="right carousel-control" href="#carousel-example-captions" role="button" data-slide="next">'.
			'<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>'.
			'<span class="sr-only">Next</span>'.
			'</a>' }}
			{{'';$html.='</div>'; }}
			$(document).on('click', '.show_event_img', function(){
				var num = $(this).attr('data-slide-num');
				bootbox.dialog({
					message: '{{ $html }}',
					title  : "{{ $event->name }}",
					size   : "large"
				});
				$(".carousel-indicators>li, .carousel-inner>.item").removeClass("active");
				$(".carousel-indicators>li").eq(num).addClass("active");
				$(".carousel-inner>.item").eq(num).addClass("active");
			});
			$('.carousel').carousel();						
		});
	</script>
@endif