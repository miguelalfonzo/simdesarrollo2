@extends( 'template.main' )
@section( 'solicitude' )
	<div class="container-fluid jumbotron">
		<h3 style="margin-top:0px">Habilitacion del Proceso de Carga del Presupuesto</h3>
		<h5><b>Nota: Mientras el proceso este habilitado solo el rol de estudios puede ingresar al sistema</b></h5>
		<button id="ppto-enable" type="button" class="btn btn-warning">Confirmar</button>
	</div>

	<script>
	
		var enableButton = document.getElementById( 'ppto-enable' );
		enableButton.onclick = function()
		{
			var data = { _token : GBREPORTS.token };
			$.post( 'ppto-enable' , data ).fail( function( statusCode , errorThrown )
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

	</script>
@stop