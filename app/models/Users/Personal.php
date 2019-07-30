<?php

namespace Users;

use \Eloquent;
use \Auth;
use \Exception;
use \Log;
use \DB;
use \stdClass;

use Illuminate\Database\Eloquent\Collection;
class Personal extends Eloquent
{

    protected $table = TB_PERSONAL;
    protected $primaryKey = 'id';

    // protected function getFullNameAttribute()
    // {
    //     return substr( $this->attributes['nombres'] , 0 , 1 ).'. '.$this->attributes['apellidos'];
    // }


    public static function userSourceSP($texto,$user){

        $row = \DB::transaction(function($conn) use($texto,$user){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_LISTAR_USER_TEMPORAL(:texto,:user,:data); END;');
                $stmt->bindParam(':texto', $texto, \PDO::PARAM_STR);
                $stmt->bindParam(':user', $user, \PDO::PARAM_STR);
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);

    }
    

    protected function lastId()
    {
        $lastId = Personal::orderBy('id', 'DESC')->first();
        if( is_null( $lastId ) )
            return 0;
        else
            return $lastId->id;
    }

    protected function getSeatNameAttribute()
    {
        return strtoupper( substr( $this->nombres , 0 , 2 ) . ' ' . $this->apellidos );
    }

    public function getFullNameAttribute()
    {
        return ucwords( mb_strtolower( $this->nombres . ' ' . $this->apellidos ) );
    }

    // idkc : RETORNA MODELO DE SUPERVISOR
    protected function getSup( $user_id )
    {
        $persona = Personal::where( 'user_id' , $user_id )->first();
        return $persona->rmSup;
    }

    // mamv : RETORNA MODELO DE REPRESENTANTE MEDICO por ID BAGO
    protected function getRM( $bago_id )
    {
        return Personal::where( 'bago_id' , $bago_id )->whereHas( 'user' , function( $query )
        {
            $query->where( 'type' , REP_MED );
        })->first();
    }

    protected function getResponsible()
    {
        $user = Auth::user();
        $personals = Personal::orderBy( 'nombres' , 'ASC' , 'apellidos' , 'ASC' );
        if( $user->type === REP_MED )
        {
            $personals->where( 'user_id' , $user->id );
        }
        elseif( $user->type === SUP )
        {
            $personals->where( 'user_id' , $user->id )->orWhere( 'referencia_id' , $user->sup->bago_id );
        }
        elseif( in_array( $user->type , [ GER_PROD , GER_PROM , GER_COM , GER_GER ] ) )
        {
            $personals->whereHas( 'user' , function( $query )
            {
                $query->whereIn( 'type' , [ REP_MED , SUP ] );
            });
        }
        return $personals->get();
    }

    
     protected static function getResponsibleFN(){
        
        $userType = Auth::user()->type;
        $userId = Auth::user()->id;
        
        $rep = DB::select( 
            'SELECT FN_GET_RESPONSIBLE(:userType, :userId) from dual',
            [ 'userType' =>$userType, 
            'userId' =>$userId  ] );

        
       $repFinal = json_decode(json_encode($rep),true);
        return $repFinal;
    }

   

    public function employees()
    {
        return $this->hasMany( 'Users\Personal' , 'referencia_id' , 'bago_id' );
    }

    // mamv : RETORNA MODELO DE REPRESENTANTE SUPERVISOR por ID BAGO
    protected function getSupvervisor( $bago_id )
    {
        $persona = Personal::where( 'bago_id' , $bago_id)->where( 'tipo' , SUP )->first();
        return $persona;
    }

    // idkc : SOLO RM
    protected function rmSup()
    {
        return $this->belongsTo( 'Users\Personal' , 'referencia_id' , 'bago_id' )->whereHas( 'user' , function( $query )
        {
            $query->where( 'type' , SUP );
        });
    }

    public function getAccount()
    {
        if( $this->tipo === 'RM' || $this->tipo === 'RI' )
        {
            if( isset( $this->bagoVisitador->cuenta->cuenta ) )
            {
                return $this->bagoVisitador->cuenta->cuenta;
            }
        }
        elseif( $this->tipo === SUP )
        {
            if( isset( $this->bagoSupervisor->cuenta->cuenta ) )
            {
                return $this->bagoSupervisor->cuenta->cuenta;
            }
        }
        return null;   
    }

    public function userSup()
    {
        if( $this->tipo === 'RM' || $this->tipo === 'RI' )
        {
            return $this->rmSup->user_id;
        }
        elseif( $this->tipo === SUP )
        {
            return $this->user_id;
        }
        else
        {
            return null;
        }
    }

    // idkc : SOLO SUPERVISOR
    public function reps()
    {
        return $this->hasMany( 'Users\Personal' , 'referencia_id' , 'bago_id' )->whereHas( 'user' , function( $query )
        {
            $query->where( 'type' , REP_MED );
        });
    }

    public function getType()
    {
        return $this->hasOne('Users\PersonalType', 'id', 'tipo_personal_id');

    }
    // idkc : SOLO GERENTE DE PRODUCTO
    public function solicituds()
    {
        return $this->hasMany('Dmkt\SolicitudGer' , 'id_gerprod' , 'bago_id');
    }

    protected static function getGerProd( $bagoIds )
    {
        return Personal::whereIn( 'bago_id' , $bagoIds )->where( 'tipo' , GER_PROD )->get();
    }

    protected static function getGerProdNotRegisteredName( $uniqueIdsGerProd )
    {
        return Personal::whereIn( 'bago_id' , $uniqueIdsGerProd )->whereNull( 'user_id' )->get()->lists( 'full_name' );
    }

    public function bagoVisitador()
    {
        return $this->hasOne( 'Users\Visitador' , 'visvisitador' , 'bago_id' );
    }

    public function bagoSupervisor()
    {
        return $this->hasOne( 'Users\Supervisor' , 'supsupervisor' , 'bago_id' );
    }

    public function user()
    {
        return $this->belongsTo( 'User' , 'user_id' );
    }

    public static function getResponsibleUsers( $name )
    {
        $data = Personal::select( [ 'UPPER( NOMBRES || \' \' || APELLIDOS ) label' , 'USER_ID value' , '( SELECT TYPE FROM ' . TB_USUARIOS . ' WHERE ID = USER_ID ) TYPE' ] )
               ->whereRaw( 'UPPER( NOMBRES || \' \' || APELLIDOS ) like q\'[%' . mb_strtoupper( $name ) . '%]\' ' )
               ->whereHas( 'user' , function( $q )
               {
                   $q->whereIn( 'type' , [ REP_MED , SUP ] );
               })
               ->get();
        return $data;
    }

    public static function viewSupRep()
    {
        try
        {
            $supRepData = [];
            $supRepData =   
                Personal::select( [ 'UPPER( ' . TB_PERSONAL . '.APELLIDOS || \' \' || ' . TB_PERSONAL . '.NOMBRES ) VISITADOR' , 'UPPER( B.APELLIDOS || \' \' || B.NOMBRES ) SUPERVISOR' ] )
                    ->join( TB_PERSONAL . ' B' , 'B.BAGO_ID' , '=' , TB_PERSONAL . '.REFERENCIA_ID' )
                    ->whereIn( TB_PERSONAL . '.tipo' , [ 'RM' , 'RI' , 'RF' ] )
                    ->where( 'b.tipo' , 'S' )
                    ->orderBy( 'b.apellidos' , 'ASC' )
                    ->orderBy( TB_PERSONAL . '.apellidos' , 'ASC' )
                    ->get();
            $rpta = [ 'Status' => 'Ok' ];
        }
        catch( Exception $e )
        {
            Log::error( $e );
            $rpta = [ 'Status' => 'Error' , 'Description' => $e->getMessage() ];
        }
        finally
        {
            $rpta[ 'Data' ] = $supRepData;
            return $rpta; 
        }
    }

    public static function updateSupRep()
    {
        try
        {
            $supRepTable = [];
            $supIds = Personal::select( 'bago_id' )
                        ->where( 'tipo' , 'S' )
                        ->lists( 'bago_id' );
            $repIds = Personal::select( 'bago_id' )
                        ->whereIn( 'tipo' , [ 'RM' , 'RI' , 'RF' ] )
                        ->lists( 'bago_id' );

            $supRepTable    =    
                DB::table( 'FICPE.VISITADOR A' )
                    ->select( [ 'a.visvisitador' , 'c.supsupervisor' , 'a.vispaterno || \' \' || a.vismaterno || \' \' || a.visnombre visitador' , 'c.suppaterno || \' \' || c.supmaterno || \' \' || c.supnombre supervisor' ] )
                    ->join( 'FICPE.LINSUPVIS B' , 'B.LSVVISITADOR' , '=' , 'A.VISVISITADOR' )
                    ->join( 'FICPE.SUPERVISOR C' , 'C.SUPSUPERVISOR' , '=' , 'B.LSVSUPERVISOR' )
                    ->where( 'a.visactivo' , 'S' )
                    ->where( 'c.supactivo' , 'S' )
                    ->whereIn( 'a.visvisitador' , $repIds )
                    ->whereIn( 'c.supsupervisor' , $supIds )
                    ->orderBy( 'c.supsupervisor' , 'DESC' )
                    ->get();

            foreach( $supRepTable as $supRepRow )
            {
                $personalRow = Personal::where( 'bago_id' , $supRepRow->visvisitador )->whereIn( 'tipo' , [ 'RM' , 'RI' , 'RF' ] )->get();
                
                if( $personalRow->count() == 1 )
                {
                    if( $personalRow[ 0 ]->referencia_id == $supRepRow->supsupervisor )
                    {
                        $supRepRow->status = -1;
                    }
                    else
                    {
                        $personalRow = $personalRow[ 0 ];
                        $personalRow->referencia_id = $supRepRow->supsupervisor;
                        $personalRow->referencia_tipo = 'S';
                        $personalRow->save();   
                        $supRepRow->status = 1;
                    }
                }
                else
                {
                    $supRepRow->status = $personalRow->count();
                }     
            }
            
            $rpta = [ 'Status' => 'Ok' , 'Description' => 'Actualizado correctamente' ];
        }
        catch( Exception $e )
        {
            Log::error( $e );
            $rpta = [ 'Status' => 'Error' , 'Description' => $e->getMessage() ];
        }
        finally
        {
            $rpta[ 'Data' ] = $supRepTable;
            return $rpta;
        }
    }

    public static function getBagoSup( $code )
    {
        return Personal::where( 'tipo' , SUP )->where( 'bago_id' , $code )->first(); 
    }
}