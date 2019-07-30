<?php

class BagoUser extends Eloquent
{
    protected $table= TB_USUARIO_BAGO;
    protected $primaryKey = 'usucodusu';
    public $incrementing = false;


    function dni($usuario)
    {
        try
        {
            $rpta = array();
            $rpta['Status'] = 'Ok';
            $dni = BagoUser::where('usucodusu',strtoupper($usuario))->select('usutelefono')->first();
            if ( is_null( $dni ) )
            {
                $rpta['Status'] = 'Warning';
                $rpta['Description'] = 'No se encontro el DNI del usuario en el Sistema'; 
            }
            // if(isset($dni->usutelefono))                
            $rpta['Data'] = is_null( $dni ) ? '' : $dni->usutelefono;
        }
        catch (Exception $e)
        {
            $rpta['Status'] = 'Error';
            $rpta['Description'] = 'Error del Sistema';
            Log::error($e);
        }
        return $rpta;

    }

}