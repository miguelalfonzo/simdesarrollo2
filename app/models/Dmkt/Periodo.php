<?php

namespace Dmkt;
use \Eloquent;

class Periodo extends Eloquent
{
    protected $table = TB_PERIODO;
    protected $primaryKey = 'id';

    public function lastId()
    {
        $lastId = Periodo::orderBy('id', 'DESC')->first();
        if( is_null( $lastId ) )
            return 0;
        else
            return $lastId->id;
    }

    protected function getPeriodoAttribute()
    {
        return substr( $this->aniomes , 0 , 4 ) . '-' . substr( $this->aniomes , 4 , 2 );
    }

    protected static function inhabilitar( $periodo )
    {
        Periodo::where( 'aniomes' , $periodo )->where( 'status' , ACTIVE )->where( 'idtiposolicitud' , SOL_INST )->update( array( 'status' => 3) );
    }

    protected static function periodoInst( $periodo )
    {
        return Periodo::where( 'aniomes' , $periodo )->where( 'idtiposolicitud' , SOL_INST )->first();    
    }
}
