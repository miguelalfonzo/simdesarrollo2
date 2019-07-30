<?php

namespace Expense;

use \Eloquent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class MarkProofAccounts extends Eloquent
{

	use SoftDeletingTrait;

    protected $table= TB_CUENTA_GASTO_MARCA;
    protected $primaryKey = 'id';
 
    public function lastId()
    {
    	$lastId = MarkProofAccounts::orderBy('id','desc')->first();
		if( $lastId == null )
            return 0;
        else
            return $lastId->id;
    }

    public function nextId()
    {
		$lastId = MarkProofAccounts::orderBy('id','desc')->first();
		if( is_null( $lastId ) )
            return 1;
        else
            return $lastId->id + 1;
    }

    protected static function order()
    {
    	return MarkProofAccounts::orderBy( 'updated_at' , 'DESC' )->get();
    }

	protected static function orderWithTrashed()
    {
    	return MarkProofAccounts::orderBy( 'updated_at' , 'DESC' )->withTrashed()->get();
    }    

	protected static function getMarks( $num_cuenta_mkt , $num_cuenta_expense )
	{
		return MarkProofAccounts::where(TB_CUENTA_GASTO_MARCA.'.num_cuenta_gasto' , $num_cuenta_expense )->where('cuenta_gasto_marca.num_cuenta_fondo' , $num_cuenta_mkt )->select( 'marca_codigo')->get();
	} 

	protected static function listData( $num_cuenta )
	{
		return MarkProofAccounts::where( 'num_cuenta_fondo' , $num_cuenta )->with( 'fondo' , 'accountExpense' , 'mark' , 'bagoAccountExpense' )->distinct()->select( 'num_cuenta_fondo , num_cuenta_gasto , marca_codigo' )->get();
	}

	public function accountExpense()
	{
		return $this->hasOne( 'Dmkt\Account' , 'num_cuenta' , 'num_cuenta_gasto' );
	}

	public function mark()
	{
		return $this->hasOne( 'Expense\Mark' , 'codigo' , 'marca_codigo' );
	}

	public function document()
	{
		return $this->hasOne( 'Expense\Proof' , 'id' , 'iddocumento' );
	}

	public function fondo()
	{
		return $this->hasOne( 'Fondo\Fondo' , 'num_cuenta' , 'num_cuenta_fondo' );
	}

	public function accountFondo()
	{
		return $this->hasOne( 'Dmkt\Account' , 'num_cuenta' , 'num_cuenta_fondo');
	}

	protected function bagoAccountFondo()
	{
		return $this->hasOne( 'Expense\PlanCta' , 'ctactaextern' , 'num_cuenta_fondo' );
	}

	public function bagoAccountExpense()
	{
		return $this->hasOne( 'Expense\PlanCta' , 'ctactaextern' , 'num_cuenta_gasto' );
	}
}