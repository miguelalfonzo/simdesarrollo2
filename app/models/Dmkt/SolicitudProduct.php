<?php

namespace Dmkt;
use \Eloquent;
use \DB;
use \Auth;
use \Fondo\FondoGerProd;
use \Fondo\FondoSupervisor;
use \Fondo\FondoInstitucional;
use \Expense\ChangeRate;
use \Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
class SolicitudProduct extends Eloquent
{
    protected $table = TB_SOLICITUD_PRODUCTO;
    protected $primaryKey = 'id';

    protected static function salvar($solProductId,$solProductIdSol,$solProductIdPro,$userId){

        $row = \DB::transaction(function($conn) use ($solProductId,$solProductIdSol,$solProductIdPro,$userId){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_APROBAR_SOLICITUD_PRODUCTO(:solProductId,:solProductIdSol,:solProductIdPro,:userId); END;');
                $stmt->bindParam(':solProductId', $solProductId, \PDO::PARAM_STR);
                $stmt->bindParam(':solProductIdSol', $solProductIdSol, \PDO::PARAM_STR);
                $stmt->bindParam(':solProductIdPro', $solProductIdPro, \PDO::PARAM_STR);
                $stmt->bindParam(':userId', $userId, \PDO::PARAM_STR);
                $stmt->execute();       
                #oci_execute($lista, OCI_DEFAULT);
                #oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                #oci_free_cursor($lista);
                #return $array;

        });

        #return new Collection($row);
    }


    protected  static function salvarmonto($monto,$solProductId){

        $row = \DB::transaction(function($conn) use ($monto,$solProductId){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_APROBAR_SOLICITUD_MONTO(:monto,:solProductId); END;');
                $stmt->bindParam(':monto', $monto, \PDO::PARAM_STR);
                $stmt->bindParam(':solProductId', $solProductId, \PDO::PARAM_STR);
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
        $lastId = SolicitudProduct::orderBy( 'id' , 'DESC' )->first();
        if( is_null( $lastId ) )
            return 0;
        else
            return $lastId->id;
    }

    public function getSubFondo( $userType , $solicitud , $productoId = null )
    {
        $id_producto = isset( $productoId ) ? $productoId : $this->id_producto;
        $year = Carbon::now()->format( 'Y' );
        
        if ( $userType == SUP )
        {
            $userId = $solicitud->personalTo->userSup();

            return FondoSupervisor::select( [ 'id' , 'subcategoria_id' , 'saldo' , 'retencion' , 'marca_id' , '\'S\' tipo' ] )
                    ->whereHas( 'subcategoria' , function( $query ) use ( $userType )
                    {
                        $query->where( 'trim( tipo )' , $userType );
                    })
                    ->with( 'subCategoria.categoria' , 'marca' )
                    ->where( 'supervisor_id' , $userId )
                    ->where( 'marca_id' , $id_producto )
                    ->where( 'anio' , $year )
                    ->get();
        }
        else if( in_array( $userType , [ GER_PROD , GER_PROM ] ) )
        {
            return FondoGerProd::select( [ 'id' , 'subcategoria_id' , 'saldo' , 'retencion' , 'marca_id' , '\'P\' tipo' ] )
                    ->whereHas( 'subcategoria' , function( $query ) use ( $userType )
                    {
                        $query->where( 'trim( tipo )' , $userType );
                    })
                    ->with( 'subCategoria.categoria' , 'marca' )
                    ->where( 'marca_id' , $id_producto )
                    ->where( 'anio' , $year )
                    ->where( 'saldo' , '>' , 0 )
                    ->get();
        }
        else if( in_array( $userType , [ GER_COM , GER_GER ] ) )
        {
            $supFunds = FondoSupervisor::select( [ 'id' , 'subcategoria_id' , 'saldo' , 'retencion' , 'marca_id' , '\'S\' tipo' ] )
                        ->whereHas( 'subcategoria' , function( $query ) use ( $userType )
                        {
                            $query->where( 'trim( tipo )' , SUP );
                        })
                        ->with( 'subCategoria.categoria' , 'marca' )
                        ->where( 'marca_id' , $id_producto )
                        ->where( 'anio' , $year )
                        ->where( 'saldo' , '>' , 0 )
                        ->get();
            $gerFunds = FondoGerProd::select( [ 'id' , 'subcategoria_id' , 'saldo' , 'retencion' , 'marca_id' , '\'P\' tipo' ] )
                        ->whereHas( 'subcategoria' , function( $query ) use ( $userType )
                        {
                            $query->whereIn( 'trim( tipo )' , [ GER_PROD , GER_PROM ] );
                        })
                        ->with( 'subCategoria.categoria' , 'marca' )
                        ->where( 'marca_id' , $id_producto )
                        ->where( 'anio' , $year )
                        ->where( 'saldo' , '>' , 0 )
                        ->get();
            return $supFunds->merge( $gerFunds );
        }
        else
        {
            return [];
        }
    }

    protected static function getSubFondoSP( $userType , $solicitud , $productoId = null ){

        
        $userType=$userType;
        $userId = $solicitud->personalTo->userSup();
        $productoId=$productoId;

        $row = \DB::transaction(function($conn) use ($userType,$userId,$productoId){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_GET_SUB_FONDO_PRODUCTO(:userType, :userId,:productoId, :data); END;');
                $stmt->bindParam(':userType', $userType, \PDO::PARAM_STR);
                $stmt->bindParam(':userId', $userId, \PDO::PARAM_STR);
                $stmt->bindParam(':productoId', $productoId, \PDO::PARAM_STR);
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row); 
    }

    
    /*protected function getSolProducts( $idSolProduct )
    {
        return SolicitudProduct::whereIn( 'id' , $idSolProduct )->lists( 'id_producto' );
    }*/

    public function thisSubFondo()
    {
        if ( $this->id_tipo_fondo_marketing == GER_PROD )
            return $this->belongsTo( 'Fondo\FondoGerProd' , 'id_fondo_marketing' );
        else
            return $this->belongsTo( 'Fondo\FondoSupervisor' , 'id_fondo_marketing' );
    }

    public function fondoSup()
    {
        return $this->belongsTo( 'Fondo\FondoSupervisor' , 'id_fondo_marketing' );
    }

    public function fondoGerProd()
    {
        return $this->belongsTo( 'Fondo\FondoGerProd' , 'id_fondo_marketing' );
    }


    public function marca()
    {
        return $this->hasOne( 'Dmkt\Marca' , 'id' , 'id_producto' );
    }

    public function fondoMarca()
    {
        return $this->hasOne( 'Dmkt\Marca' , 'id' , 'id_fondo_producto' );
    }

    public function subCatFondo()
    {
        return $this->hasOne( 'Fondo\FondoSubCategoria' , 'id' , 'id_fondo' );
    }

    public function user()
    {
        return $this->hasOne( 'User' , 'id' , 'id_fondo_user');
    }

    protected function getMontoAsignadoSolesAttribute()
    {
        $compra = ChangeRate::getLastDayDolar( $this->updated_at );
        return round( $this->monto_asignado * $compra , 2 , PHP_ROUND_HALF_DOWN );
    }

    protected function setMontoAsignadoAttribute( $value )
    {
        $this->attributes[ 'monto_asignado' ] = round( $value , 2 , PHP_ROUND_HALF_DOWN );
    }

}