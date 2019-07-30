<?php

namespace Expense;

use \Eloquent;

class ExpenseItem extends Eloquent{

    protected $table= TB_GASTO_ITEM;
    protected $primaryKey = 'id';
    
    public function lastId()
    {
    	$lastId = ExpenseItem::orderBy('id','desc')->first();
		if( is_null( $lastId ) )
            return 0;
        else
            return $lastId->id;
    }

    function idExpenseType()
    {
		return $this->hasOne('ExpenseType','idtipogasto','tipo_gasto');
	}

    protected function setImporteAttribute( $value )
    {
        $this->attributes[ 'importe' ] = round( $value , 2 , PHP_ROUND_HALF_DOWN );
    }
}