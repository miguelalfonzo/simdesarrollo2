<?php
/**
 * Created by PhpStorm.
 * User: Developer
 * Date: 12/08/14
 * Time: 03:11 PM
 */
namespace Common;
use \Eloquent;

class State extends Eloquent
{
    protected $table = TB_ESTADO_SUB;
    protected $primaryKey = 'id';

	public function rangeState()
	{
        return $this->hasOne( 'Common\StateRange' , 'id' , 'id_estado' );
    }

    protected function getCancelStates()
    {
    	return State::whereIn( 'id' , array( PENDIENTE , DERIVADO , ACEPTADO , APROBADO , DEPOSITO_HABILITADO ) )->lists( 'id' );
    }
}