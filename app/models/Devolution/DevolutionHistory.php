<?php

namespace Devolution;
use \Eloquent;

class DevolutionHistory extends Eloquent
{

    protected $table= 'DEVOLUCION_HISTORIAL';
    protected $primaryKey = 'id';

    public function nextId()
    {
    	$nextId = DevolutionHistory::select( 'id' )->orderBy( 'id' , 'desc' )->first();
        if ( is_null( $nextId ) )
            return 1;
        else
            return $nextId->id + 1;
    }

    protected function updatedBy()
    {
        return $this->belongsTo( 'User' , 'updated_by' );
    }
}