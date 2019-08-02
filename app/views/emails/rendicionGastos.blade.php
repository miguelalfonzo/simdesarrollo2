<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />
	<meta name="description" content="Static &amp; Dynamic Tables" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	{{ HTML::style('css/bootstrap.min.css') }}
    {{ HTML::style('css/fonts.googleapis.com.css') }}
    {{ HTML::style('css/ace.css') }}
    {{ HTML::style('css/ace-skins.min.css') }}
    {{ HTML::style('css/ace-rtl.min.css') }}	

</head>

<style>
	
	a.button {
    -webkit-appearance: button;
    -moz-appearance: button;
    appearance: button;

    text-decoration: none;
    color: initial;
}

</style>

<body class="no-skin">

	<div class="main-container ace-save-state" id="main-container">


		<div class="main-content">
			<div class="main-content-inner">

				<div class="page-content">
					<h5> Estimados Colaboradores: {{ $gastos['nombreRep'].'/'.$gastos['nombreSup'] }}</h5>
					<h6>{{ $gastos['parrafo'] }}</h6>
		<table id="TableProductos" class="table table-striped table-bordered table-hover table-condensed">
                <tbody id="BodyDetalleProducto">
                	<tr>
                        <td style="background: #2e6d9b;color: #ffffff;width: 10%;">Solicitud</td>
                        <td style="background: #ffffff;width: 35%;"> {{ $gastos['ID_SOLICITUD'] }} </td>
                        <td style="background: #2e6d9b;color: #ffffff;width: 10%;"> Ruc </td>
                        <td style="background: #ffffff;"> {{$gastos['RUC']}} </td>
                        <td style="background: #2e6d9b;color: #ffffff;">Tipo de Comprobante</td>
                        <td style="background: #ffffff;"> {{$gastos['TIPO']}} </td>
                    </tr>
                    <tr>
                        <td style="background: #2e6d9b;color: #ffffff;"> Razon Social </td>
                        <td style="background: #ffffff;"> {{$gastos['RAZON']}} </td>
                        <td style="background: #2e6d9b;color: #ffffff;"> # Documento </td>
                        <td style="background: #ffffff;">{{$gastos['NUM_PREFIJO'].'-'.$gastos['NUM_SERIE']}} </td>
                        <td style="background: #2e6d9b;color: #ffffff;"> Fecha </td>
                        <td style="background: #ffffff;"> {{$gastos['FECHA']}} </td> 
                    </tr>
                </tbody>
        </table>
        <br>
        <tr>
            <td style="background: #2e6d9b;color: #ffffff;"> Sub Total </td>
            <td style="background: #ffffff;"> {{$gastos['SUB_TOT']}} </td>
        </tr>
        <tr>
            <td style="background: #2e6d9b;color: #ffffff;"> Imp. por Servicio </td>
            <td style="background: #ffffff;">{{$gastos['IMP_SERV']}} </td>
        </tr>
        <tr>
            <td style="background: #2e6d9b;color: #ffffff;"> IGV </td>
            <td style="background: #ffffff;"> {{$gastos['IGV']}} </td> 
        </tr>
        <tr>
            <td style="background: #2e6d9b;color: #ffffff;"> Monto </td>
            <td style="background: #ffffff;"> {{$gastos['MONTO']}} </td> 
        </tr>


        <H5>Comentario: </H5><br>
        <p>{{$gastos['comentario']}}</p>
        <h5>
            <p>Atentamente,</p>
            <p>Sistema de Mensajería Automatizado</p>
        </h5>
		
		</div><!-- /.page-content -->
	</div>

</body>
</html>
