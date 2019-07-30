<?php

namespace System;

use \Eloquent;

class TiempoEstimadoFlujo extends Eloquent{

    protected $table = TB_FLUJO_TIEMPO_ESTIMADO;
    protected $primaryKey = 'id';

    public function StatusId(){
        return $this->hasOne( 'Common\State' , 'id' , 'status_id' );
    }

    protected function toUserType()
    {
        return $this->hasOne( 'Common\TypeUser' , 'codigo' , 'to_user_type' );
    }
}