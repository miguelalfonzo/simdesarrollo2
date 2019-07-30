<?php

use Dmkt\Solicitud;
use Users\Rm;
use Dmkt\Account;
use \Exception;
use \Illuminate\Database\Eloquent\Collection;
use \Common\StateRange;
use \Alert\AlertController;
use \Dmkt\Reason;
use \Dmkt\Activity;
use \Common\TypePayment;
use \Common\TypeMoney;
use \Dmkt\InvestmentType;
use \Dmkt\Marca;
use \Parameter\Parameter;
use \Carbon\Carbon;
use \User;
use \Expense\ChangeRate;

class TestController extends BaseController 
{

	public function testCarbon()
	{
		$history = Solicitud::find( 3 )->lastHistory;
		return  ChangeRate::where( 'fecha' ,  Carbon::createFromFormat( 'Y-m-d H:i' , $history->updated_at )->subDay()->format( 'Y:m:d') )->first();
        
		$a = Carbon::now();
		return $a->format( 'd/m/Y' );
		return $a->day;
		$f = $a;
		$c = $f->lastOfMonth()->day;
		return $a->day;
	}

	public function changePass( $id )
	{
		$user = User::find( $id );
		$user->password = Hash::make( 'admin' );
		$user->save();
	}

	private function diffCreatedAt( $record1 , $record2 , $record3 )
	{
		$date1 = new Carbon( $record1->created_at );
		$date2 = new Carbon( $record2->created_at );
		$date3 = new Carbon( $record3->created_at );
		$min = $date1->min( $date2 , $date3 );
		$max = $date1->max( $date2 , $date3 );
		$rpta = $max->diffInDays( $min );
		return $rpta;
	}

