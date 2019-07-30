<?php

namespace PPTO;
use \Exception;
use \PDO;
use \Log;
use \DB;

class SupPPTOProcedure
{

    public function uploadValidate( $input , $year , $category )
    {
        try
        {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare( 'BEGIN PK_PPTO_SUPERVISOR.SP_VALIDAR_PPTO_SUPERVISOR( ' . $input . ' , :año , :categoria , :rpta , :desc , :list ); end;' );
            $stmt->bindParam( ':año' , $year , PDO::PARAM_INT );
            $stmt->bindParam( ':categoria' , $category , PDO::PARAM_INT );
            $stmt->bindParam( ':rpta' , $rpta , PDO::PARAM_STR , 10 );
            $stmt->bindParam( ':desc' , $desc , PDO::PARAM_STR , 300 );
            $stmt->bindParam( ':list' , $list , PDO::PARAM_STR , 10000 );
            $stmt->execute();

            $response = [ status => $rpta , description => $desc ];
            if( ! is_null( $list ) )
            {
                $response[ 'List' ] = [ 'Detail' => explode( '|' , substr( $list , 0 , -1 ) ) , 'Class' => 'list-group-item-warning' ];
            }
            return $response;
        }
        catch( Exception $e )
        {
            Log::error( $e );
            return [ status => error , description => $e->getMessage() ];
        }
    }

    public function upload( $input , $year , $category , $user_id )
    {
        try
        {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare( 'BEGIN PK_PPTO_SUPERVISOR.SP_CARGA_PPTO_SUPERVISOR( ' . $input . ' , :año , :categoria , :user_id , :rpta , :desc , :list ); end;' );
            $stmt->bindParam( ':año' , $year , PDO::PARAM_INT );
            $stmt->bindParam( ':categoria' , $category , PDO::PARAM_INT );
            $stmt->bindParam( ':user_id' , $user_id , PDO::PARAM_INT );
            $stmt->bindParam( ':rpta' , $rpta , PDO::PARAM_STR , 10 );
            $stmt->bindParam( ':desc' , $desc , PDO::PARAM_STR , 300 );
            $stmt->bindParam( ':list' , $list , PDO::PARAM_STR , 10000 );
            $stmt->execute();

            $response = [ status => $rpta , description => $desc ];
            if( ! is_null( $list ) )
            {
                $response[ 'List' ] = [ 'Detail' => explode( '|' , substr( $list , 0 , -1 ) ) , 'Class' => 'list-group-item-warning' ];    
            }
            return $response;
        }
        catch( Exception $e )
        {
            Log::error( $e );
            return [ status => error , description => $e->getMessage() ];
        }
    }

    public function update( $ppto_id , $roundAmount , $user_id )
    {
        try
        {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare( 'BEGIN PK_PPTO_SUPERVISOR.SP_UPDATE_PPTO_SUPERVISOR( :ppto_id , :monto , :user_id , :rpta , :desc , :data ); end;' );
            $stmt->bindParam( ':ppto_id' , $ppto_id , PDO::PARAM_INT );
            $stmt->bindParam( ':monto' , $roundAmount );
            $stmt->bindParam( ':user_id' , $user_id );
            $stmt->bindParam( ':rpta' , $rpta , PDO::PARAM_STR , 10 );
            $stmt->bindParam( ':desc' , $desc , PDO::PARAM_STR , 500 );
            $stmt->bindParam( ':data' , $data , PDO::PARAM_STR , 20 );
            $stmt->execute();
            
            $response = [ status => $rpta , description => $desc ];
            if( ! is_null( $data ) )
            {
                $response[ data ] = $data;
            }
            return $response;       
        }
        catch( Exception $e )
        {
            Log::error( $e );
            return [ status => error , description => $e->getMessage() ];
        }
    }

}
