<?php

namespace Dmkt;

use \Eloquent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Illuminate\Database\Eloquent\Collection;

class Activity extends Eloquent
{
    use SoftDeletingTrait;

    protected $table = TB_TIPO_ACTIVIDAD;
    protected $primaryKey = 'id';

    public function nextId()
    {
        $nextId = Activity::withTrashed()->select('id')->orderBy( 'id' , 'desc' )->first();
        if ( is_null( $nextId ) )
            return 1;
        else
            return $nextId->id + 1;
    }

    protected static function order()
    {
    	return Activity::orderBy( 'nombre' , 'asc' )->get();
    }

    protected static function orderSP()
    {
        $row = \DB::transaction(function($conn){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_LISTAR_ACTIVIDADES( :data); END;');
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);
    }

    protected static function get_actividad_cliente($clientType)
    {   
        $Tipoclient=$clientType;

        $row = \DB::transaction(function($conn) use ($Tipoclient){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_GET_ACTIVIDAD_CLIENTE(:Tipoclient,:data); END;');
                $stmt->bindParam(':Tipoclient', $Tipoclient, \PDO::PARAM_STR);
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);
    }


    protected static function get_activities($client,$inversion)
    {   
        $clients=$client;
        $inversions=$inversion;

        $row = \DB::transaction(function($conn) use ($clients,$inversions){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_GET_ACTIVITIES(:clients,:inversions,:data); END;');
                $stmt->bindParam(':clients', $clients, \PDO::PARAM_STR);
                $stmt->bindParam(':inversions', $inversions, \PDO::PARAM_STR);
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);
        //return json_encode($row);
    }

    protected static function orderWithTrashed()
    {
        return Activity::orderBy( 'updated_at' , 'desc' )->withTrashed()->get();
    }

    public function investmentActivity()
    {
    	return $this->hasMany('Dmkt\InvestmentActivity' , 'id_actividad' , 'id' );
    }

    protected function client()
    {
        return $this->hasOne( 'Client\ClientType' , 'id' , 'tipo_cliente' );
    }

    protected function getClientActivities( $clientType )
    {
        return Activity::distinct()->select( 'id' )->where( 'tipo_cliente' , $clientType )->get();
    }

    public function getAddData()
    {
        return $this->select( [ 'id' , 'nombre descripcion' ] )->get();
    }
}