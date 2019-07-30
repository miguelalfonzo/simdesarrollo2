<?php

class RUC{
	public function consultRuc($rucConsult){
		set_time_limit(60);
		$url = 'http://www.sunat.gob.pe/w/wapS01Alias?ruc='.$rucConsult;
		$proxy = 'proxy.bagoperu.com.pe:3128';
		$proxyauth = 'outinf01:uy349asx';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
		curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		$sunat = curl_exec($ch);
		curl_close($ch);
		
		preg_match_all("(<small>(.*?)</small>)", $sunat, $salida, PREG_PATTERN_ORDER);
		
		$i = 0;
		if (preg_match("(Ruc. </b>(.*?)-)", $salida[1][$i], $ruc) == 0){
			return $salida[1][$i];
		}else{
			$data['ruc'] = trim($ruc[1]);
			preg_match("(-(.*?)<br/>)", $salida[1][$i++], $razonSocial);
			$data['razonSocial'] = trim($razonSocial[1]);
			$data['rucAntiguo'] = ($salida[1][++$i] == '-')?'':trim($salida[1][$i]);
			$i++;
			preg_match("(</b>(.*))", $salida[1][$i++], $estado);
			$data['estado'] = trim($estado[1]);
			if (preg_match("(Nombre Comercial)", $salida[1][$i]) == 0){
				$data['exc'] = trim($salida[1][$i++]);
				preg_match("(<br/>(.*))", $salida[1][$i++], $nomComercial);
				$data['nomComercial'] = trim($nomComercial[1]) == '-'? '': trim($nomComercial[1]);
			}else{
				$data['exc'] = '';
				preg_match("(<br/>(.*))", $salida[1][$i++], $nomComercial);
				$data['nomComercial'] = trim($nomComercial[1]) == '-'? '': trim($nomComercial[1]);
			}
			preg_match("(<br/>(.*))", $salida[1][$i++], $direccion);
			$data['direccion'] = trim($direccion[1]);
			preg_match("(<b>(.*)</b>)", $salida[1][$i++], $situacion);
			$data['situacion'] = trim($situacion[1]);
			preg_match("(<br/>(.*))", $salida[1][$i++], $telefonos);
			$data['telefonos'] = trim($telefonos[1]) == '-'? '': trim($telefonos[1]);
			preg_match("(<br/>(.*))", $salida[1][$i++], $dependencia);
			$data['dependencia'] = trim($dependencia[1]);
			preg_match("(<br/>(.*))", $salida[1][$i++], $tipo);
			$data['tipo'] = trim($tipo[1]);

			return $data;
		}
	}
}