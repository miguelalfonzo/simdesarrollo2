<?php

namespace Dmkt;

use Eloquent;
use Users\Personal;

class SolicitudDetalle extends Eloquent
{
	protected $table = TB_SOLICITUD_DETALLE;
    protected $primaryKey = 'id';    
    

    protected static function salvar($idDetalle,$jsonDetalleSol,$userId){

        $row = \DB::transaction(function($conn) use ($idDetalle,$jsonDetalleSol,$userId){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_APROBAR_SOLICITUD_DETALLE(:idDetalle,:jsonDetalleSol,:userId); END;');
                $stmt->bindParam(':idDetalle', $idDetalle, \PDO::PARAM_STR);
                $stmt->bindParam(':jsonDetalleSol', $jsonDetalleSol, \PDO::PARAM_STR);
                $stmt->bindParam(':userId', $userId, \PDO::PARAM_STR);
                $stmt->execute();       
                #oci_execute($lista, OCI_DEFAULT);
                #oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                #oci_free_cursor($lista);
                #return $array;

        });

        #return new Collection($row);
    }

    protected function getNumeroOperacionDevolucionAttribute()
    {
        $jDetalle = json_decode( $this->detalle );
        return isset( $jDetalle->numero_operacion_devolucion ) ? $jDetalle->numero_operacion_devolucion : null;            
    }

    protected function getMontoAprobadoAttribute()
    {
        return json_decode( $this->detalle )->monto_aprobado;
    }

    protected function getDescuentoAttribute()
    {
        $jDetalle = json_decode( $this->detalle );
        return isset( $jDetalle->descuento ) ? $jDetalle->descuento : null;
    }

    protected function getMontoDescuentoAttribute()
    {
        $jDetalle = json_decode( $this->detalle );
        return isset( $jDetalle->monto_descuento ) ? $jDetalle->monto_descuento : null;    
    }

    protected function getTccAttribute()
    {
        return json_decode( $this->detalle )->tcc;
    }

    protected function getTcvAttribute()
    {
        return json_decode( $this->detalle )->tcv;
    }

    protected function getNumCuentaAttribute()
    {
        return json_decode( $this->detalle )->num_cuenta;
    }

    protected function getSupervisorAttribute()
    {
        $idSup = json_decode( $this->detalle )->supervisor;
//        return \Users\Sup::where( 'iduser' , $idSup )->first()->full_name;
        return Personal::where( 'user_id' , $idSup)->first()->full_name;
    }    

    protected function getMontoActualAttribute()
    {
        $jDetalle = json_decode( $this->detalle );
        if ( isset( $jDetalle->monto_aprobado ) )
            return $jDetalle->monto_aprobado;
        else if ( isset( $jDetalle->monto_aceptado ) )
            return $jDetalle->monto_aceptado;
        else if ( isset( $jDetalle->monto_derivado ) )
            return $jDetalle->monto_derivado;
        else if ( isset( $jDetalle->monto_solicitado ) )
            return $jDetalle->monto_solicitado;
        else
            return null;
    }

    protected function getCurrencyMoneyAttribute()
    {
        return $this->typeMoney->simbolo . ' ' . $this->monto_actual;
    }

    protected function getMontoSolicitadoAttribute()
    {
        return json_decode( $this->detalle )->monto_solicitado;
    }

    protected function getFechaEntregaAttribute()
    {
        $jDetalle = json_decode( $this->detalle );
        if ( isset( $jDetalle->fecha_entrega ) )
        {
            return $jDetalle->fecha_entrega;
        }
        else
        {
            return $this->periodo->aniomes;
        }
    }

    protected function getNumRucAttribute()
    {
        $jDetalle = json_decode( $this->detalle );
        if ( isset( $jDetalle->num_ruc ) )
        {
            return $jDetalle->num_ruc;
        }
        else
        {
            return null;
        }
    }

    protected function getSolesImportAttribute()
    {
        if( $this->id_moneda == DOLARES )
        {
            return round( $this->monto_actual * $this->tcv , 2 , PHP_ROUND_HALF_DOWN );
        }
        elseif( $this->id_moneda == SOLES )
        {
            return $this->monto_actual;
        }
        else
        {
            return null;
        }
    }

    protected function getSolesDepositImportAttribute()
    {
        if( $this->id_moneda == DOLARES )
        {
            return round( $this->deposit->total * $this->tcv , 2 , PHP_ROUND_HALF_DOWN );
        }
        elseif( $this->id_moneda == SOLES )
        {
            return $this->deposit->total;
        }
        else
        {
            return null;
        }
    }

    public function nextId()
    {
        $lastId = SolicitudDetalle::orderBy('id', 'DESC')->first();
        if( is_null( $lastId ) )
            return 1;
        else
        	return $lastId->id + 1;
    }

    public function periodo()
    {
        return $this->hasOne( 'Dmkt\Periodo' , 'id' , 'id_periodo' );
    }

    public function typeMoney()
    {
    	return $this->hasOne('Common\TypeMoney','id', 'id_moneda' );
    }

    protected function typePayment()
    {
        return $this->hasOne('Common\TypePayment','id','id_pago');
    }

    protected function deposit()
    {
        return $this->hasOne('Common\Deposit','id','id_deposito');
    }

    public function thisSubFondo()
    {
        return $this->belongsTo( 'Fondo\FondoInstitucional' , 'id_fondo' );
    }

    protected function solicitud()
    {
        return $this->belongsTo( 'Dmkt\Solicitud' , 'id' , 'id_detalle' );
    }
}