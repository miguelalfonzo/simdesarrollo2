<?php

namespace Report;

use \Eloquent;

class TbQuery extends Eloquent 
{
	protected $table      = TB_REPORTE_QUERY;
	protected $primaryKey = 'id';
	public $incrementing  = false;
	protected $fillable   = array('id','name','query');
	public $timestamps    = false;

	public function tbReportes(){
		return $this->hasMany('\Report\TbReporte');
	}
}