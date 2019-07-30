<?php

namespace PPTO;
use \Exception;
use \Auth;
use \PDO;
use \Log;
use \DB;

class InsPPTOProcedure
{

    public function uploadValidate( $roundAmount , $year )
    {
        try
        {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare( 'BEGIN PK_PPTO_INSTITUCION.SP_VALIDAR_PPTO_INSTITUCION( :monto , :año , :rpta , :desc ); end;' );
            $stmt->bindParam( ':monto' , $roundAmount );
            $stmt->bindParam( ':año' , $year , PDO::PARAM_INT );
            $stmt->bindParam( ':rpta' , $rpta , PDO::PARAM_STR , 10 );
            $stmt->bindParam( ':desc' , $desc , PDO::PARAM_STR , 200 );
            $stmt->execute();
            return [ status => $rpta , description => $desc ];
        }
        catch( Exception $e )
        {
            Log::error( $e );
            return [ status => error , description => $e->getMessage() ];
        }    
    }

	public function upload( $roundAmount , $year , $user_id )
    {
        try
        {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare( 'BEGIN PK_PPTO_INSTITUCION.SP_CARGA_PPTO_INSTITUCION( :monto , :año , :user_id , :rpta , :desc ); end;' );
            $stmt->bindParam( ':monto' , $roundAmount );
            $stmt->bindParam( ':año' , $year , PDO::PARAM_INT );
            $stmt->bindParam( ':user_id' , $user_id , PDO::PARAM_INT );
            $stmt->bindParam( ':rpta' , $rpta , PDO::PARAM_STR , 10 );
            $stmt->bindParam( ':desc' , $desc , PDO::PARAM_STR , 200 );
            $stmt->execute();
            return [ status => $rpta , description => $desc ];
        }
        catch( Exception $e )
        {
            Log::error( $e );
            return [ status => error , description => $e->getMessage() ];
        }
    }
}
