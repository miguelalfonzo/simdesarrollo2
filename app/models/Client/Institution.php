<?php

namespace Client;
use \Eloquent;

class Institution extends Eloquent
{

    protected $table = TB_INSTITUCIONES;
    protected $primaryKey = 'pejcodpers';

    protected function getFullNameAttribute()
    {
        return $this->pejrazon;
    }

    protected function getEntryNameAttribute()
    {
    	return $this->pejrazon;
    }
}