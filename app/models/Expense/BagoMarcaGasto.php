<?php

namespace Expense;

use \Eloquent;

class BagoMarcaGasto extends Eloquent
{
    protected $table= 'B3O.MARCAS_GASTOS';
    
  	protected function getRegister( $mark )
  	{
  		return BagoMarcaGasto::select( 'cod1' )->where( 'substr( cod1 , 0 , 6 )' , $mark )->first();
  	}

}