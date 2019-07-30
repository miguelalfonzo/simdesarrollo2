<?php

namespace Fondo;

use \Carbon\Carbon;
use \Eloquent;
use \DB;
use \Auth;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class FondoInstitucional extends Eloquent
{

	use SoftDeletingTrait;

	protected $table      = TB_FONDO_INSTITUCION;
	protected $primaryKey = 'id';
	protected $fillable   = array('id','descripcion');

	protected function getSaldoDisponibleAttribute()
	{
		return round( $this->saldo - $this->retencion , 2 , PHP_ROUND_HALF_DOWN );
	}

	public function subCategoria()
	{
		return $this->hasOne( 'Fondo\FondoSubCategoria' , 'id' , 'subcategoria_id' );
	}

	public static function getSubFondo()
	{
		$now = Carbon::now();
		return  FondoInstitucional::where( 'anio' , $now->format( 'Y' ) )
				->whereHas( 'subcategoria' , function( $query )
	            {
	            	$query->where( 'trim( tipo )' , 'I' );
	            })->get();
    }

    protected static function order()
	{
		return FondoInstitucional::orderBy( 'updated_at' , 'desc' )->get();
	}

	protected static function orderWithTrashed()
	{
		$now = Carbon::now();
		$fundDataSql = FondoInstitucional::select( [ 'id' , 'subcategoria_id' , 'saldo' , 'retencion' ] )
			->where( 'anio' , '=' , $now->format( 'Y' ) )
			->orderBy( 'updated_at' , 'DESC' )
			->withTrashed();

		if( ! in_array( Auth::user()->type , [ GER_PROM , GER_COM ] ) )
		{
			$fundDataSql->where( 'subcategoria_id' , 0 );
		}
		return $fundDataSql->get();
	}

	protected function setSaldoAttribute( $value )
	{
		$this->attributes[ 'saldo' ] = round( $value , 2 , PHP_ROUND_HALF_DOWN );
	}

	protected function setRetencionAttribute( $value )
	{
		$this->attributes[ 'retencion' ] = round( $value , 2 , PHP_ROUND_HALF_DOWN );
	}

	protected function getFullNameAttribute()
	{
		return $this->SubCategoria->descripcion;
	}

	protected function getMiddleNameAttribute()
	{
		return $this->SubCategoria->descripcion;
	}

	public function getDetailNameAttribute()
    {
    	$subCategory = $this->subCategoria;
        return $subCategory->categoria->descripcion . ' | ' . $subCategory->descripcion;
    }

}