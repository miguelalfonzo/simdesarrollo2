<?php

namespace Expense;

use \Eloquent;

class Mark extends Eloquent
{
    protected $table= TB_MARCA;
    protected $primaryKey = 'id';
   
   	public static function lastId()
   	{
   		$mark = Mark::orderBy('id' , 'desc' )->first();
   		if ( $mark == null )
   			return 0;
   		else
   			return $mark->id;
   	}

    protected static function getMark( $markCode )
    {
      return Mark::where( 'codigo' , $markCode )->first();
    }
}