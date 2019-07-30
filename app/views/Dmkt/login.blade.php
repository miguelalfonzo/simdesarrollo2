<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta name="csrf-token" content="<?= csrf_token() ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('apple-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('apple-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('apple-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('apple-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('apple-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('apple-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('apple-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-icon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('android-icon-192x192.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{ asset('ms-icon-144x144.png') }}">
        <meta name="theme-color" content="#ffffff">
        <meta name="author" content="Laboratorito Bagó | Perú">
        <title>Sistema de Inversiones Marketing</title>     
        {{ HTML::style('css/bootstrap.min.css') }}
        {{ HTML::style('css/stylos.css') }}
        {{ HTML::script('js/jquery_2.1.0.min.js') }}
    </head>

    <body style="background:url(img/logo-marcadeagua.png);">
        @if ( $errors->has() )
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                @foreach ($errors->all() as $error)
                    <strong>{{ $error }}</strong><br>        
                @endforeach
            </div>
        @endif
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <div class="panel panel-default" style="margin-top:45%;">
                        <div class="panel-heading">
                            <span class="glyphicon glyphicon-lock"></span>Acceso al Sistema</div>
                        <div class="panel-body">
                            {{ Form::open(array('url'=>'login','class'=>'form-horizontal')) }}
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">
                                        Usuario</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="username" id="inputEmail3"  required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-3 control-label">
                                        Clave</label>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" name="password" id="inputPassword3"  required>
                                    </div>
                                </div>
                                <div class="form-group last" style="padding: 10px;" class="col-sm-3 control-label">
                                    <div class="btn-group btn-group-justified">
                                        <button type="submit" class="btn btn-success btn-lg"  style="width: 33.33%; float: left !important; font-size: 12pt;">Ingresar</button>
                                        <button type="reset" class="btn btn-default btn-lg"  style="width: 33.33%; float: left !important; font-size: 12pt;">Limpiar</button>
                                        <a href="http://intra.bagoperu.com.pe/produccion/intranet.php" class="btn btn-danger btn-lg"  style="width: 33.33%; float: left !important; font-size: 12pt;">Regresar</a>
                                    </div>
                                </div>
                                {{--@if( Session::has( 'message' ) )
                                    <p style="color:red; text-align: center;"><strong>{{ Session::get( 'message' ) }}</strong></p>
                                @endif --}}
                           {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ HTML::script('js/bootstrap.min.js') }}
        {{ HTML::script('js/bootbox.min.js') }}
    </body>
</html>
<script>
    $( document ).ready( function()
    {
        @if( Session::has( 'message' ) )
            bootbox.alert( '<h4 class="text-warning">{{ Session::get( 'message' ) }}</h4>' );
        @endif
    });
</script>
