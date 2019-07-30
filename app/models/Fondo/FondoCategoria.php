<?php

namespace Fondo;

use \Eloquent;

class FondoCategoria extends Eloquent
{
	protected $table      = TB_FONDO_CATEGORIA;
	protected $primaryKey = 'id';
	protected $fillable   = array('id','descripcion');

	public function fondosSubCategorias()
	{
		return $this->hasMany('Maintenance\fondosSubCategorias');
	}

	public function getAddData()
	{
		return $this->select( [ 'id' , 'descripcion' ] )
			->orderBy( 'descripcion' , 'ASC' )
			->get();
	}
}