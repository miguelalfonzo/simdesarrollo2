<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Listado de Solicitudes a Depositar</title>
        {{ HTML::style('css/report.css') }}
    </head>
    <body style="background: url('img/logo-marcadeagua.png') no-repeat center fixed">
        <div class="background">
            <header>
                <img src="img/logo-report.png" style="width:170px">
                <h1 style="text-align:center"><strong>Solicitudes a Depositar</strong></h1>
            </header>
            <section style="text-align:center;height:auto">
                <table class="table">
                    @include( 'Dmkt.Cont.SolicitudsToDeposit.table_head' )
                    @include( 'Dmkt.Cont.SolicitudsToDeposit.table_body' )
                </table>
            </section>
            <footer style="bottom:0">
                <p class="firma">V°B° Contabilidad</p>
                <div style="width:120px;text-align:center" ><span class="dni">{{ Auth::user()->personal->full_name }}</span></div>
            </footer>
        </div>
    </body>
</html>