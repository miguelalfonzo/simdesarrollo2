<?php 

namespace Expense;

use \Eloquent;

class Expense extends Eloquent {

	protected $table= TB_GASTO;
	protected $primaryKey = 'id';

	protected function getFechaMovimientoAttribute( $attr )
    {
        return \Carbon\Carbon::parse( $attr )->format('Y-m-d');
    }

    protected function getSeatDateAttribute()
    {
        return $this->updated_at->format( 'd/m/Y' );
    }

	protected function proof()
	{
		return $this->hasOne('Expense\ProofType','id','idcomprobante');
	}

    protected function items()
    {
        return $this->hasMany('Expense\ExpenseItem','id_gasto','id');
    }

    public function solicitud()
    {
    	return $this->hasOne( 'Dmkt\Solicitud' , 'id' , 'id_solicitud' );
    }

    public function lastId()
    {
		$lastId = Expense::orderBy('id','desc')->first();
		if( is_null( $lastId ) )
            return 0;
        else
            return $lastId->id;
	}

	public function lastIdIgv()
	{
		$lastIdIgv = Expense::orderBy( 'idigv' , 'desc' )->first();
		if( $lastIdIgv == null )
            return 0;
        else
            return $lastIdIgv->idigv;	
	}

}