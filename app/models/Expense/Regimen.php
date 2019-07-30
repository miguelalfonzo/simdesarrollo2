<?php

namespace Expense;

use \Eloquent;
use Illuminate\Database\Eloquent\Collection;

class Regimen extends Eloquent
{
    protected $table= TB_TIPO_REGIMEN;
    protected $primaryKey = 'id';


    public static function allSP()
    {
       $row = \DB::transaction(function($conn){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_LISTAR_TIPO_REGIMEN(:data); END;');
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