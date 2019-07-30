<?php

namespace Fondo;

use \Eloquent;

class FondoMktType extends Eloquent
{
	protected $table      = 'TIPO_FONDO_SUBCATEGORIA';
	protected $primaryKey = 'id';

	public function getAddData()
	{
		return $this->select( [ 'codigo id' , 'descripcion' ] )
			->orderBy( 'descripcion' , 'ASC' )->get();
	}

	
}