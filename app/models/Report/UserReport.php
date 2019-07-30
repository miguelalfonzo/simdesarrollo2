<?php

namespace Report;

use \Eloquent;

class UserReport extends Eloquent {

	protected $table      = TB_REPORTE_USUARIO;
	protected $primaryKey = 'id';
	public $incrementing  = false;
	protected $fillable   = array('id', 'id_reporte', 'id_usuario', 'created_at', 'updated_at');

	public function TbReporte(){
		return $this->belongsTo('\Report\TbReporte', 'id_reporte', 'id_reporte');
	}
	public function User(){
		return $this->belongsTo('\User', 'id_usuario', 'id');
	}
	public function nextId()
    {
        $lastId = UserReport::orderBy('id', 'DESC')->first();
        if($lastId == null)
            return 0+1;
        else
            return $lastId->id+1;
    }
}