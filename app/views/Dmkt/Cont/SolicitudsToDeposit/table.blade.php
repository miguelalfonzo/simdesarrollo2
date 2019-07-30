<table id="table_solicitudsToDeposit" class="table table-striped table-hover table-bordered" cellpadding="0" cellspacing="0" border="0" width="100%">
    @include( 'Dmkt.Cont.SolicitudsToDeposit.table_head' )
    @include( 'Dmkt.Cont.SolicitudsToDeposit.table_body' )
</table>
<script>
    $( document ).ready( function()
    {
        dataTable( 'solicitudsToDeposit' , '' , 'solicitudes' );
    });
</script>
