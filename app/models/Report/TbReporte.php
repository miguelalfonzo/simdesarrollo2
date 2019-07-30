<?php

namespace Report;

use \Eloquent;

class TbReporte extends Eloquent {

	protected $table      = TB_REPORTE_FORMULA;
	protected $primaryKey = 'id_reporte';
	public $incrementing  = false;
	protected $fillable   = array('id_reporte','descripcion','formula','query_id', 'created_at', 'updated_at');

	public function tbQuery(){
		return $this->belongsTo('\Report\TbQuery', 'query_id');
	}

	public function UserReport()
    {
         return $this->hasMany('\Report\UserReport', 'id_reporte');
    }

    public function nextId()
    {
        $lastId = TbReporte::orderBy('id_reporte', 'DESC')->first();
        if($lastId == null)
            return 0+1;
        else
            return $lastId->id_reporte+1;
    }
}
