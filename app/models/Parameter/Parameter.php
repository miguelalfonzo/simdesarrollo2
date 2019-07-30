<?php

namespace Parameter;

use \Eloquent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Parameter extends Eloquent
{

	use SoftDeletingTrait;
	
    protected $table= TB_PARAMETRO;
    protected $primaryKey = 'id';

    protected static function order()
    {
    	return Parameter::orderBy( 'id' , 'ASC' )->get();
    }

    protected static function orderWithTrashed()
    {
        return Parameter::orderBy( 'updated_at' , 'DESC' )->withTrashed()->get();
    }

    protected function nextId()
    {
    	$nextId = Parameter::withTrashed()->select('id')->orderBy( 'id' , 'desc' )->first();
        if ( is_null( $nextId ) )
            return 1;
        else
            return $nextId->id + 1;
    }

}