<?php

namespace Fondo;

use \Eloquent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Fondo extends Eloquent
{

    use SoftDeletingTrait;

    protected $table = TB_FONDO_CONTABLE;
    protected $primaryKey = 'id';

    public function getIdusertypeAttribute($value)
    {
        return trim($value);
    }

    public function lastId()
    {
	  	$lastId = Fondo::orderBy('id','desc')->first();
	  	if($lastId == null)
        	return 0;
    	else
        	return $lastId->id;
 	}

    public function nextId()
    {
        $lastId = Fondo::withTrashed()->orderBy('id','desc')->first();
        if( is_null( $lastId ) )
            return 0;
        else
            return $lastId->id + 1;

    }

    protected static function order()
    {
        return Fondo::orderBy( 'updated_at' , 'desc' )->get();
    }

    protected static function orderWithTrashed()
    {
        return Fondo::orderBy( 'updated_at' , 'DESC' )->withTrashed()->get();
    }

    public function bagoAccount()
    {
        return $this->hasOne( 'Expense\PlanCta' , 'ctactaextern' , 'num_cuenta' );
    }

    protected function account()
    {
        return $this->hasOne( 'Dmkt\Account' , 'num_cuenta' , 'num_cuenta' );
    }

    protected static function getContableFund( $accountNumber )
    {
        return Fondo::where( 'num_cuenta' , $accountNumber )->first();
    }

    public function getAddData()
    {
        return $this->select( [ 'id' , 'nombre descripcion' ] )
            ->orderBy( 'nombre' , 'ASC' )
            ->get();
    }


}