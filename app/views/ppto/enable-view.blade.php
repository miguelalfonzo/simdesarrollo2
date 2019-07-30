@extends( 'template.main' )
@section( 'solicitude' )
	<div class="container-fluid jumbotron">
		<h3 style="margin-top:0px">Inhabilitacion del Proceso de Carga de Presupuesto</h3>
		<h5><b>Nota: Si no se inhabilita el proceso los usuarios no podran ingresar al sistema</b></h5>
		<button id="ppto-disable" type="button" class="btn btn-success">Confirmar</button>
	</div>

	<ul class="nav nav-tabs" role="tablist">
		<li class="active">
			<a href="#tab-ppto-sup" role="tab" data-toggle="tab">
            	Supervisor
            </a>
		</li>
		<li>
			<a href="#tab-ppto-ger" role="tab" data-toggle="tab">
            	Gerente
            </a>
		</li>
		<li>
			<a href="#tab-ppto-ins" role="tab" data-toggle="tab">
            	Institucional
            </a>
		</li>
	</ul>		

	<div class="tab-content">
		<div class="tab-pane fade active in" id="tab-ppto-sup" data-type="1">

			
				<div class="form-group col-md-12">
					<h4><b>Carga de Presupuesto Supervisor</b></h4>
				</div>
				
				<div class="form-group col-lg-2">
					<label>Año</label>
					<select class="form-control ppto-year">
						@foreach( $years as $year )
							<option value="{{ $year }}">{{ $year }}</option>
						@endforeach
					</select>
				</div>

				<div class="form-group col-lg-2">
					<label>Categoría</label>
					<select class="form-control ppto-category">   
						@foreach( $categories as $category )
							@if( $category->tipo == SUP )
								<option value="{{ $category->id }}">{{ $category->descripcion }}</option>
							@endif
						@endforeach
					</select>
				</div>

				<div class="form-group col-lg-2">
					<label>Version</label>
					<select class="form-control ppto-version"></select>
				</div>

				<div class="form-group col-lg-3">
					<label>Excel</label>
					<div class="input-group">
						<span  class="input-group-addon btn glyphicon glyphicon-folder-open open-file" style="top:0"></span>
						<input type="text" class="form-control filename" readonly="true">
					</div>
					<input type="file" class="file" accept=" application/vnd.ms-excel , application/vnd.openxmlformats-officedocument.spreadsheetml.sheet , application/vnd.ms-excel.sheet.macroEnabled.12 " style="display:none">	
				</div>

				<div class="form-group col-xs-4 col-lg-1">
					<button type="button" class="btn btn-primary load-ppto ladda-button" data-style="zoom-in" style="margin-top:24px" >Cargar</button>
				</div>

				<div class="form-group col-xs-2 col-lg-1">
					<button type="button" class="btn btn-primary search-ppto ladda-button" data-style="zoom-in" style="margin-top:24px" >
						<span class="glyphicon glyphicon-search"></span>
					</button>
				</div>

				<div class="form-group col-xs-6 col-lg-1 pull-right">
					<button type="button" class="btn btn-primary export-ppto" style="margin-top:24px" >Exportar
						<span class="glyphicon glyphicon glyphicon-export"></span>
					</button>
				</div>

				<div style="clear:both"></div>
				<label>Nota: El campo de version no afecta la carga del presupuesto solo afecta a la busqueda del presupuesto</label>

			<table id="table-ppto-1" class="table table-striped table-hover table-bordered" cellspacing="0" width="100%">
			</table>
		</div>

		<div class="tab-pane fade" id="tab-ppto-ger" data-type="2">
			<div class="row">
				<div class="form-group col-md-12">
					<h4><b>Carga de Presupuesto Gerentes</b></h4>
				</div>
			</div>
			<div class="form-group col-lg-2">
				<label>Año</label>
				<select class="form-control ppto-year">
					@foreach( $years as $year )
						<option value="{{ $year }}">{{ $year }}</option>
					@endforeach
				</select>
			</div>

			<div class="form-group col-lg-2">
				<label>Categoría</label>
				<select class="form-control ppto-category"> 
					@foreach( $categories as $category )
						@if( in_array( $category->tipo , [ GER_PROD , GER_PROM ] ) ) 
							<option value="{{ $category->id }}">{{ $category->descripcion }}</option>
						@endif
					@endforeach
				</select>
			</div>

			<div class="form-group col-lg-2">
				<label>Version</label>
				<select class="form-control ppto-version"></select>
			</div>

			<div class="form-group col-lg-3">
				<label>Excel</label>
				<div class="input-group">
					<span  class="input-group-addon btn glyphicon glyphicon-folder-open open-file" style="top:0"></span>
					<input type="text" class="form-control filename" readonly="true">
				</div>
				<input type="file" class="file" accept=" application/vnd.ms-excel , application/vnd.openxmlformats-officedocument.spreadsheetml.sheet , application/vnd.ms-excel.sheet.macroEnabled.12 " style="display:none">	
			</div>

			<div class="form-group col-lg-1">
				<button type="button" class="btn btn-primary load-ppto ladda-button" data-style="zoom-in" style="margin-top:24px" >Cargar</button>
			</div>

			<div class="form-group col-lg-1">
				<button type="button" class="btn btn-primary search-ppto ladda-button" data-style="zoom-in" style="margin-top:24px" >
					<span class="glyphicon glyphicon-search"></span>
				</button>
			</div>

			<div class="form-group col-lg-1 pull-right">
				<button type="button" class="btn btn-primary export-ppto" style="margin-top:24px" >Exportar
					<span class="glyphicon glyphicon glyphicon-export"></span>
				</button>
			</div>

			<div style="clear:both"></div>
			<label>Nota: El campo de version no afecta la carga del presupuesto solo afecta a la busqueda del presupuesto</label>

			<table id="table-ppto-2" class="table table-striped table-hover table-bordered" cellspacing="0" width="100%" style="width:100%">
			</table>

		</div>
		<div class="tab-pane fade" id="tab-ppto-ins" data-type="3">
			<div class="row">
				<div class="form-group col-md-12">
					<h4><b>Carga de Presupuesto Institucional</b></h4>
				</div>
			</div>
			<div class="form-group col-lg-2">
				<label>Año</label>
				<select class="form-control ppto-year">
					@foreach( $years as $year )
						<option value="{{ $year }}">{{ $year }}</option>
					@endforeach
				</select>
			</div>

			<div class="form-group col-lg-2">
				<label>Version</label>
				<select class="form-control ppto-version"></select>
			</div>

			<div class="form-group col-lg-2">
				<label>Monto</label>
				<input id="ppto-amount" class="form-control">
			</div>

			<div class="form-group col-lg-1">
				<button type="button" class="btn btn-primary load-ppto ladda-button" data-style="zoom-in" style="margin-top:24px" >Cargar</button>
			</div>

			<div class="form-group col-lg-1">
				<button type="button" class="btn btn-primary search-ppto ladda-button" data-style="zoom-in" style="margin-top:24px" >
					<span class="glyphicon glyphicon-search"></span>
				</button>
			</div>

			<div class="form-group col-lg-1 pull-right">
				<button type="button" class="btn btn-primary export-ppto" style="margin-top:24px" >Exportar
					<span class="glyphicon glyphicon glyphicon-export"></span>
				</button>
			</div>
			
			<div style="clear:both"></div>
			<label>Nota: El campo de version no afecta la carga del presupuesto solo afecta a la busqueda del presupuesto</label>
			

			<table id="table-ppto-3" class="table table-striped table-hover table-bordered" cellspacing="0" width="100%" style="width:100%">
			</table>

		</div>
	</div>

	<script>
		
		var file     = $( '.file' );
		var filename = $( '.filename' );

		function loadPPTO( tab )
		{
			var type    = tab.attr( 'data-type' );
			var year    = tab.find( '.ppto-year' ).val();
			var version = tab.find( '.ppto-version' ).val();
			
			var data =
			{
				_token  : GBREPORTS.token,
				type    : type,
				year    : year,
				version : version	
			};

			if( type != 3 )
			{
				data.category = tab.find( '.ppto-category' ).val();
			}

			var spin = Ladda.create( tab.find( '.search-ppto' )[ 0 ] );
			spin.start();

			$.ajax(
			{
				type : 'POST',
				url  : 'load-ppto',
				data : data
			}).fail( function( statusCode , errorThrown )
			{
				ajaxError( statusCode , errorThrown );
			}).done( function( response )
			{
				spin.stop();
				var dataTable = $( '#table-ppto-' + type ).DataTable(
	            {
	                columns         : response.columns,
	                data            : response.Data ,
	                dom             : "<'row'<'col-xs-6'><'col-xs-6 pull-right'f>r>t<'row'<'col-xs-6'i><'col-xs-6'p>>",
	                destroy         : true,
	                pageLength      : 10,
	                stateSave       : true,
	                scrollX         : true,
	                language        :
	                {
	                    search       : 'Buscar',
	                    zeroRecords  : 'No hay ' + 'solicitudes' ,
	                    infoEmpty    : 'No ha encontrado ' + 'solicitudes' +' disponibles',
	                    info         : 'Mostrando _END_ de _TOTAL_ ' + 'solicitudes' ,
	                    lengthMenu   : "Mostrando _MENU_ registros por página",
	                    infoEmpty    : "No ha encontrado información disponible",
	                    infoFiltered : "(filtrado de _MAX_ regitros en total)",
	                    paginate     : 
	                    {
	                        sPrevious : 'Anterior',
	                        sNext     : 'Siguiente'
	                    }
	                },
	                createdRow: function( row , data , dataIndex )
	                {
	                    $( row ).append( '<input type="hidden" class="ppto-id" value="' + data.id + '">' );
	                }
	            });
			});
		}

    	$( 'a[data-toggle="tab"]').on( 'shown.bs.tab', function()
    	{
    		$.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
    	});

    	$( '.search-ppto' ).click( function()
    	{
    		var panel = $( this ).closest( '.tab-pane' );
    		loadPPTO( panel );
    	});

    	$( document ).off( 'click' , '.edit-ppto-row' );
    	$( document ).on( 'click' , '.edit-ppto-row' , function()
    	{
    		var elem = $( this );
    		elem.fadeOut();
    		var tr = elem.closest( 'tr' );
    		var montoCell = tr.find( '.monto-cell' );
    		var inputs = 
    			'<input type="text" value="' + montoCell.text().trim() + '" class="form-control"/>' +
    			'<input type="hidden" value="' + montoCell.text().trim() + '" class="form-control monto-ppto-row"/>'
    		
    		montoCell.html( inputs );
    		tr.find( '.save-ppto-row' ).fadeIn(); 
    		tr.find( '.cancel-ppto-row' ).fadeIn(); 
    	});

    	$( document ).off( 'click' , '.save-ppto-row' );
    	$( document ).on( 'click' , '.save-ppto-row' , function()
    	{
    		var elem = $( this );
    		var tr = elem.closest( 'tr' );
    		
    		var type = elem.closest( '.tab-pane' ).attr( 'data-type' );
    		var ppto_id = tr.find( '.ppto-id' ).val();
    		var monto = tr.find( '.monto-cell input[ type=text ]' ).val();
    		
    		$.ajax(
			{
				type : 'POST',
				url  : 'update-ppto-row',
				data : 
				{
					_token    : GBREPORTS.token,
					type      : type,
					ppto_id   : ppto_id,
					monto     : monto
				}
			}).fail( function( statusCode , errorThrown )
			{
				ajaxError( statusCode , errorThrown );
			}).done( function( response )
			{
				if( response.Status == ok )
				{
					tr.find( '.monto-cell' ).html( response.Data );
					elem.fadeOut();
					tr.find( '.cancel-ppto-row' ).fadeOut();
					tr.find( '.edit-ppto-row' ).fadeIn();
					loadPPTO( elem.closest( '.tab-pane' ) );
				}
				bootboxMessage( response );
			
			});
    	});

    	$( document ).off( 'click' , '.cancel-ppto-row' );
    	$( document ).on( 'click' , '.cancel-ppto-row' , function()
    	{
    		var elem = $( this );
    		elem.fadeOut();
    		var tr = elem.closest( 'tr' );
    		var monto = tr.find( '.monto-ppto-row' ).val();
    		tr.find( '.monto-cell' ).html( monto );
    		tr.find( '.edit-ppto-row' ).fadeIn();
    		tr.find( '.save-ppto-row' ).fadeOut();
    	});

		$( '.open-file' ).click( function()
		{
			$( this ).closest( '.form-group' ).find( 'input[ type=file ]' ).click();
		});

		file.on( 'change' , function()
		{
			$( this ).parent().find( '.filename' ).val( this.files[ 0 ].name ).closest( '.form-group' ).addClass( 'has-success' ).removeClass( 'has-error' );
		});

		$( '.load-ppto' ).click( function()
		{
			var panel = $( this ).closest( '.tab-pane' );
			var data = new FormData();
			var type = panel[ 0 ].dataset.type;
			var year = panel.find( '.ppto-year' ).val();
			
			if( type == 3 )
			{
				data.append( 'year'  , year );
				data.append( 'amount' , panel.find( '#ppto-amount' ).val() );
			}
			else
			{
				data.append( 'file' , panel.find( '.file' )[ 0 ].files[ 0 ] );
				data.append( 'year'  , year );
				data.append( 'category'  , panel.find( '.ppto-category' ).val() );	
			}
			data.append( 'type' , type );
			data.append( '_token' , GBREPORTS.token );
		
			var spin = Ladda.create( this );
			spin.start();	
			$.ajax(
			{
				type : 'POST',
				url  : 'upload-ppto',
				data : data,
				contentType: false,
				processData: false,
				cache: false,
				dataType: 'json',
			}).fail( function( statusCode , errorThrown )
			{
				ajaxError( statusCode , errorThrown );
			}).done( function( response )
			{
				spin.stop();
				if( response.Status == ok )
				{
					//loadPPTO( panel );
					getVersions( panel );
				}
				bootboxMessage( response );
			});
		});

		var disableButton = document.getElementById( 'ppto-disable' );
		disableButton.onclick = function()
		{
			var data = { _token : GBREPORTS.token };
			$.post( 'ppto-disable' , data ).fail( function( statusCode , errorThrown )
			{
				ajaxError( statusCode , errorThrown );
			}).done( function( response )
			{
				if( response.Status == ok )
				{
					window.location.reload();
				}
				else
				{
					bootboxMessage( response );
				}	
			});
		};

		function getVersions( tab )
		{
			var type 	 = tab.attr( 'data-type' );
			var year 	 = tab.find( '.ppto-year' ).val();
			
			var data =
			{
				_token   : GBREPORTS.token,
				type     : type,
				year     : year
			};

			if( type != 3 )
			{
				data.category = tab.find( '.ppto-category' ).val();
			}

			$.ajax(
			{
				type : 'POST',
				url  : 'ppto-versions',
				data : data
			}).fail( function( statusCode , errorThrown )
			{
				ajaxError( statusCode , errorThrown );
			}).done( function( response )
			{
				if( validateResponse( response ) )
				{
					var html = '';
					response.Data.forEach( function( value )
					{
						html += '<option value="' + value.version + '">' + value.version + '</option>';
					});
					tab.find( '.ppto-version' ).html( html );
				}
				else
				{
					bootboxMessage( response );
				}
			});
		}

		var selectsYear = $( '.ppto-year' );
		selectsYear.change( function()
		{
			var tab = $( this ).closest( '.tab-pane' );
			getVersions( tab );
		});

		var selectsCategory = $( '.ppto-category' );
		selectsCategory.change( function()
		{
			var tab = $( this ).closest( '.tab-pane' );
			getVersions( tab );
		});

		$( '.export-ppto' ).click( function()
		{
			var tab = $( this ).closest( '.tab-pane' );
			var type = tab.attr( 'data-type' );
			var year = tab.find( '.ppto-year' ).val();
			var category = tab.find( '.ppto-category' ).val();
			var version = tab.find( '.ppto-version' ).val();
			var url = server + 'ppto-export' + '-' + type + '-' + year + '-' + category + '-' + version;
			window.location.href = url;
		});

		$( document ).ready( function()
		{
			$( '#ppto-amount' ).numeric( { negative : false } );
			var tabPPTOSup = $( '#tab-ppto-sup' );
			var tabPPTOGer = $( '#tab-ppto-ger' );
			var tabPPTOIns = $( '#tab-ppto-ins' );
			loadPPTO( tabPPTOSup );
			getVersions( tabPPTOSup );
			
			loadPPTO( tabPPTOGer );
			getVersions( tabPPTOGer );
			
			loadPPTO( tabPPTOIns );
    		getVersions( tabPPTOIns );
			
    	});

	</script>
@stop
