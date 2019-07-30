<?php
namespace Expense;

use \BaseController;
use \ruc;
use \Input;

class RucController extends BaseController{
    public function show(){
        $inputs = Input::all();
    	$rucConsult = $inputs['ruc']; 
        if(strlen($rucConsult)<11)
    		return 0;
    	else
    	{
			$rucClass = new RUC;
			$data     = $rucClass->consultRUC($rucConsult);
			$reg = array();
			if(is_array($data))
			{
				foreach ($data as $key => $value)
					$reg [$key] = $this->encodingString($value);
				return $reg;
			}
			else
				return 1;
    	}
    }
    
    public function encodingString($stringData)
    {
    	foreach (mb_list_encodings() as $val) {
    		$stringUTF8 = mb_convert_encoding($stringData, 'UTF-8');
    	}
    	return $stringUTF8;
    }

}
