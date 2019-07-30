@extends('template.main')
@section('solicitude')

<section class="container-fluid" >
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Detalle de Evento</h3>
            </div>
            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Nombre del Evento</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" placeholder="Nombre del Evento" maxlength="100">
                        </div>
                    </div>

DESCRIPTION
PLACE
EVENT_DATE
                    <div class="form-group">
                        <label for="event-name" class="col-sm-2 control-label">Fecha del Evento</label>
                        <div class="col-sm-10">
                            <div id="event-date" class="input-group">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-calendar"></i>
                                </span>
                                <input type="text" class="form-control" maxlength="10" value="" maxlength="250">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="event-place" class="col-sm-2 control-label">Lugar del Evento</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="event-place" placeholder="(Opcional)" maxlength="250">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="event-description" class="col-sm-2 control-label">Descripcion del Evento</label>
                        <div class="col-sm-10">
                            <textarea id="event-description" type="text" class="form-control"  rows="4" maxlength="250"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-10">
                            <div class="btn-group" role="group" aria-label="Opciones">
                              <button type="button" class="btn btn-primary">Crear</button>
                              <button type="button" class="btn btn-info">Subir Imagenes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<script>
    $(document).ready(function(){
        $("#event-date input").datepicker({
            clearBtn: true,
            language: "es",
            multidate: true,
            todayHighlight: true,
            toggleActive: true
        });
    });
</script>

<div class="container">
    <div class="span8">
        <!-- Post Title -->
        <div class="row">
            <div class="span8">
                <h4>Ajax Image Upload and Preview With Laravel</h4>
                
            </div>
        </div>
        <!-- Post Footer -->
        <div class="row">
            <div class="span3">
                <div id="validation-errors"></div>
                <form class="form-horizontal" id="solicitude-upload-image-event" enctype="multipart/form-data" method="post" action="{{ url('testUploadImgSave') }}" autocomplete="off">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <span class="btn btn-info btn-file">
                        Subir Imagenes <input type="file" name="image[]" id="upload-image-event" multiple="true" /> 
                    </span>
                </form>
 
            </div>
            <div class="span5" id="output" style="display:none;">
                
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    var options = {
                beforeSubmit:   showRequest,
                success:        showResponse,
                dataType:       'json' 
        }; 
    $('body').delegate('#upload-image-event','change', function(){
        $('#solicitude-upload-image-event').ajaxForm(options).submit();         
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
@stop
