<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 total-fondo fondo_r" style="">
    <input class="form-control input-md" name="total" type="text" value="{{$total}}" readonly>
</div>
<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 pull-right fondo_r" >
    <a id="export-fondo" class="btn btn-sm btn-primary ladda-button" data-style="zoom-in" data-size="l">
        <i class="glyphicon glyphicon-print"></i> 
        Exportar
    </a>
</div>
@if( isset( $state ) && $state == ACTIVE )
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4 pull-right fondo_r" >
        <a id="terminate-fondo" class="btn btn-sm btn-danger ladda-button" data-style="zoom-in" data-size="l">
            <i class="glyphicon glyphicon-download"></i> 
            Terminar
        </a>
    </div>
@endif
<div class="table-responsive fondo_r">
    @include('Dmkt.AsisGer.tablefondo')
</div>