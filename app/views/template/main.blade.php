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
        {{ HTML::style('css/bago.bootstrap.css') }}
        {{ HTML::style('css/bootstrap-lightbox.css')}}
        {{ HTML::style('css/bootstrap-theme.min.css') }} 
        {{ HTML::style('css/jquery-ui.min.css') }}
        {{ HTML::style('css/ladda-themeless.min.css') }}
        {{ HTML::style('css/stylos.css') }}
        {{ HTML::style('css/typeahead.css') }}
        {{ HTML::style('css/main.css') }}
        {{ HTML::style('css/datepicker3.css') }}
        {{ HTML::style('css/dataTables.bootstrap.css') }}
        {{ HTML::style('css/daterangepicker-bs3.css') }}
        {{ HTML::style('css/bago.report.css') }}
        {{ HTML::style('css/gsdk-base.css') }}
        {{ HTML::style('css/timeline.css') }}
        {{ HTML::style('css/chosen-bootstrap.css') }}
        {{ HTML::style('css/chosen.css') }}
        {{ HTML::style('css/select2.css') }}
        {{ HTML::style('css/select2-bootstrap.min.css') }}
        {{ HTML::style('css/stylosAux.css') }}
        {{ HTML::script('js/jquery_2.1.0.min.js') }}
        {{ HTML::script('https://kit.fontawesome.com/b5b3fa01a5.js') }}
        {{ HTML::script('js/jquery.validate.js') }}
        {{ HTML::style('css/alertify.min.css') }}
        {{ HTML::style('css/default.min.css') }}
        {{ HTML::style('css/semantic.min.css') }}
        {{ HTML::style('css/bootstrap.alertify.min.css') }}
        {{ HTML::script('js/alertify.min.js') }}
        {{ HTML::style('css/stylo_cayro.css') }}

    </head>
    <style type="text/css">
        .zoomContainer{ z-index: 9999;}
         .zoomWindow{ z-index: 9999;}
    </style>
    <body>
        <div id="alert-console" class="container-fluid" style="z-index: 99999999; margin-top: 10px;"></div>
        <header>
            {{ HTML::link('/show_user', '', array('id' => 'logo', 'title' => 'Bagó Perú', 'alt' => 'Bagó Perú')) }}
            <a id="logout" href="{{URL::to('logout')}}" title="Cerrar Sesión" alt="Cerrar Sesión">
                <div class="pull-left" style="font-size: 11pt; color: #fff; margin-top: 20px; margin-right: 10px; margin-left: 10px;">{{ Auth::user()->getFirstName() }}  | 
                    <span class="closed-session">Cerrar sesión</span>
                </div>
                <div class="pull-left">
                    <img class="img-circle" src="{{URL::to('/')}}/img/user.png">
                </div>
            </a>
        </header>
        <div class="container-fluid" style="max-height: 100vh; padding-left: 0; padding-right: 0;">
            <nav class="navbar navbar-bago navbar-static-top" role="navigation" style="/*margin-bottom: 0*/; z-index: 10;">
                <div class="container-fluid">
                    <div class="navbar-header relative">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            @if( Auth::user()->type != ESTUD )
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                        Solicitud  
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        @if ( in_array( Auth::user()->type , array( REP_MED , SUP , GER_PROD , ASIS_GER ) ) )
                                            <li>
                                                <a href="{{URL::to('nueva-solicitud')}}">
                                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" ></span>
                                                    <span class="glyphicon-class"> Nuevo</span>
                                                </a>
                                            </li>
                                            <li role="separator" class="divider"></li>
                                        @endif
                                        <li><a href="{{ URL::to('show_user') }}">Listado de Solicitudes</a></li>
                                        @if ( Auth::user()->type == ASIS_GER )
                                            <li><a href="{{ URL::to('solicitude/institution') }}">Solicitudes Institucionales</a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif
                            @if ( in_array( Auth::user()->type, array( REP_MED , SUP, GER_PROD, GER_PROM, GER_COM , GER_GER , CONT ) ) )
                                <li><a href="{{ URL::to('solicitude/statement')}}">Movimientos</a></li>
                            @endif
                            @if( Auth::user()->type != ESTUD )
                                <li><a href="{{ URL::to('eventos')}}">Eventos</a></li>
                            @endif
                            @if( ! in_array( Auth::user()->type , [ REP_MED , ESTUD ] ) ) 
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                        Reportes 
                                        <span class="caret"></span>
                                    </a>
                                    <ul id="menu-report" class="dropdown-menu" role="menu">
                                        @if( Auth::user()->type == GER_PROM )
                                            <li><a href="{{ URL::to('maintenance/view/Fondo_Supervisor') }}">Fondos de Supervisor</a></li>
                                            <li><a href="{{ URL::to('maintenance/view/Fondo_Gerente_Producto') }}">Fondos de G. Promocion</a></li>
                                            <li><a href="{{ URL::to('maintenance/view/Fondo_Institucion') }}">Fondos Institucionales</a></li>
                                        @elseif( Auth::user()->type == GER_PROD )
                                            <li><a href="{{ URL::to('maintenance/view/Fondo_Gerente_Producto') }}">Fondos de G. Producto</a></li>
                                        @endif

                                        <li class="report_menubar_option new">
                                            <a href="#" rel="new" data-toggle="modal" data-target=".report_new">
                                                <span class="glyphicon glyphicon-plus" aria-hidden="true" ></span>
                                                <span class="glyphicon-class"> Nuevo Reporte</span>
                                            </a>
                                        </li>
                                        <li class="divider"></li>
                                        @if( Auth::user()->type == CONT )
                                            <li><a href="{{ URL::to('solicitudsToDeposit') }}">Solicitudes a Depositar</a></li>
                                            <li><a href="{{ URL::to( 'report/cont/view/cuenta' ) }}">Estado</a></li>
                                            <li><a href="{{ URL::to( 'report/cont/view/completo' ) }}">Completo</a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif
                            @if ( in_array(Auth::user()->type, array(SUP, GER_PROD, GER_PROM, GER_COM, CONT , ESTUD )) )
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                        Configuración 
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu" role="menu">
                                        
                                        @if ( in_array(Auth::user()->type, array( SUP , GER_PROD , GER_PROM , GER_COM ) ) )
                                            <li><a data-toggle="modal" data-target="#modal-temporal-user">Derivación de Usuario</a></li>
                                        @endif

                                        @if( Auth::user()->type == GER_COM )
                                            <li><a href="{{ URL::to('fondoHistorial') }}">Historial de Saldo de los Fondos</a></li>
                                            <li><a href="{{ URL::to('maintenance/view/Tipo_Inversion') }}">Mantenimiento de Inversion</a></li>
                                            <li><a href="{{ URL::to('maintenance/view/Tipo_Actividad') }}">Mantenimiento de Actividades</a></li>
                                            <li><a href="{{ URL::to('maintenance/view/Inversion_Actividad') }}">Mantenimiento de Inversion-Actividad</a></li>
                                            <li><a href="{{ URL::to('maintenance/view/Parametro') }}">Mantenimiento de Parametros</a></li>
                                            <li><a href="{{ URL::to('maintenance/view/Fondo_Supervisor') }}">Mantenimiento de Fondos de Supervisor</a></li>
                                            <li><a href="{{ URL::to('maintenance/view/Fondo_Gerente_Producto') }}">Mantenimiento de Fondos de G. Producto / G. Promocion</a></li>
                                            <li><a href="{{ URL::to('maintenance/view/Fondo_Institucion') }}">Mantenimiento de Fondos Institucionales</a></li>
                                        @elseif( Auth::user()->type == CONT )
                                            <li><a href="{{ URL::to('maintenance/finddocument') }}">Busqueda de Documentos Registrados</a></li>
                                            <li><a href="{{ URL::to('maintenance/documenttype') }}">Mantenimiento de Tipo de Documentos</a></li>
                                            <li><a href="{{ URL::to('maintenance/view/Fondo_Contable' ) }}">Mantenimiento de Fondo de Contabilidad</a></li>
                                            <li><a href="{{ URL::to('maintenance/view/Cuenta_Gasto_Marca') }}">Mantenimiento de Cuentas - Marcas</a></li>
                                            <li><a href="{{ URL::to('maintenance/view/Solicitud_Exclusion') }}">Bloqueo de Solicitud</a></li>
                                            <li><a href="{{ URL::to('maintenance/view/Cuenta_Especial') }}">Cambio de Cuenta de Reembolso</a></li>
                                        @elseif( Auth::user()->type == SUP )
                                            <li><a href="{{ URL::to( 'report/sup/view/Fondo_Supervisor' ) }}">Reporte de Fondos de Supervisor</a></li>
                                        @elseif( Auth::user()->type == ESTUD )
                                            <li><a href="{{ URL::to( 'view-sup-rep' ) }}">Syncronizacion</a></li>
                                            <li><a href="{{ URL::to( 'view-ppto' ) }}">Presupuesto</a></li>
                                            <li><a href="{{ URL::to('maintenance/view/Fondo_Subcategoria') }}">Mantenimiento de Categorías de Fondo</a></li> 
                                        @endif
                                    </ul>
                                </li>
                            @endif      
                            @if( ! in_array( Auth::user()->type , [ REP_MED , ESTUD ] ) )
                                <li class="report_menubar_option btn_extra" style="display:none;">
                                    <a href="#" rel="export">
                                        <span class="glyphicon glyphicon-save" aria-hidden="true"></span>
                                        <span class="glyphicon-class">  Exportar</span>
                                    </a>
                                </li>
                                <li class="report_menubar_option btn_extra2" style="display:none;">
                                    <a href="#" rel="email">
                                        <span class="glyphicon glyphicon-envelope" ></span>
                                        <span class="glyphicon-class">  Enviar Correo</span>
                                    </a>
                                </li>
                             @endif
                        </ul>
                        <a class="btn btn-primary navbar-btn sim_alerta" href="{{ URL::to('alerts') }}" role="button">
                            Alertas 
                            <span class="badge badge-success"></span>
                        </a>
                        @if( Auth::user()->type != ESTUD )
                            <div id="drp_menubar" class="navbar-form navbar-right btn-default navbar-btn" style="cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -8px;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span> 
                                <b class="caret"></b>
                            </div>
                        @endif
                    </div>
                </div>
            </nav>
            
            <div id="dataTable" class="container-fluid" style="font-size:10pt">
                @yield('solicitude')
                <br>
            </div>
        </div>

        <div id="loading" style="display: none">
            {{ HTML::image('img/spiffygif.gif') }}
        </div>
        @include('template.Modals.temporal_user')

        {{ HTML::script('js/jquery.blockUI.js') }}
        {{ HTML::script('js/jquery.numeric.js') }}
        {{ HTML::script('js/jquery-ui.min.js') }}
        {{ HTML::script('js/jquery.form.js') }}
        {{ HTML::script('js/bootstrap.min.js') }}
        {{ HTML::script('js/bootstrap-datepicker.js') }}
        {{ HTML::script('js/bootstrap-datepicker.es.js') }}
        {{ HTML::script('js/bootbox.min.js') }}
        {{ HTML::script('js/jquery.dataTables.min.js') }}
        {{ HTML::script('js/dataTables.bootstrap.js') }}
        {{ HTML::script('js/spin.min.js') }}
        {{ HTML::script('js/ladda.min.js') }}
        {{ HTML::script('js/bootstrap-lightbox.js') }}
        {{ HTML::script('js/typeahead.js') }}
        {{ HTML::script('js/js.js') }}
        {{ HTML::script('js/jsdmkt.js') }}
        {{ HTML::script('js/locales/bootstrap-tagsinput.min.js') }}
        {{ HTML::script('js/moment.js') }}
        {{ HTML::script('js/moment.locale.es.js') }}
        {{ HTML::script('js/select2.js') }}
        {{ HTML::script('js/jquery.elevatezoom.js') }}
        {{ HTML::script('js/sweetalert2.all.min.js') }}

        <script type="text/javascript">
            URL_BASE = '{{ asset( '/' ) }}';
            @if( isset( $solicitud ) )  
                @if( in_array( $solicitud->id_estado , array ( GASTO_HABILITADO , ENTREGADO ) ) && isset( $date ) )
                    START_DATE = '{{ $date[ "startDate" ] }}';
                    END_DATE   = '{{ $date[ "endDate" ] }}';
                @endif
            @else
                START_DATE = moment().startOf('month');
                END_DATE   = moment();
                @if( isset( $date ) )
                    START_DATE = moment( "{{ $date[ "startDate" ] }}" , "DD/MM/YYYY" );
                    END_DATE   = moment( "{{ $date[ "endDate" ] }}" , "DD/MM/YYYY" );
                @endif
            @endif

         
        </script>


        {{ HTML::script('js/daterangepicker.js') }}
        {{ HTML::script('js/jquery.bootstrap.wizard.js') }}
        {{ HTML::script('js/bago.reports.js') }}
        {{ HTML::script('js/bago.reports.main.js') }}
        {{ HTML::script('js/wizard.js') }}
        {{ HTML::script('js/chosen.jquery.js') }}
    </body>
</html>