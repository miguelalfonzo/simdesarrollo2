<?php

namespace Dmkt;

use \Eloquent;

class CtaRm extends Eloquent 
{
    protected $table = TB_CUENTA_RM;
    protected $primaryKey = 'cl_codigo';

    public function cuenta($dni)
    {
    	try
    	{
    		$rpta = array();
    		$cta = CtaRm::WHERE('CODBENEFICIARIO',$dni)->WHERE('TIPO','H')->SELECT(TB_CUENTA)->first();
    		if (!$cta)
    			$cta = CtaRm::WHERE('CODBENEFICIARIO',$dni)->WHERE('TIPO','B')->SELECT(TB_CUENTA)->first();
    		$rpta[status] = ok;
    		$rpta[data] = $cta;
    	}
    	catch (Exception $e)
    	{
    		$rpta[status] = error;
    	}
    	return $rpta;
    }
}