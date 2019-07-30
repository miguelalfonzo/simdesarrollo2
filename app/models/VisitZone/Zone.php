<?php

namespace VisitZone;

use \Eloquent;
use Illuminate\Database\Eloquent\Collection;
class Zone extends Eloquent
{
	
    protected $table = 'FICPE.NIVEL3GEOG';
    protected $primaryKey = 'N3GNIVEL3GEOG';


    protected static function getZonasSP(){

    	$row = \DB::transaction(function($conn){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_LISTAR_ZONAS( :data); END;');
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);
    }
  
}