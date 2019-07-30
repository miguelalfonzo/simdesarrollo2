<?php

namespace Dmkt;

use \Eloquent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use \Auth;
use Illuminate\Database\Eloquent\Collection;

class InvestmentType extends Eloquent
{

    use SoftDeletingTrait;

    protected $table = TB_TIPO_INVERSION;
    protected $primaryKey = 'id';

    public function nextId()
    {
        $nextId = InvestmentType::withTrashed()->select('id')->orderBy( 'id' , 'desc' )->first();
        if ( is_null( $nextId ) )
            return 1;
        else
            return $nextId->id + 1;
    }

    protected static function order()
    {
    	return InvestmentType::orderBy('nombre','asc')->get();
    }

    protected static function orderWithTrashed()
    {
        return InvestmentType::orderBy( 'updated_at' , 'desc' )->withTrashed()->get();
    }

    public function investmentActivity()
    {
    	return $this->hasMany('Dmkt\InvestmentActivity' , 'id_inversion' , 'id' );
    }

    // protected static function orderMkt()
    // {
    //     $investments = InvestmentType::orderBy('nombre','asc');
    //     if( Auth::user()->username == 'HOLISTIC' )
    //     {
    //         $investments->whereIn( 'codigo_actividad' , [ INVERSION_MKT , INVERSION_PROV ] ); 
    //     }
    //     else
    //     {
    //         $investments->where( 'codigo_actividad' , INVERSION_MKT );
    //     }
    //     return $investments->get();
    //}

    protected static function orderMktSP(){
        $usuario = Auth::user()->username;
        $row = \DB::transaction(function($conn) use ($usuario){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_LISTAR_INVERSIONES(:usuario, :data); END;');
                $stmt->bindParam(':usuario', $usuario, \PDO::PARAM_STR);
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);
    }


    protected static function get_inversion_cliente($clientType){
        $Tipoclient=$clientType;

        $row = \DB::transaction(function($conn) use ($Tipoclient){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_GET_INVERSION_CLIENTE(:Tipoclient,:data); END;');
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


    protected static function orderInst()
    {
        return InvestmentType::orderBy('nombre','asc')->where( 'codigo_actividad' , INVERSION_INSTITUCIONAL )->get();
    }

    protected function accountFund()
    {
        return $this->hasOne( '\Fondo\Fondo' , 'id' , 'id_fondo_contable' );
    }

    protected function approvalInstance()
    {
        return $this->belongsTo( '\Policy\ApprovalInstanceType' , 'id_tipo_instancia_aprobacion' );
    }

    public function getAddData()
    {
        return $this->select( [ 'id' , 'nombre descripcion' ] )->get();
    }
}