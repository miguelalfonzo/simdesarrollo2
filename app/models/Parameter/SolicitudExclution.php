<?php

namespace Parameter;

use \Eloquent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class SolicitudExclution extends Eloquent
{

	use SoftDeletingTrait;
	
    protected $table = 'SOLICITUD_EXCLUSION';
    protected $primaryKey = 'id';

    protected static function order()
    {
    	return SolicitudExclution::orderBy( 'updated_at' , 'DESC' )->get();
    }

    protected static function orderWithTrashed()
    {
        return SolicitudExclution::orderBy( 'updated_at' , 'DESC' )->withTrashed()->get();
    }

    public function nextId()
    {
    	$nextId = SolicitudExclution::withTrashed()->select('id')->orderBy( 'id' , 'desc' )->first();
        if ( is_null( $nextId ) )
            return 1;
        else
            return $nextId->id + 1;
    }

}