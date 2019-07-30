<?php
/**
 * Created by PhpStorm.
 * User: Developer
 * Date: 12/08/14
 * Time: 03:11 PM
 */
namespace Common;
use \Eloquent;
use Illuminate\Database\Eloquent\Collection;
class StateRange extends Eloquent{

    protected $table = TB_ESTADO;
    protected $primaryKey = 'id';

    protected function order()
    {
    	return StateRange::orderBy('id', 'ASC')->get();
    }

    protected function orderSP()
    {
    	$row = \DB::transaction(function($conn){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_LISTAR_ESTADO( :data); END;');
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