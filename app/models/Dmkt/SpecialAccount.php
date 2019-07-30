<?php

namespace Dmkt;

use \Eloquent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class SpecialAccount extends Eloquent 
{

	use SoftDeletingTrait;
	
    protected $table = 'CUENTA_ESPECIAL';
    protected $primaryKey = 'id';

    public static function getRefund()
    {
    	return SpecialAccount::find( 1 )->num_cuenta;
    }

    protected static function order()
    {
    	return SpecialAccount::orderBy( 'updated_at' , 'DESC' )->get();
    }

    protected static function orderWithTrashed()
    {
        return SpecialAccount::orderBy( 'updated_at' , 'DESC' )->withTrashed()->get();
    }
}