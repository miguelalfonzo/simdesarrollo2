<?php

namespace Policy;

use \Eloquent;

class ApprovalPolicy extends Eloquent 
{
    protected $table = TB_POLITICA_APROBACION;
    protected $primaryKey = 'id';

    protected function userType()
    {
        return $this->hasOne( 'Common\TypeUser' , 'codigo' , 'tipo_usuario' );
    }
}
