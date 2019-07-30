<?php

namespace Expense;

use \Eloquent;
use \Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ChangeRate extends Eloquent
{
    protected $table = TB_TIPO_DE_CAMBIO;
    
    protected function getFechaAttribute($value)
    {
        return date_format( date_create( $value ), 'd/m/Y' );
    }

    protected static function getTc()
    {
        return ChangeRate::where('moneda' , 'DO')->orderBy('fecha','desc')->first();
    }

    protected static function getTcSP()
    {
        
        $row = \DB::transaction(function($conn){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_LISTAR_TC(:data); END;');
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);
        
    }

    protected static function getDayTc( $date )
    {
        return ChangeRate::where( 'moneda' , 'DO' )->where( 'fecha' , $date )->first();
    }

    protected static function getLastDayDolar( $date )
    {
        return ChangeRate::where( 'moneda' , 'DO' )->where( 'fecha + 1' , $date->startOfDay() )->first()->venta;
    }

}