<?php

namespace Client;
use \Eloquent;

class ClientType extends Eloquent
{

    protected $table = TB_CLIENTE_TIPO;
    protected $primaryKey = 'id';

    public function getAddData()
    {
    	return $this->select( [ 'id' , 'descripcion' ] )
    		->orderBy( 'descripcion' , 'ASC' )
    		->get();
    }

}