	public function dt()
	{
		return '{
  
  "data": [
    [
      "Airi",
      "Satou",
      "Accountant",
      "Tokyo",
      "28th Nov 08",
      "$162,700",
      "Tokyo",
      "28th Nov 08",
      "Accountant",
      "$162,700"
    ],
    [
      "Angelica",
      "Ramos",
      "Chief Executive Officer (CEO)",
      "London",
      "9th Oct 09",
      "$1,200,000",
      "Tokyo",
      "28th Nov 08",
      "Accountant",
      "$162,700"
    ]
  ]
}';
	}

	public function testQuery()
	{
		try
		{
			/*$a = "fgsssssssssssssssssssssssssssssssssssssssssss";
			$query = 'SELECT a.titulo , a.CREATED_AT , b.type as "USUARIO" , d.DESCRIPCION as "PRODUCTO" 
					, f.ID_CLIENTE , g.DESCRIPCION as "TIPO_CLIENTE" , h.NOMBRE as "RUBRO"
					FROM SOLICITUD a 
					LEFT JOIN OUTDVP.USERS b on a.CREATED_BY = b.id
					LEFT JOIN SOLICITUD_PRODUCTO c on c.ID_SOLICITUD = a.id
					LEFT JOIN OUTDVP.MARCAS d on d.id = c.ID_PRODUCTO
					LEFT JOIN SOLICITUD_CLIENTE f on f.ID_SOLICITUD = a.id
					LEFT JOIN TIPO_CLIENTE g on g.ID = f.ID_TIPO_CLIENTE
					LEFT JOIN TIPO_ACTIVIDAD h on h.id = a.ID_ACTIVIDAD
					where idtiposolicitud = 1';

			$query = DB::select( DB::raw( $query ) ); */
			$frecuency = 'N';

			$fromDate = '2015/04/18';
			$toDate = '2015/07/30';

			/* $q = 'Select ' . ( $frecuency == 'M' ? "('SEMANA ' || to_char( z.the_date , 'IW' ) )" : 'TO_CHAR( ' . 
				( $frecuency == 'S' ? 'z.the_date' : 'a.created_at' ) . " , 'YYYY/MM/DD' )" ) ." as FECHA, a.titulo , b.type as USUARIO , d.DESCRIPCION as PRODUCTO 
							, f.ID_CLIENTE , g.DESCRIPCION as TIPO_CLIENTE , h.NOMBRE as RUBRO , ROUND( a.updated_at - a.created_at , 2 )  DIAS , c.MONTO_ASIGNADO , q.detalle DETALLE ,
							CASE 
							WHEN g.DESCRIPCION = 'MEDICO' THEN i.pefnombres || ' ' || i.pefpaterno || ' ' || i.pefmaterno
							WHEN g.DESCRIPCION = 'FARMACIA' THEN j.pejrazon
							WHEN g.DESCRIPCION = 'INSTITUCION' THEN k.pejrazon 
							WHEN g.DESCRIPCION = 'DISTRIBUIDOR' OR g.DESCRIPCION = 'BODEGA' THEN l.clnombre
							ELSE 'No Identificado'END as CLIENTE ,
							CASE 
							WHEN b.type = 'R' THEN m.nombres
							WHEN b.type = 'S' THEN n.nombres
							WHEN b.type = 'P' THEN o.descripcion
							WHEN b.type IN ( 'GC' , 'GP' , 'T' , 'C' , 'AG' )  THEN p.nombres
							ELSE 'No Identificado'END as USUARIO
							FROM ". ($frecuency == 'S' || $frecuency == 'M' ? " ( 
							SELECT to_date('$fromDate','YYYY/MM/DD')+level-1 as the_date 
							FROM dual connect by level <= to_date('$toDate','YYYY/MM/DD') - to_date('$fromDate','YYYY/MM/DD') + 1) z LEFT JOIN SOLICITUD a ON TO_DATE ( to_char( a.created_at , 'YYYY/MM/DD' ) , 'YYYY/MM/DD' )  = z.the_date " : "SOLICITUD a") ." 
							LEFT JOIN OUTDVP.USERS b on a.CREATED_BY = b.id
							LEFT JOIN OUTDVP.DMKT_RG_RM m on b.id = m.IDUSER
							LEFT JOIN OUTDVP.DMKT_RG_SUPERVISOR n ON b.id = n.IDUSER
							LEFT JOIn OUTDVP.GERENTES o ON b.id= o.IDUSER
							LEFT JOIN OUTDVP.PERSONAS p ON b.id = p.IDUSER
							LEFT JOIN SOLICITUD_DETALLE q on a.id_detalle = q.id
							LEFT JOIN SOLICITUD_PRODUCTO c on c.ID_SOLICITUD = a.id
							LEFT JOIN OUTDVP.MARCAS d on d.id = c.ID_PRODUCTO
							LEFT JOIN SOLICITUD_CLIENTE f on f.ID_SOLICITUD = a.id
							LEFT JOIN TIPO_CLIENTE g on g.ID = f.ID_TIPO_CLIENTE
							LEFT JOIN TIPO_ACTIVIDAD h on h.id = a.ID_ACTIVIDAD
							LEFT JOIN FICPE.PERSONAFIS i on i.pefcodpers = f.ID_CLIENTE  
							LEFT JOIN FICPEF.PERSONAJUR j on j.PEJCODPERS = f.ID_CLIENTE
							LEFT JOIN FICPE.PERSONAJUR k on k.PEJCODPERS = f.ID_CLIENTE
							LEFT JOIN VTADIS.CLIENTES l on l.CLCODIGO = f.ID_CLIENTE ". 
							($frecuency == 'S' ? "" : "WHERE a.created_at between to_date('".$fromDate."','yyyy/mm/dd') and to_date('".$toDate."','yyyy/mm/dd') ") . " " . ($frecuency == 'S' || $frecuency == 'M' ? 
							"ORDER BY z.the_date" : "")."";*/

			//$q = 'SELECT ' . ( $frecuency == 'M' ? "( 'SEMANA ' || to_char( zzz.the_date , 'IW' ) )" : 'TO_CHAR( ' . ( $frecuency == 'S' ? 'zzz.the_date' : 'a.created_at' ) . " , 'YYYY/MM/DD' )" ) . "  FECHA , a.TITULO , n.NOMBRE TIPO_SOLICITUD , u.NOMBRE INVERSION , h.NOMBRE RUBRO ,   CASE     WHEN b.TYPE = 'R' THEN o.nombres || ' ' || o.apellidos 		WHEN b.TYPE = 'S' THEN p.nombres || ' ' || p.apellidos 		WHEN b.TYPE = 'P' THEN q.descripcion 		WHEN b.TYPE IN ( 'GC' , 'GP' , 'T' , 'C' , 'AG' ) THEN r.nombres || ' ' || r.apellidos     ELSE b.TYPE   END CREADO_POR ,   m.DESCRIPCION  TIPO_USUARIO , d.DESCRIPCION PRODUCTO ,  e.DESCRIPCION LINEA_PRODUCTO ,   CASE 		WHEN g.DESCRIPCION = 'MEDICO' THEN i.PEFNOMBRES || ' ' || i.PEFPATERNO || ' ' || i.PEFMATERNO 		WHEN g.DESCRIPCION = 'FARMACIA' THEN j.PEJRAZON 		WHEN g.DESCRIPCION = 'INSTITUCION' THEN k.PEJRAZON 		WHEN g.DESCRIPCION = 'DISTRIBUIDOR' OR g.DESCRIPCION = 'BODEGA' THEN l.CLNOMBRE 		ELSE 'No Identificado'   END CLIENTE ,   g.DESCRIPCION TIPO_CLIENTE , ROUND( a.updated_at - a.created_at , 2 ) DIAS_DURACION , c.MONTO_ASIGNADO MONTO_PRODUCTO,   ltrim( regexp_substr( s.DETALLE , '\"monto_solicitado\":(\s*)(\"*)(\s*)[[:digit:]]*' , 1 , 1 , 'i' ) , '\"monto_solicitado\":' ) MONTO_SOLICITADO ,   ltrim( regexp_substr( s.DETALLE , '\"monto_aprobado\":(\s*)(\"*)(\s*)[[:digit:]]*' , 1 , 1 , 'i' ) , '\"monto_aprobado\":' ) MONTO_APROBADO , bz.N1GDESCRIPCION PAIS , cz.N2GDESCRIPCION AREA , dz.N3GDESCRIPCION ZONA , ez.N4GDESCRIPCION DISTRITO  FROM ". ($frecuency == 'S' || $frecuency == 'M' ? " (  SELECT to_date('$fromDate','YYYY/MM/DD')+level-1 as the_date FROM dual connect by level <= to_date('$toDate','YYYY/MM/DD') - to_date('$fromDate','YYYY/MM/DD') + 1) zzz LEFT JOIN SOLICITUD a ON TO_DATE ( to_char( a.created_at , 'YYYY/MM/DD' ) , 'YYYY/MM/DD' )  = zzz.the_date " : "SOLICITUD a ") ." LEFT JOIN OUTDVP.USERS b on a.CREATED_BY = b.id LEFT JOIN SOLICITUD_PRODUCTO c ON c.ID_SOLICITUD = a.ID LEFT JOIN OUTDVP.MARCAS d ON d.ID = c.ID_PRODUCTO LEFT JOIN OUTDVP.LINEAS e ON d.LINEA_ID = e.ID LEFT JOIN SOLICITUD_CLIENTE f ON f.ID_SOLICITUD = a.ID LEFT JOIN TIPO_CLIENTE g ON g.ID = f.ID_TIPO_CLIENTE LEFT JOIN TIPO_ACTIVIDAD h ON h.ID = a.ID_ACTIVIDAD LEFT JOIN FICPE.PERSONAFIS i ON i.PEFCODPERS = f.ID_CLIENTE  LEFT JOIN FICPEF.PERSONAJUR j ON j.PEJCODPERS = f.ID_CLIENTE LEFT JOIN FICPE.PERSONAJUR k ON k.PEJCODPERS = f.ID_CLIENTE LEFT JOIN VTADIS.CLIENTES l ON l.CLCODIGO = f.ID_CLIENTE LEFT JOIN OUTDVP.TIPO_USUARIO m ON m.CODIGO = b.TYPE LEFT JOIN SOLICITUD_TIPO n ON n.ID = a.IDTIPOSOLICITUD LEFT JOIN OUTDVP.DMKT_RG_RM o on b.ID = o.IDUSER LEFT JOIN OUTDVP.DMKT_RG_SUPERVISOR p ON b.ID = p.IDUSER lEFT JOIN OUTDVP.GERENTES q ON b.ID = q.IDUSER LEFT JOIN OUTDVP.PERSONAS r ON b.ID = r.IDUSER LEFT JOIN SOLICITUD_DETALLE s ON a.ID_DETALLE = s.ID LEFT JOIN TIPO_INVERSION u ON a.ID_INVERSION = u.ID LEFT JOIN SOLICITUD_GERENTE v ON v.ID_SOLICITUD = a.ID AND v.ID_GERPROD =  " . Auth::user()->id . "  LEFT JOIN OUTDVP.GERENTES w ON w.ID = d.GERENTE_ID LEFT JOIN OUTDVP.DMKT_RG_SUPERVISOR x ON x.IDSUP = o.IDSUP LEFT JOIN OUTDVP.DMKT_RG_RM y ON y.IDUSER = a.ID_USER_ASSIGN LEFT JOIN OUTDVP.DMKT_RG_SUPERVISOR z ON z.IDSUP = y.IDSUP LEFT JOIN FICPE.VISITADOR az ON o.IDRM = az.VISVISITADOR LEFT JOIN FICPE.NIVEL1GEOG bz on bz.N1GNIVEL1GEOG = az.VISNIVEL1GEOG LEFT JOIN FICPE.NIVEL2GEOG cz on cz.N2GNIVEL2GEOG = az.VISNIVEL2GEOG LEFT JOIN FICPE.NIVEL3GEOG dz on dz.N3GNIVEL3GEOG =  az.VISNIVEL3GEOG LEFT JOIN FICPE.NIVEL4GEOG ez on ez.N4GNIVEL4GEOG = az.VISNIVEL4GEOG WHERE   ( a.CREATED_BY =  " . Auth::user()->id . "  OR a.ID_USER_ASSIGN =  " . Auth::user()->id . "   OR v.ID_GERPROD =  " . Auth::user()->id . "   OR q.IDUSER =  " . Auth::user()->id . "  OR p.IDUSER =  " . Auth::user()->id . "  OR z.IDUSER =  " . Auth::user()->id . "  )  AND a.ID_ESTADO = 7 " . ( $frecuency == 'S' ? "" : "AND a.created_at between to_date('" .$fromDate . "','yyyy/mm/dd') and to_date('".$toDate."','yyyy/mm/dd') ") . " " . ($frecuency == 'S' || $frecuency == 'M' ? "ORDER BY zzz.the_date" : "" ). "";
			//$q = 'SELECT ' . ( $frecuency == 'M' ? "( 'SEMANA ' || to_char( zzz.the_date , 'IW' ) )" : 'TO_CHAR( ' . ( $frecuency == 'S' ? 'zzz.the_date' : 'a.created_at' ) . " , 'YYYY/MM/DD' )" ) . "  FECHA , a.TITULO , n.NOMBRE TIPO_SOLICITUD , u.NOMBRE INVERSION , h.NOMBRE RUBRO ,   CASE WHEN b.TYPE = 'R' THEN o.nombres || ' ' || o.apellidos 		WHEN b.TYPE = 'S' THEN p.nombres || ' ' || p.apellidos 		WHEN b.TYPE = 'P' THEN q.descripcion 		WHEN b.TYPE IN ( 'GC' , 'GP' , 'T' , 'C' , 'AG' ) THEN r.nombres || ' ' || r.apellidos     ELSE b.TYPE   END CREADO_POR ,   m.DESCRIPCION  TIPO_USUARIO , d.DESCRIPCION PRODUCTO ,  e.DESCRIPCION LINEA_PRODUCTO ,   CASE 		WHEN g.DESCRIPCION = 'MEDICO' THEN i.PEFNOMBRES || ' ' || i.PEFPATERNO || ' ' || i.PEFMATERNO 		WHEN g.DESCRIPCION = 'FARMACIA' THEN j.PEJRAZON 		WHEN g.DESCRIPCION = 'INSTITUCION' THEN k.PEJRAZON 		WHEN g.DESCRIPCION = 'DISTRIBUIDOR' OR g.DESCRIPCION = 'BODEGA' THEN l.CLNOMBRE 		ELSE 'No Identificado'   END CLIENTE ,   g.DESCRIPCION TIPO_CLIENTE , CASE WHEN ( a.updated_at - a.created_at ) < 1 AND ( a.updated_at - a.created_at ) > 0 THEN LPAD ( ROUND( a.updated_at - a.created_at , 2 ) , LENGTH( ROUND( a.updated_at - a.created_at , 2 ) ) + 1 , '0' ) ELSE TO_CHAR( ROUND( a.updated_at - a.created_at , 2 ) ) END DIAS_DURACION , c.MONTO_ASIGNADO MONTO_PRODUCTO,   ltrim( regexp_substr( s.DETALLE , '\"monto_solicitado\":(\s*)(\"*)(\s*)[[:digit:]]*' , 1 , 1 , 'i' ) , '\"monto_solicitado\":' ) MONTO_SOLICITADO ,   ltrim( regexp_substr( s.DETALLE , '\"monto_aprobado\":(\s*)(\"*)(\s*)[[:digit:]]*' , 1 , 1 , 'i' ) , '\"monto_aprobado\":' ) MONTO_APROBADO , bz.N1GDESCRIPCION PAIS , cz.N2GDESCRIPCION AREA , dz.N3GDESCRIPCION ZONA , ez.N4GDESCRIPCION DISTRITO  FROM ". ($frecuency == 'S' || $frecuency == 'M' ? " (  SELECT to_date('$fromDate','YYYY/MM/DD')+level-1 as the_date FROM dual connect by level <= to_date('$toDate','YYYY/MM/DD') - to_date('$fromDate','YYYY/MM/DD') + 1) zzz LEFT JOIN SOLICITUD a ON TO_DATE ( to_char( a.created_at , 'YYYY/MM/DD' ) , 'YYYY/MM/DD' )  = zzz.the_date " : "SOLICITUD a ") ." LEFT JOIN OUTDVP.USERS b on a.CREATED_BY = b.id LEFT JOIN SOLICITUD_PRODUCTO c ON c.ID_SOLICITUD = a.ID LEFT JOIN OUTDVP.MARCAS d ON d.ID = c.ID_PRODUCTO LEFT JOIN OUTDVP.LINEAS e ON d.LINEA_ID = e.ID LEFT JOIN SOLICITUD_CLIENTE f ON f.ID_SOLICITUD = a.ID LEFT JOIN TIPO_CLIENTE g ON g.ID = f.ID_TIPO_CLIENTE LEFT JOIN TIPO_ACTIVIDAD h ON h.ID = a.ID_ACTIVIDAD LEFT JOIN FICPE.PERSONAFIS i ON i.PEFCODPERS = f.ID_CLIENTE  LEFT JOIN FICPEF.PERSONAJUR j ON j.PEJCODPERS = f.ID_CLIENTE LEFT JOIN FICPE.PERSONAJUR k ON k.PEJCODPERS = f.ID_CLIENTE LEFT JOIN VTADIS.CLIENTES l ON l.CLCODIGO = f.ID_CLIENTE LEFT JOIN OUTDVP.TIPO_USUARIO m ON m.CODIGO = b.TYPE LEFT JOIN SOLICITUD_TIPO n ON n.ID = a.IDTIPOSOLICITUD LEFT JOIN OUTDVP.DMKT_RG_RM o on b.ID = o.IDUSER LEFT JOIN OUTDVP.DMKT_RG_SUPERVISOR p ON b.ID = p.IDUSER lEFT JOIN OUTDVP.GERENTES q ON b.ID = q.IDUSER LEFT JOIN OUTDVP.PERSONAS r ON b.ID = r.IDUSER LEFT JOIN SOLICITUD_DETALLE s ON a.ID_DETALLE = s.ID LEFT JOIN TIPO_INVERSION u ON a.ID_INVERSION = u.ID LEFT JOIN SOLICITUD_GERENTE v ON v.ID_SOLICITUD = a.ID AND v.ID_GERPROD =  " . Auth::user()->id . "  LEFT JOIN OUTDVP.GERENTES w ON w.ID = d.GERENTE_ID LEFT JOIN OUTDVP.DMKT_RG_SUPERVISOR x ON x.IDSUP = o.IDSUP LEFT JOIN OUTDVP.DMKT_RG_RM y ON y.IDUSER = a.ID_USER_ASSIGN LEFT JOIN OUTDVP.DMKT_RG_SUPERVISOR z ON z.IDSUP = y.IDSUP LEFT JOIN FICPE.VISITADOR az ON o.IDRM = az.VISVISITADOR LEFT JOIN FICPE.NIVEL1GEOG bz on bz.N1GNIVEL1GEOG = az.VISNIVEL1GEOG LEFT JOIN FICPE.NIVEL2GEOG cz on cz.N2GNIVEL2GEOG = az.VISNIVEL2GEOG LEFT JOIN FICPE.NIVEL3GEOG dz on dz.N3GNIVEL3GEOG =  az.VISNIVEL3GEOG LEFT JOIN FICPE.NIVEL4GEOG ez on ez.N4GNIVEL4GEOG = az.VISNIVEL4GEOG WHERE a.ID_ESTADO = 7 " . ( in_array( Auth::user()->id , array( REP_MED , SUP , GER_PROD , GER_PROM ) ) ? ' AND ( a.CREATED_BY =  ' . Auth::user()->id . "  OR a.ID_USER_ASSIGN =  " . Auth::user()->id . "   OR v.ID_GERPROD =  " . Auth::user()->id . "   OR q.IDUSER =  " . Auth::user()->id . "  OR p.IDUSER =  " . Auth::user()->id . "  OR z.IDUSER =  " . Auth::user()->id . "  ) " : ( is_null( Auth::user()->simApp ) ) ? ' AND a.created_by = 0 ' : ' ' ) . ($frecuency == 'S' ? "" : " AND a.created_at between to_date('" .$fromDate . "','yyyy/mm/dd') and to_date('".$toDate."','yyyy/mm/dd') ") . " " . ($frecuency == 'S' || $frecuency == 'M' ? "ORDER BY zzz.the_date" : "" ). "";
			
			//return $q;
			$q = 'SELECT ' . ( $frecuency == 'M' ? "( 'SEMANA ' || to_char( zzz.the_date , 'IW' ) )" : 'TO_CHAR( ' . ( $frecuency == 'S' ? 'zzz.the_date' : 'a.created_at' ) . " , 'YYYY/MM/DD' )" ) . "  FECHA , a.TITULO , n.NOMBRE TIPO_SOLICITUD , u.NOMBRE INVERSION , h.NOMBRE RUBRO ,   CASE WHEN b.TYPE = 'R' THEN o.nombres || ' ' || o.apellidos 		WHEN b.TYPE = 'S' THEN p.nombres || ' ' || p.apellidos 		WHEN b.TYPE = 'P' THEN q.descripcion 		WHEN b.TYPE IN ( 'GC' , 'GP' , 'T' , 'C' , 'AG' ) THEN r.nombres || ' ' || r.apellidos     ELSE b.TYPE   END CREADO_POR ,   m.DESCRIPCION  TIPO_USUARIO , d.DESCRIPCION PRODUCTO ,  e.DESCRIPCION LINEA_PRODUCTO ,   CASE 		WHEN g.DESCRIPCION = 'MEDICO' THEN i.PEFNOMBRES || ' ' || i.PEFPATERNO || ' ' || i.PEFMATERNO 		WHEN g.DESCRIPCION = 'FARMACIA' THEN j.PEJRAZON 		WHEN g.DESCRIPCION = 'INSTITUCION' THEN k.PEJRAZON 		WHEN g.DESCRIPCION = 'DISTRIBUIDOR' OR g.DESCRIPCION = 'BODEGA' THEN l.CLNOMBRE 		ELSE 'No Identificado'   END CLIENTE ,   g.DESCRIPCION TIPO_CLIENTE , CASE WHEN ( a.updated_at - a.created_at ) < 1 AND ( a.updated_at - a.created_at ) > 0 THEN LPAD ( ROUND( a.updated_at - a.created_at , 2 ) , LENGTH( ROUND( a.updated_at - a.created_at , 2 ) ) + 1 , '0' ) ELSE TO_CHAR( ROUND( a.updated_at - a.created_at , 2 ) ) END DIAS_DURACION , c.MONTO_ASIGNADO MONTO_PRODUCTO,   ltrim( regexp_substr( s.DETALLE , '\"monto_solicitado\":(\s*)(\"*)(\s*)[[:digit:]]*' , 1 , 1 , 'i' ) , '\"monto_solicitado\":' ) MONTO_SOLICITADO ,   ltrim( regexp_substr( s.DETALLE , '\"monto_aprobado\":(\s*)(\"*)(\s*)[[:digit:]]*' , 1 , 1 , 'i' ) , '\"monto_aprobado\":' ) MONTO_APROBADO , bz.N1GDESCRIPCION PAIS , cz.N2GDESCRIPCION AREA , dz.N3GDESCRIPCION ZONA , ez.N4GDESCRIPCION DISTRITO  FROM ". ($frecuency == 'S' || $frecuency == 'M' ? " (  SELECT to_date('$fromDate','YYYY/MM/DD')+level-1 as the_date FROM dual connect by level <= to_date('$toDate','YYYY/MM/DD') - to_date('$fromDate','YYYY/MM/DD') + 1) zzz LEFT JOIN SOLICITUD a ON TO_DATE ( to_char( a.created_at , 'YYYY/MM/DD' ) , 'YYYY/MM/DD' )  = zzz.the_date " : "SOLICITUD a ") ." LEFT JOIN OUTDVP.USERS b on a.CREATED_BY = b.id LEFT JOIN SOLICITUD_PRODUCTO c ON c.ID_SOLICITUD = a.ID LEFT JOIN OUTDVP.MARCAS d ON d.ID = c.ID_PRODUCTO LEFT JOIN OUTDVP.LINEAS e ON d.LINEA_ID = e.ID LEFT JOIN SOLICITUD_CLIENTE f ON f.ID_SOLICITUD = a.ID LEFT JOIN TIPO_CLIENTE g ON g.ID = f.ID_TIPO_CLIENTE LEFT JOIN TIPO_ACTIVIDAD h ON h.ID = a.ID_ACTIVIDAD LEFT JOIN FICPE.PERSONAFIS i ON i.PEFCODPERS = f.ID_CLIENTE  LEFT JOIN FICPEF.PERSONAJUR j ON j.PEJCODPERS = f.ID_CLIENTE LEFT JOIN FICPE.PERSONAJUR k ON k.PEJCODPERS = f.ID_CLIENTE LEFT JOIN VTADIS.CLIENTES l ON l.CLCODIGO = f.ID_CLIENTE LEFT JOIN OUTDVP.TIPO_USUARIO m ON m.CODIGO = b.TYPE LEFT JOIN SOLICITUD_TIPO n ON n.ID = a.IDTIPOSOLICITUD LEFT JOIN OUTDVP.DMKT_RG_RM o on b.ID = o.IDUSER LEFT JOIN OUTDVP.DMKT_RG_SUPERVISOR p ON b.ID = p.IDUSER lEFT JOIN OUTDVP.GERENTES q ON b.ID = q.IDUSER LEFT JOIN OUTDVP.PERSONAS r ON b.ID = r.IDUSER LEFT JOIN SOLICITUD_DETALLE s ON a.ID_DETALLE = s.ID LEFT JOIN TIPO_INVERSION u ON a.ID_INVERSION = u.ID LEFT JOIN SOLICITUD_GERENTE v ON v.ID_SOLICITUD = a.ID AND v.ID_GERPROD =  " . Auth::user()->id . "  LEFT JOIN OUTDVP.GERENTES w ON w.ID = d.GERENTE_ID LEFT JOIN OUTDVP.DMKT_RG_SUPERVISOR x ON x.IDSUP = o.IDSUP LEFT JOIN OUTDVP.DMKT_RG_RM y ON y.IDUSER = a.ID_USER_ASSIGN LEFT JOIN OUTDVP.DMKT_RG_SUPERVISOR z ON z.IDSUP = y.IDSUP LEFT JOIN FICPE.VISITADOR az ON o.IDRM = az.VISVISITADOR LEFT JOIN FICPE.NIVEL1GEOG bz on bz.N1GNIVEL1GEOG = az.VISNIVEL1GEOG LEFT JOIN FICPE.NIVEL2GEOG cz on cz.N2GNIVEL2GEOG = az.VISNIVEL2GEOG LEFT JOIN FICPE.NIVEL3GEOG dz on dz.N3GNIVEL3GEOG =  az.VISNIVEL3GEOG LEFT JOIN FICPE.NIVEL4GEOG ez on ez.N4GNIVEL4GEOG = az.VISNIVEL4GEOG WHERE a.ID_ESTADO = 7 " . ( in_array( Auth::user()->type , array( REP_MED , SUP , GER_PROD , GER_PROM ) ) ? ' AND ( a.CREATED_BY =  ' . Auth::user()->id . "  OR a.ID_USER_ASSIGN =  " . Auth::user()->id . "   OR v.ID_GERPROD =  " . Auth::user()->id . "   OR q.IDUSER =  " . Auth::user()->id . "  OR p.IDUSER =  " . Auth::user()->id . "  OR z.IDUSER =  " . Auth::user()->id . "  ) " : ( ( is_null( Auth::user()->simApp ) ) ? ' AND a.created_by = 0 ' : ' ' ) ) . ($frecuency == 'S' ? "" : " AND a.created_at between to_date('" .$fromDate . "','yyyy/mm/dd') and to_date('".$toDate."','yyyy/mm/dd') ") . " " . ($frecuency == 'S' || $frecuency == 'M' ? "ORDER BY zzz.the_date" : "" ). "";
			$results = DB::select( DB::raw( $q ) );
			/*foreach ( $results as $result )
			{
				$jDetalle = json_decode( $result->detalle );
				if ( isset( $jDetalle->monto_aprobado ) )
					$result->monto_aprobado = $jDetalle->monto_aprobado;
				else
					$result->monto_aprobado = 0;
				if ( isset( $jDetalle->monto_solicitado ) )
					$result->monto_solicitado = $jDetalle->monto_solicitado;
				else
					$result->monto_solicitado = 0;
				unset( $result->detalle );
			}*/
			return $results;
			$sol = Solicitud::getAllData();
			return $sol;
			return $query;
		}
		catch( Exception $e )
		{
			Log::error( $e );
			return $e;
		}
	}


	public function tm()
	{
		$id = 3;
		$models = Solicitud::with('state.rangeState')->whereHas('state', function ($q) use($id)
		{
			$q->whereHas('rangeState', function ($t) use($id)
			{
				$t->where('id',$id);
			});
		})->get();
		
		return $models;
	}

	public function withHistory()
	{
		$data = array('alias' => 'BANCOS');
		


		$account = Account::firstOrNew($data);
		if (isset($account->rn))
			return 'true';
		else
			return $account;

		$account->idcuenta = $account->searchId() + 1;
		$account->save();
		return $account;


	}

	private function clientsTables()
	{
		$tables = array('VTA.CLIENTES','SIP.MEDICOS2');
		$wheres = array('clcodigo,clnombre,clrutholding','rutmed,patmed,matmed,nommed');
		$selects = array('clcodigo,clnombre',"rutmed,(patmed||' '||matmed||' '||nommed)");
		$sJson = array();
		for ($i=0;$i<count($tables) ;$i++)
		{
			$tab = array();
			$tab['name'] = $tables[$i];
			$where = explode(',',$wheres[$i]);
			$tab['wheres'] = $where;
			$select = explode(',',$selects[$i]);
			$tab['selects'] = $select;
			$sJson[] = $tab;
		}
		return json_encode($sJson);
	}

	public function clientSource()
	{
		try
		{
			$inputs = Input::all();
			$json = '[{"name":"FICPE.PERSONAFIS","wheres":{"likes":["PEFNRODOC1","(PEFNOMBRES || \' \' || PEFPATERNO || \' \' || PEFMATERNO)"],"equal":[{"PEFESTADO":1}]},"selects":["PEFCODPERS","(\'DOCTOR: \' || PEFNRODOC1 || \'-\' || PEFNOMBRES || \' \' || PEFPATERNO || \' \' || PEFMATERNO)"]},{"name":"FICPEF.PERSONAJUR","wheres":{"likes":["PEJNRODOC","PEJRAZON"],"equal":[{"PEJESTADO":1}]},"selects":["PEJCODPERS","(\'CENTRO: \' || PEJNRODOC || \'-\' || PEJRAZON)"]}]';
	    	$rpta = $this->searchSeeker($inputs['sVal'],$json);
		}
		catch (Exception $e)
		{
			$rpta = $this->internalException($e,__FUNCTION__);
		}
		return Response::Json($rpta);

	}


    private function searchSeeker($inputs,$json)
    {
    	try
    	{
	    	if (!empty($inputs))
	    	{
		    	$json = json_decode($json);
	    		$cAlias = array('value','label');
		    	if (json_last_error() == JSON_ERROR_NONE)
		    	{
			    	$array = array();
			    	foreach ($json as $table)
			    	{
			    		$select = '';
			    		$query = DB::table($table->name);
			    		foreach ( $table->wheres->likes as $like)
			    			$query->orWhereRaw(" UPPER(" .$like. ") like '%" .strtoupper($inputs). "%' ");
			    		for ( $i=0; $i<2; $i++)
			    			$select = $select. ' ' .$table->selects[$i]. ' as "' .$cAlias[$i]. '",';				
			    		$select = substr($select,0,-1);
			    		$query->select(DB::raw($select));
			    		$query->take(4);
			    		$tms = $query->get();
			    		for ($i=0; $i < count($tms); $i++)
			    			$tms[$i]->table = $table->name;
			    		$array = array_merge($tms,$array);
			    		
			    	}
			    	$rpta = $this->setRpta($array);
			    }
			    else
			    {
			    	$rpta = array(status => warning , description => 'Json: Formato Incorrecto');
		    	}
		    }
		    else
		    {
		    	$rpta = array(status => warning , description => 'Post Input "JSON" Vacio');   
		    }
	    }
	    catch (Exception $e)
	   	{
	    	$rpta = $this->internalException($e,__FUNCTION__);
    	}
    	return $rpta;
    }

    private function intersectRecords( $rs1 , $rs2 )
    {
    	$intersect = new Collection;
    	foreach( $rs1 as $r1 )
    	{
    		foreach( $rs2 as $r2 )
    		{
    			if ( $r2->id_cliente == $r1->id_cliente && $r2->id_tipo_cliente == $r1->id_tipo_cliente )
    			{
    				$intersect->add( $r2 );
    				break;
    			}
    		}
    	}
    	return $intersect;
    }

    public function testalert()
    {
    	$tipo_cliente_requerido = array( MEDICO , INSTITUCION );
		$solicituds = Solicitud::all();
		foreach ( $solicituds as $key => $solicitud )
		{
			$clients = $solicitud->clients;
			$solicitud_tipo_cliente = array_unique( $clients->lists( 'id_tipo_cliente') );
			if ( count( array_intersect( $solicitud_tipo_cliente, $tipo_cliente_requerido ) ) <= 1 )
				unset( $solicituds[ $key ] );
		}
		$clientList = array();
		foreach( $solicituds as $solicitud_inicial )
		{
			$clients_inicial = $solicitud_inicial->clients()->select( 'id_cliente' , 'id_tipo_cliente' )->get();
			foreach ( $solicituds as $solicitud_secundaria )
			{
				if ( $solicitud_inicial->id != $solicitud_secundaria->id )
				{
					$clients_secundaria = $solicitud_secundaria->clients()->select( 'id_cliente' , 'id_tipo_cliente' )->get();
					//return $clients_inicial->toJson() . '        ' . $clients_secundaria->toJson();
					//return array_intersect( $clients_inicial->toArray() , $clients_secundaria->toArray() );
					//return $clients_inicial->intersect( $clients_secundaria );
					$cliente_inicial = $this->intersectRecords( $clients_inicial , $clients_secundaria );
					//return $cliente_inicial;
					$solicitud_tipo_cliente = array_unique( $clients_inicial->lists( 'id_tipo_cliente' ) );
					if ( count( array_intersect( $solicitud_tipo_cliente, $tipo_cliente_requerido ) ) >= 2 )
					{
						$cliente = '';
						foreach ( $clients_inicial as $client_inicial )
						{
							$cliente .= $client_inicial->{$client_inicial->clientType->relacion}->full_name . '. ' ; 
						}
						return 'La solicitud ' . $solicitud_inicial->id . ' y la solicitud ' . $solicitud_secundaria->id . ' tienen por lo menos un cliente medico e institucion iguales: ' .$cliente;
						
					}
					/*foreach ( $solicituds as $solicitud_final )
					{
						if ( $solicitud_inicial->id != $solicitud_final->id || $solicitud_secundaria != $solicitud_final->id )
						{

						}
					}*/
				}
			}
		}
    }

    public function passLogin()
    {
    	$user = User::find(41);
    	Auth::login($user);
    	if ( Session::has('state') )
            $state = Session::get('state');
        else
        {
            if ( Auth::user()->type == CONT )
                $state = R_APROBADO ;
            else if ( in_array( Auth::user()->type , array( REP_MED , SUP , GER_PROD , GER_PROM , GER_COM , ASIS_GER ) ) )
                $state = R_PENDIENTE;
            elseif ( Auth::user()->type == TESORERIA )
                $state = R_REVISADO ;
        }
        $mWarning = array();
        if ( Session::has('warnings') )
        {
            $warnings = Session::pull('warnings');
            $mWarning[status] = ok ;
            if (!is_null($warnings))
                foreach ($warnings as $key => $warning)
                     $mWarning[data] = $warning[0].' ';
            $mWarning[data] = substr($mWarning[data],0,-1);
        }
        $data = array( 'state'  => $state , 'states' => StateRange::order() , 'warnings' => $mWarning );
        if ( Auth::user()->type == TESORERIA )
        {
            $data['tc'] = ChangeRate::getTc();    
            $data['banks'] = Account::banks();
        }
        elseif ( Auth::user()->type == ASIS_GER )
        {
            $data['fondos']  = Fondo::asisGerFondos();                
            $data['activities'] = Activity::order();
        }
        elseif ( Auth::user()->type == CONT )
        {
            $data['proofTypes'] = ProofType::order();
            $data['regimenes'] = Regimen::all();      
        }
        if ( Session::has( 'id_solicitud') )
        {
            $solicitud = Solicitud::find( Session::pull( 'id_solicitud' ) );
            $solicitud->status = ACTIVE ;
            $solicitud->save();
        }
        $alert = new AlertController;
        $data[ 'alert' ] = $alert->alertConsole();
        return View::make('template.User.show',$data);   
    }

    public function getUserSubFondos()
    {
    	$id = 35;
    	/*$user = \User::find( $id );
    	//$marca_id = 16;
    	$tipo = FONDO_SUBCATEGORIA_GERPROD;*/
    	$user = Auth::user();
    	if ( $user->type != SUP )
			return DB::table('Fondos f')->select( "m.descripcion || ' | ' || fc.descripcion || ' | ' || fsc.descripcion descripcion" , 'f.saldo saldo' )
			->leftJoin( 'fondos_subcategorias fsc' , 'f.fondos_subcategoria_id' , '=' , 'fsc.id' )
			->leftJoin( 'fondos_categorias fc' , 'fsc.fondos_categorias_id' , '=' , 'fc.id' )
			->leftJoin( 'outdvp.marcas m' , 'f.marca_id' , '=' , 'm.id' )
			->where( function( $query ) use( $user )
			{
				if ( $user->type == GER_PROD )
					$query->where( 'm.gerente_id' , $user->gerProd->id )->where( 'tipo' , FONDO_SUBCATEGORIA_GERPROD );
				elseif( $user->type == ASIS_GER )
					$query->where( 'fsc.tipo' , 'I' );
				else
					$query->where( 'fsc.tipo' , 'NNN' );
			})->get();
		else
			return DB::table('fondos_supervisor fs')
			->select( "m.descripcion || ' | ' || fc.descripcion || ' | ' || fsc.descripcion descripcion" , 'fs.saldo saldo' , 'fsc.id' , 'fs.marca_id marca_id' )
			->leftJoin( 'fondos_subcategorias fsc' , 'fsc.id' , '=' , 'fs.subcategoria_id' )
			->leftJoin( 'fondos_categorias fc' , 'fc.id' , '=' , 'fsc.fondos_categorias_id' )
			->leftJoin( 'outdvp.marcas m' , 'fs.marca_id' , '=' , 'm.id' )
			->where( function( $query ) use( $user )
			{
					$query->where( 'fsc.tipo' , 'S' )->where('fs.supervisor_id' , $user->id );
			})->get();
		//$subFondo = \Maintenance\FondosSubCategorias::getUserSubFondos( FONDO_SUBCATEGORIA_GERPROD , $user );
    }

    public function testcalert()
    {
    	$msg = '';
    	$tipo_cliente_requerido = array( MEDICO , INSTITUCION );
    	$tiempo = Parameter::find( ALERTA_INSTITUCION_CLIENTE );
		$solicituds = Solicitud::all();
		foreach ( $solicituds as $key => $solicitud )
		{
			$clients = $solicitud->clients;
			$solicitud_tipo_cliente = array_unique( $clients->lists( 'id_tipo_cliente') );
			if ( count( array_intersect( $solicitud_tipo_cliente, $tipo_cliente_requerido ) ) <= 1 )
				unset( $solicituds[ $key ] );
		}
		$clientList = array();
		$compare_second_id = array();
		$compare_initial_id = array();
		foreach( $solicituds as $solicitud_inicial )
		{
			foreach ( $solicituds as $solicitud_secundaria )
			{
				if ( $solicitud_secundaria->id != $solicitud_inicial->id && ( ! in_array( $solicitud_secundaria->id , $compare_initial_id ) ) )
				foreach( $solicituds as $solicitud_final )
				{
					if( $solicitud_final->id != $solicitud_secundaria->id && 
						$solicitud_final->id != $solicitud_inicial->id && 
						( ! in_array( $solicitud_final->id , $compare_second_id ) ) )
					{
						$clients_inicial = $solicitud_inicial->clients()->select( 'id_cliente' , 'id_tipo_cliente' )->get();
						$clients_secundaria = $solicitud_secundaria->clients()->select( 'id_cliente' , 'id_tipo_cliente' )->get();
						$clients_final = $solicitud_final->clients()->select( 'id_cliente' , 'id_tipo_cliente' )->get();
						//echo $solicitud_inicial->id . '|' . $solicitud_secundaria->id . '|' . $solicitud_final->id . '<br>';
						$intersect_client = $this->intersectRecords( $clients_inicial , $clients_secundaria );
						$intersect_client = $this->intersectRecords( $intersect_client , $clients_final );
						$solicitud_tipo_cliente = array_unique( $intersect_client->lists( 'id_tipo_cliente' ) );
						if ( count( array_intersect( $solicitud_tipo_cliente, $tipo_cliente_requerido ) ) >= 1 )
						{
							$cliente = '( ';
							foreach ( $intersect_client as $client_inicial )
							{
								$clienteArray[] = $client_inicial->{$client_inicial->clientType->relacion}->full_name;
								$cliente .= $client_inicial->{$client_inicial->clientType->relacion}->full_name .  ' , ' ;
							} 
							$cliente = rtrim( $cliente , ', ' );
							$cliente .= ' ).';
							$msg .= 'Las solicitudes ' . $solicitud_inicial->id . ' , ' . $solicitud_secundaria->id . ' , ' . $solicitud_final->id . ' ' . $tiempo->mensaje . ' ' . $cliente;
							$result[]=array(
								'cliente'    => $clienteArray,
								'solicitude' => array($solicitud_inicial->id, $solicitud_secundaria->id, $solicitud_final->id),
								'msg'     => $tiempo->mensaje
							);
						}
					}
				}
				$compare_second_id[] = $solicitud_secundaria->id;
			}
			$compare_second_id = array();
			$compare_initial_id[] = $solicitud_inicial->id;
		}
		return array( 'type' => 'warning' , 'msg' => $msg, 'data' => $result, 'typeData' => 'clientAlert');
	}

	public function getQuery()
	{
		
	}


}