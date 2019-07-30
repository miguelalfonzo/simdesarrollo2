<?php

namespace Fondo;

use \Eloquent;
use \Carbon\Carbon;
use \DB;

class FondoMktPeriodHistory extends Eloquent
{
	protected $table      = 'FONDO_MKT_PERIODO_HISTORIA';
	protected $primaryKey = 'id';
	
	protected static function getFondoMktPeriod( $period , $subCategoryId )
	{
		return FondoMktPeriodHistory::where( 'periodo' , $period )->where( 'subcategoria_id' , $subCategoryId )->first();
	}

	protected static function getNowFondoMktPeriod( $subCategoryId )
	{
		$nowPeriod = Carbon::now()->format( 'Ym' ); 
		return FondoMktPeriodHistory::where( 'subcategoria_id' , $subCategoryId )->where( 'periodo' , $nowPeriod )->orderBy( 'periodo' , 'DESC' )->first();
	}

	protected static function getLastFondoMktPeriod( $subCategoryId )
	{
		return FondoMktPeriodHistory::where( 'subcategoria_id' , $subCategoryId )->orderBy( 'periodo' , 'DESC' )->first();
	}

	public function nextId()
	{
		$lastId = FondoMktPeriodHistory::orderBy( 'id' , 'DESC' )->first();
		if ( is_null( $lastId ) )
			return 1;
		else
			return $lastId->id + 1;
	}

	public function maxFundPeriod( $subCategoryId , $endPeriod )
	{
		$startPeriod = substr( $endPeriod , 0 , 4 ) . '00';
		return $this->select( 'periodo' )
			->where( 'subcategoria_id' , $subCategoryId )
			->where( 'periodo' , '<' , $endPeriod )
			->where( 'periodo' , '>' , $startPeriod )
			->max( 'periodo' );
	}
	
	public function getPeriodData( $subCategoryId , $period )
	{
		return $this->select( [ 'saldo_final' , 'retencion_final' ] )
            ->where( 'periodo' , $period )
            ->where( 'subcategoria_id' , $subCategoryId )
            ->first();
	}
}