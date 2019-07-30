<?php

namespace Users;

use \Eloquent;

class Visitador extends Eloquent 
{
    protected $table = TB_VISITADOR;
    protected $primaryKey = 'VISVISITADOR';

    protected function cuenta()
    {
    	return $this->hasOne( 'Dmkt\CtaRm' , 'codbeneficiario' , 'vislegajo' )->where( 'tipo' , CUENTA_BAGO );
    }

    protected function sup()
    {
    	return $this->belongsToMany( 'Users\Supervisor','ficpe.linsupvis','lsvvisitador','lsvsupervisor','visvisitador');
    }

    protected function linsupvis()
    {
        return $this->hasOne( 'Users\RmSupBago' , 'lsvvisitador' , 'visvisitador' );
    }
}