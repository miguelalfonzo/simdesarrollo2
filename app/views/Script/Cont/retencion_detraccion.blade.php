<script>
	$( '#regimen' ).on( 'change' , function()
	{
		if ( this.value == 0 )
	   	{
	        $( '#monto-regimen' ).closest( '.form-group' ).hide();
	    }
	    else
	   	{
	        $("#monto-regimen").closest( '.form-group' ).show();        
	    }
	});
</script>