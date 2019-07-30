<?php

namespace Dmkt;
use \Eloquent;

class SolicitudClient extends Eloquent
{
    protected $table = TB_SOLICITUD_CLIENTE;
    protected $primaryKey = 'id';


    protected static function salvar($solClientId,$solClientIdsol,$solClientIdCl,$solClientTipoCl,$userId){

        $row = \DB::transaction(function($conn) use ($solClientId,$solClientIdsol,$solClientIdCl,$solClientTipoCl,$userId){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_APROBAR_SOLICITUD_CLIENTE(:solClientId,:solClientIdsol,:solClientIdCl,:solClientTipoCl,:userId); END;');
                $stmt->bindParam(':solClientId', $solClientId, \PDO::PARAM_STR);
                $stmt->bindParam(':solClientIdsol', $solClientIdsol, \PDO::PARAM_STR);
                $stmt->bindParam(':solClientIdCl', $solClientIdCl, \PDO::PARAM_STR);
                $stmt->bindParam(':solClientTipoCl', $solClientTipoCl, \PDO::PARAM_STR);
                $stmt->bindParam(':userId', $userId, \PDO::PARAM_STR);
                $stmt->execute();       
                #oci_execute($lista, OCI_DEFAULT);
                #oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                #oci_free_cursor($lista);
                #return $array;

        });

        #return new Collection($row);
    }

    public function lastId()
    {
        $lastId = SolicitudClient::orderBy('id', 'DESC')->first();
        if( is_null( $lastId ) )
            return 0;
        else
            return $lastId->id;
    }

    protected function doctor()
    {
        return $this->hasOne('Client\Doctor','pefcodpers','id_cliente');
    }

    protected function institution()
    {
        return $this->hasOne('Client\Institution','pejcodpers','id_cliente');
    }

    protected function pharmacy()
    {
        return $this->hasOne('Client\Pharmacy' , 'pejcodpers' , 'id_cliente');
    }

    protected function warehouse()
    {
        return $this->hasOne('Client\DistrimedClient' , 'clcodigo' , 'id_cliente')->where( 'clclase' , 1 )->where( 'clestado' , 1 );
    }    

    protected function distributor()
    {
        return $this->hasOne('Client\DistrimedClient' , 'clcodigo' , 'id_cliente')->where( 'clclase' , 6 )->where( 'clestado' , 1 );
    }

    protected function clientType()
    {
        return $this->hasOne( 'Client\ClientType' , 'id' , 'id_tipo_cliente' );
    }

    public function solicitud()
    {
        return $this->belongsTo( 'Dmkt\Solicitud' , 'id_solicitud' , 'id' );
    }
}