<?php

namespace Client;
use \Eloquent;

class Doctor extends Eloquent
{
    protected $table = TB_DOCTOR;
    protected $primaryKey = 'pefcodpers';

    protected function getFullNameAttribute()
    {
        return $this->attributes[ 'pefnrodoc1' ].'-'.$this->attributes[ 'pefnombres' ].' '.$this->attributes[ 'pefpaterno' ] .' '. $this->attributes[ 'pefmaterno' ];
    }

    protected function getEntryNameAttribute()
    {
    	return $this->pefnombres . ' ' . $this->pefpaterno . ' ' . $this->pefmaterno;
    }
} 