<?php

namespace Users;

use \Eloquent;

class Supervisor extends Eloquent 
{
    protected $table = TB_SUPERVISOR;
    protected $primaryKey = 'SUPSUPERVISOR';

    protected function reps()
    {
    	return $this->belongsToMany('Users\Visitador','ficpe.linsupvis','lsvvisitador','lsvsupervisor');
    }

    protected function cuenta()
    {
    	return $this->hasOne( 'Dmkt\CtaRm' , 'codbeneficiario' , 'suplegajo' )->where( 'tipo' , CUENTA_BAGO );
    }

}
