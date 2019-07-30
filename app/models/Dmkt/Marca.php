<?php

namespace Dmkt;

class Marca extends \Eloquent 
{
	protected $table = TB_MARCAS_BAGO;
    public $timestamps = false;
    protected $primaryKey = 'id';

    function manager()
    {
        return $this->hasOne('Dmkt\Manager','id','gerente_id');
    }
    public function fondos()
	{
		return $this->hasMany('Maintenance\Fondos');
	}
}