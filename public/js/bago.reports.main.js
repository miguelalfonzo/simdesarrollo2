$(document).ready(function()
{
	$('#drp_menubar').on('apply.daterangepicker', function(ev, picker) {
		$('#btn_extra').hide();
		if( $( '#idState' ).length === 1 )
			listSolicituds();
		else if( $( '#container-event' ).length === 1 )
			getEvents();
		else if( $( '#fondo_mkt_history' ).length === 1 )
		{
			if ( $( '#fondoMkt' ).val() !== null )
				getSubCategoryHistory( $( '#fondoMkt' ) );
		}
		else if( $( '#movimientos' ).length === 1 )
			listTable( 'movimientos' , null );
		else if( $( '#report-type' ).length == 1 )
			getReportData();
		else
		{
			$(".btn_extra").hide("slow");
			GBREPORTS.openReport();
		}
	});
 //    $(document).off("change", "#query");
	// $(document).on("change", "#query", function(){
	// 	a = this;
	// 	a.style.height = 'auto';
	// 	a.style.height = a.scrollHeight+'px';
	// });

	$(document).off("click","li.report_menubar_option>a");
	$(document).on("click","li.report_menubar_option>a", function(e){
		e.preventDefault();

		var rel = $(this).attr("rel");
		if(rel=="open"){
			
			$('#btn_extra').hide();
			var reporte_id = $(this).attr("data-id");
			GBREPORTS.openReport(reporte_id);

		}else if(rel=="new"){
			GBREPORTS.getAllDataSet();
			
		}
		else if(rel=="export")
		{
			
			$("#loading").show("slow");
			GBREPORTS.getReportExcel("fm-grid-layout", GBREPORTS.lastReport.descripcion);
		}
		else if(rel=="email")
		{
			bootbox.dialog({
				message: GBREPORTS.modal.sendEmail,
				title: "Enviar Reporte por Email",
				buttons: {
					success: {
						label: "Enviar",
						className: "btn-success",
						callback: function() {
						}
					}
				}
			});
		}	
	});
});

HTMLElement.prototype.click = function() {
   var evt = this.ownerDocument.createEvent('MouseEvents');
   evt.initMouseEvent('click', true, true, this.ownerDocument.defaultView, 1, 0, 0, 0, 0, false, false, false, false, 0, null);
   this.dispatchEvent(evt);
}

